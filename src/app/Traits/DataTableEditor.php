<?php

namespace LaravelEnso\DataTable\app\Traits;

use LaravelEnso\DataTable\app\Classes\TableEditor;

trait DataTableEditor
{
    public function setTableData()
    {
        $config = new $this->tableEditorConfigClass();
        $editor = new TableEditor($config);
        $editor->setData();

        return $editor->getResponse();
    }
}
