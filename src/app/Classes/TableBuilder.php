<?php

namespace LaravelEnso\DataTable\app\Classes;

use LaravelEnso\DataTable\app\Classes\Abstracts\TableStructure;
use LaravelEnso\DataTable\app\Enums\ActionButtonsEnum;

class TableBuilder
{
    private $structure;
    private $actionButtons;

    public function __construct(TableStructure $tableStructure, QueryBuilder $queryBuilder)
    {
        $this->structure = $tableStructure->data;
        $this->queryBuilder = $queryBuilder;

        $this->computeActionButtons()
             ->computeCustomActionButtons()
             ->computeEnums();
    }

    public function getResponse()
    {
        return [

            'draw'            => request()->draw,
            'recordsTotal'    => $this->queryBuilder->recordsTotal,
            'recordsFiltered' => $this->queryBuilder->recordsFiltered,
            'data'            => $this->queryBuilder->data,
            'totals'          => $this->queryBuilder->totals,
            'actionButtons'   => $this->actionButtons,
        ];
    }

    private function computeActionButtons()
    {
        $routeArray = explode('.', request()->route()->getName());
        array_pop($routeArray);
        $route = implode('.', $routeArray);
        $actionButtons = new ActionButtonsEnum($route);
        $this->actionButtons = $actionButtons->getData();

        return $this;
    }

    private function computeCustomActionButtons()
    {
        if (!isset($this->structure['customActionButtons'])) {
            return $this;
        }

        $this->actionButtons['custom'] = [];

        foreach ($this->structure['customActionButtons'] as $customButton) {
            if (isset($customButton['route']) && !request()->user()->hasAccessTo($customButton['route'])) {
                continue;
            }

            $this->actionButtons['custom'][] = $customButton;
        }

        return $this;
    }

    private function computeEnums()
    {
        $dataFromEnums = $this->getDataFromEnums();

        foreach ($this->queryBuilder->data as &$value) {
            foreach ($dataFromEnums as $key => $data) {
                $value->$key = $data[$value->$key];
            }
        }

        return $this;
    }

    private function getDataFromEnums()
    {
        $dataFromEnum = [];

        if (isset($this->structure['enumMappings'])) {
            foreach ($this->structure['enumMappings'] as $column => $enumClass) {
                $enum = new $enumClass();

                $dataFromEnum[$column] = $enum->getData();
            }
        }

        return $dataFromEnum;
    }
}
