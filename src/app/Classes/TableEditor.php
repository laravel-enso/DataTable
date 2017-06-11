<?php

namespace LaravelEnso\DataTable\app\Classes;

use LaravelEnso\DataTable\app\Classes\Abstracts\TableEditorConfig;
use LaravelEnso\Helpers\Classes\Object;

class TableEditor extends Object
{
    private $model;
    private $validationClass;
    private $modelId;
    private $property;
    private $value;
    private $response;

    public function __construct(string $model, string $validationClass)
    {
        $this->model = $model;
        $this->validationClass = $validationClass;
        $this->setModelId();
        $this->setProperty();
        $this->setValue();
        $this->updateModel();
    }

    public function getResponse()
    {
        return $this->response;
    }

    private function setModelId()
    {
        $this->modelId = key(request('data'));
    }

    private function setProperty()
    {
        $this->property =  key(request('data')[$this->modelId]);
    }

    private function setValue()
    {
        $this->value =  request('data')[$this->modelId][$this->property];
    }
    private function updateModel()
    {
        $this->validate();

        $model = $this->model::find($this->modelId);
        $model->{$this->property} = $this->value;
        $model->save();

        $this->setSuccessResponse();
    }

    private function validate()
    {
        $rules = (new $this->validationClass)->rules()[$this->property];
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
