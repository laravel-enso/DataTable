<?php

namespace LaravelEnso\DataTable\app\Classes;

use LaravelEnso\DataTable\app\Classes\TableStructure;

class TableBuilder
{
    private $structure;
    private $queryBuilder;
    private $data;

    public function __construct(TableStructure $tableStructure, QueryBuilder $queryBuilder)
    {
        $this->structure = $tableStructure->getData();
        $this->queryBuilder = $queryBuilder;
        $this->data = $queryBuilder->getData();
        $this->computeEnums();
    }

    public function getTableData()
    {
        return [
            'draw'            => request('draw'),
            'recordsTotal'    => $this->queryBuilder->getTotalRecords(),
            'recordsFiltered' => $this->queryBuilder->getFilteredRecords(),
            'totals'          => $this->queryBuilder->getTotals(),
            'data'            => $this->data,
        ];
    }

    private function computeEnums()
    {
        foreach ($this->data as &$value) {
            foreach ($this->getDataFromEnums() as $key => $data) {
                $value->$key = $data[$value->$key];
            }
        }

        return $this;
    }

    private function getDataFromEnums()
    {
        $dataFromEnum = [];

        if (!isset($this->structure['enumMappings'])) {
            return $dataFromEnum;
        }

        foreach ($this->structure['enumMappings'] as $column => $enumClass) {
            $enum = new $enumClass();
            $dataFromEnum[$column] = $enum->getData();
        }

        return $dataFromEnum;
    }
}
