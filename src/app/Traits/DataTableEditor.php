<?php

namespace LaravelEnso\DataTable\app\Traits;

use LaravelEnso\DataTable\app\Classes\TableEditor;

trait DataTableEditor
{
    public function setTableData()
    {
        return (new TableEditor(
            $this->editableModel,
            $this->validationClass,
            request()->get('data')
        ))->getResponse();
    }
}
