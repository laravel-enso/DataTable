<?php

namespace LaravelEnso\DataTable\app\Classes;

use Illuminate\Database\Eloquent\Builder;
use LaravelEnso\Helpers\Classes\Object;

class TableBuilder
{
    private $structure;
    private $queryBuilder;
    private $data;
    private $params;

    public function __construct(TableStructure $tableStructure, Builder $query, array $params)
    {
        $this->structure = $tableStructure->getData();
        $this->params = $params;
        $this->queryBuilder = new QueryBuilder($query, $this->params);
        $this->data = $this->queryBuilder->getData();
        $this->setAppends();
        $this->computeEnums();
    }

    public function getTableData()
    {
        return [
            'draw'            => $this->params['draw'],
            'recordsTotal'    => $this->queryBuilder->getTotalRecords(),
            'recordsFiltered' => $this->queryBuilder->getFilteredRecords(),
            'totals'          => $this->queryBuilder->getTotals(),
            'data'            => $this->data,
        ];
    }

    public function getExcelData()
    {
        return new Object([
            'header'          => $this->structure['columns'],
            'recordsTotal'    => $this->queryBuilder->getTotalRecords(),
            'recordsFiltered' => $this->queryBuilder->getFilteredRecords(),
            'totals'          => $this->queryBuilder->getTotals(),
            'records'         => $this->data,
        ]);
    }

    private function setAppends()
    {
        $appends = isset($this->structure['appends']) ? $this->structure['appends'] : [];
        $this->data->each->setAppends($appends);
    }

    private function computeEnums()
    {
        if (!isset($this->structure['enumMappings'])) {
            return false;
        }

        (new EnumComputor($this->data, $this->structure['enumMappings']))->run();
    }
}
