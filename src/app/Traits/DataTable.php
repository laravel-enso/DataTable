<?php

namespace LaravelEnso\DataTable\app\Traits;

use LaravelEnso\DataTable\app\Classes\QueryBuilder;
use LaravelEnso\DataTable\app\Classes\TableBuilder;
use LaravelEnso\DataTable\app\Classes\TableInit;

trait DataTable
{
    public function initTable()
    {
        $init = new TableInit(new $this->tableStructureClass());

        return $init->getData();
    }

    public function getTableData()
    {
        $builder = new TableBuilder(new $this->tableStructureClass(), new QueryBuilder($this->getTableQuery()));

        return $builder->getTableData();
    }
}
