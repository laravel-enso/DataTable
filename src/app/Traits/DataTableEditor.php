<?php

namespace LaravelEnso\DataTable\app\Traits;

use LaravelEnso\DataTable\app\Classes\TableEditor;

trait DataTableEditor
{
    public function setTableData()
    {
        $validationClass = isset($this->validationClass)
            ? $this->validationClass
            : null;

        return (new TableEditor(
            $this->editableModel,
            $validationClass,
            request()->get('data')
        ))->getResponse();
    }
}
