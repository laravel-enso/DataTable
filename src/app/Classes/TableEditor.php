<?php

namespace LaravelEnso\DataTable\app\Classes;

use Illuminate\Validation\Rules\Unique;

class TableEditor
{
    private $model;
    private $params;
    private $table;
    private $validationClass;
    private $modelId;
    private $property;
    private $value;
    private $response;

    public function __construct(string $model, string $validationClass, array $params)
    {
        $this->model = $model;
        $this->params = $params;
        $this->validationClass = $validationClass;
        $this->setModelId()
            ->setTable()
            ->setProperty()
            ->setValue();
        $this->updateModel();
    }

    public function getResponse()
    {
        return $this->response;
    }

    private function setModelId()
    {
        $this->modelId = key($this->params);

        return $this;
    }

    private function setTable()
    {
        $this->table = key($this->params[$this->modelId]);

        if (!is_array($this->params[$this->modelId][$this->table])) {
            throw new \EnsoException(__("You need to define in the 'TableStructure' class,
                the 'name' attribute for the editable column with the form of 'table.column'"));
        }

        return $this;
    }

    private function setProperty()
    {
        $this->property = key($this->params[$this->modelId][$this->table]);

        if (!in_array($this->property, (new $this->model())->getFillable())) {
            throw new \EnsoException(__('Seems like the')." '".$this->property."' ".__("property defined in the 'TableStructure' class under
                the 'name' attribute for the editable column, does not exist in the fillable array for the")." '".$this->model."' ".__('model'));
        }

        return $this;
    }

    private function setValue()
    {
        $this->value = $this->params[$this->modelId][$this->table][$this->property];
    }

    private function updateModel()
    {
        $this->validateTable()
            ->validateValue();

        $model = $this->model::find($this->modelId);
        $model->{$this->property} = $this->value;
        $model->save();

        $this->setSuccessResponse();
    }

    private function validateTable()
    {
        $modelTable = (new $this->model())->getTable();

        if ($this->table !== $modelTable) {
            throw new \EnsoException(__('The model')." '".$this->model."' ".__('has')." '".$modelTable
                ."' ".__('as default table instead of the given')." '".$this->table."' "
                .__("defined in the 'name' attribute from the 'TableStructure' class"));
        }

        return $this;
    }

    private function validateValue()
    {
        $rules = $this->validationClass ? (new $this->validationClass())->rules()[$this->property] : '';

        if (is_array($rules)) {
            $rules = $this->excludeModelIdFromUnique($rules);
        }

        $validator = \Validator::make([$this->property => $this->value], [$this->property => $rules]);

        if ($validator->fails()) {
            throw new \EnsoException($this->getValidatorErrors($validator));
        }
    }

    private function excludeModelIdFromUnique(array $rules)
    {
        foreach ($rules as &$rule) {
            if (is_object($rule) && ($rule instanceof Unique)) {
                $rule = $rule->ignore($this->modelId);
            }
        }

        return $rules;
    }

    private function getValidatorErrors($validator)
    {
        $message = __('The form contains errors');

        foreach ($validator->errors()->get($this->property) as $error) {
            $message .= '<br>'.$error;
        }

        return $message;
    }

    private function setSuccessResponse()
    {
        $this->response = [
            'data'    => [$this->property => $this->value],
            'message' => __(config('labels.successfulOperation')),
        ];
    }
}
