<?php

namespace LaravelEnso\DataTable\app\Classes;

use LaravelEnso\DataTable\app\Classes\Abstracts\TableEditorConfig;

class TableEditor
{
    private $model;
    private $rowId;
    private $property;
    private $value;
    private $validations;
    private $response;

    public function __construct(TableEditorConfig $config)
    {
        $this->validations = $config->getEditableValidations();

        $this->model = $config->editableModel;
        $this->rowId = $this->getEditableModelId();
        $this->property = $this->getEditableProperty();
        $this->value = $this->getNewValue();
    }

    public function setData()
    {
        if (!$this->checkValidationRules()) {
            return $this;
        }

        $model = $this->model::find($this->rowId);
        $model->{$this->property} = $this->value;
        $model->save();

        $this->response = [

            'data'    => [$this->property => $this->value],
            'message' => __('Operation was successfull'),
        ];

        return $this;
    }

    private function getEditableModelId()
    {
        return key(request()['data']); //fixme
    }

    private function getEditableProperty()
    {
        $key = key(request()['data'][$this->rowId]); //fixme

        if (is_array(request()['data'][$this->rowId][$key])) {
            return key(request()['data'][$this->rowId][$key]);
        }

        return $key;
    }

    private function getNewValue()
    {
        $key = key(request()['data'][$this->rowId]); //fixme

        if (is_array(request()['data'][$this->rowId][$key])) {
            return request()['data'][$this->rowId][$key][$this->property];
        }

        return request()['data'][$this->rowId][$this->property];
    }

    public function checkValidationRules()
    {
        if (!$this->validations) {
            return true;
        }

        $validator = \Validator::make([$this->property => $this->value], $this->validations);

        if ($validator->fails()) {
            $this->response = [

                'error' => $validator->errors()->first(),
            ];

            return false;
        }

        return true;
    }

    public function getResponse()
    {
        return response()->json($this->response);
    }
}
