<?php

namespace LaravelEnso\DataTable\app\Traits;

use LaravelEnso\DataTable\app\Classes\QueryBuilder;
use LaravelEnso\DataTable\app\Classes\TableBuilder;
use LaravelEnso\DataTable\app\Classes\TableInit;

trait DataTable
{
    public function initTable()
    {
        $structure = new $this->tableStructureClass();
        $table = new TableInit($structure);

        return $table->getResponse();
    }

    public function getTableData()
    {
        $builder = new TableBuilder(new $this->tableStructureClass(), new QueryBuilder($this->getTableQuery()));

        return $builder->getResponse();
    }
}
