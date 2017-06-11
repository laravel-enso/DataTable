<?php

namespace LaravelEnso\DataTable\app\Traits;

use LaravelEnso\DataTable\app\Classes\TableEditor;

trait DataTableEditor
{
    public function setTableData()
    {
        $editor = new TableEditor($this->editableModel, $this->validationClass);

        return $editor->getResponse();
    }
}
