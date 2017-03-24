<?php

namespace LaravelEnso\DataTable\App\Traits;

use LaravelEnso\DataTable\App\Classes\TableEditor;

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
