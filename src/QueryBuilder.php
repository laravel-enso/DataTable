<?php

namespace LaravelEnso\DataTable;

class QueryBuilder
{
    public $query;
    public $recordsTotal;
    public $recordsFiltered;
    public $totals;
    public $data;

    public function __construct($queryBuilder)
    {
        $this->query = $queryBuilder;
        $this->recordsTotal = $this->query->count();

        $this->applyFilters()
             ->applyExtraFilters()
             ->applyIntervalFilters()
             ->applySortOrder();

        $this->recordsFiltered = $this->query->count();
        $this->totals = $this->getTotals();
        $this->applyLimit();
        $this->data = $this->query->get();
    }

    private function applyFilters()
    {
        if (request()->search['value']) {
            $search = explode(' ', request()->search['value']);

            foreach ($search as $argument) {
                $this->query->where(function () use ($argument) {
                    foreach (request()->columns as $column) {
                        if ($column['searchable'] == 'true') {
                            $this->query->orWhere($column['name'], 'LIKE', '%'.$argument.'%');
                        }
                    }
                });
            }
        }

        return $this;
    }

    private function applyExtraFilters()
    {
        if (!request()->has('extraFilters')) {
            return $this;
        }

        $this->query->where(function () {
            foreach ((array) json_decode(request()->extraFilters) as $table => $values) {
                foreach ((array) $values as $column => $value) {
                    if (!is_object($value) && $value != null && $value != '') {
                        $this->query->where($table.'.'.$column, '=', $value);
                    }
                }
            }
        });

        return $this;
    }

    private function applyIntervalFilters()
    {
        if (!request()->has('intervalFilters')) {
            return $this;
        }

        $this->query->where(function () {
            foreach ((array) json_decode(request()->intervalFilters) as $table => $intervalObject) {
                foreach ((array) $intervalObject as $column => $value) {
                    $this->setMinLimit($table, $column, $value)
                         ->setMaxLimit($table, $column, $value);
                }
            }
        });

        return $this;
    }

    private function setMinLimit($table, $column, $value)
    {
        if (!$value->min) {
            return $this;
        }

        $min = isset($value->dbDateFormat) ? $this->getFormattedDate($value->min, $value->dbDateFormat) : $value->min;

        $this->query->where($table.'.'.$column, '>=', $min);

        return $this;
    }

    private function setMaxLimit($table, $column, $value)
    {
        if (!$value->max) {
            return $this;
        }

        $max = isset($value->dbDateFormat) ? $this->getFormattedDate($value->max, $value->dbDateFormat) : $value->max;

        $this->query->where($table.'.'.$column, '<=', $max);

        return $this;
    }

    private function getFormattedDate(String $date, String $dbDateFormat)
    {
        $date = new \Date($date);

        return $date->format($dbDateFormat);
    }

    public function applySortOrder()
    {
        if (!empty(request()->order)) {
            foreach (request()->order as $order) {
                $this->query->orderBy(request()->columns[$order['column']]['name'], $order['dir']);
            }
        }

        return $this;
    }

    public function getTotals()
    {
        if (!request()->totals) {
            return false;
        }

        $totals = [];

        foreach (request()->totals as $key => $value) {
            $totals[$key] = $this->query->sum($value);
        }

        return $totals;
    }

    private function applyLimit()
    {
        $this->query->skip(request()->start)->take(request()->length);

        return $this;
    }
}
