<?php

namespace LaravelEnso\DataTable\Traits;

use LaravelEnso\DataTable\TableEditor;

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
