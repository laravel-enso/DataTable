<?php

namespace LaravelEnso\DataTable\App\Traits;

use LaravelEnso\DataTable\App\Classes\QueryBuilder;
use LaravelEnso\DataTable\App\Classes\TableBuilder;
use LaravelEnso\DataTable\App\Classes\TableInit;

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
