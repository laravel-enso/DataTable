<?php

namespace LaravelEnso\DataTable\Traits;

use LaravelEnso\DataTable\QueryBuilder;
use LaravelEnso\DataTable\TableBuilder;
use LaravelEnso\DataTable\TableInit;

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
