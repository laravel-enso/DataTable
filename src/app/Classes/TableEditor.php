<?php

namespace LaravelEnso\DataTable\app\Classes;

use Illuminate\Validation\Rules\Unique;

class TableEditor
{
    private $model;
    private $table;
    private $validationClass;
    private $modelId;
    private $property;
    private $value;
    private $response;

    public function __construct(string $model, string $validationClass)
    {
        $this->model = $model;
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
        $this->modelId = key(request('data'));

        return $this;
    }

    private function setTable()
    {
        $this->table = key(request('data')[$this->modelId]);

        if (!is_array(request('data')[$this->modelId][$this->table])) {
            throw new \EnsoException(__("You need to define in the 'TableStructure' class,
                the 'name' attribute for the editable column with the form of 'table.column'"), 'warning');
        }

        return $this;
    }

    private function setProperty()
    {
        $this->property = key(request('data')[$this->modelId][$this->table]);

        if (!in_array($this->property, (new $this->model())->getFillable())) {
            throw new \EnsoException(__('Seems like the')." '".$this->property."' ".__("property defined in the 'TableStructure' class under
                the 'name' attribute for the editable column, does not exist in the fillable array for the")." '".$this->model."' ".__('model'), 'warning');
        }

        return $this;
    }

    private function setValue()
    {
        $this->value = request('data')[$this->modelId][$this->table][$this->property];
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
                .__("defined in the 'name' attribute from the 'TableStructure' class"), 'warning');
        }

        return $this;
    }

    private function validateValue()
    {
        $rules = (new $this->validationClass())->rules()[$this->property];

        foreach ($rules as &$rule) {
            if (is_object($rule) && ($rule instanceof Unique)) {
                $rule = $rule->ignore($this->modelId);
            }
        }

        $validator = \Validator::make([$this->property => $this->value], [$this->property => $rules]);

        if ($validator->fails()) {
            $message = $this->getValidatorErrors($validator);
            throw new \EnsoException($message, 'warning');
        }
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
            'message' => __('Operation was successfull'),
        ];
    }
}
