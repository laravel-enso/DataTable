<?php

namespace LaravelEnso\DataTable\app\Classes;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class QueryBuilder
{
    private $query;
    private $params;
    private $totalRecords;
    private $filteredRecords;
    private $totals;
    private $data;

    public function __construct(Builder $query, array $params)
    {
        $this->query = $query;
        $this->params = $params;
        $this->run();
    }

    public function getTotalRecords()
    {
        return $this->totalRecords;
    }

    public function getFilteredRecords()
    {
        return $this->filteredRecords;
    }

    public function getTotals()
    {
        return $this->totals;
    }

    public function getData()
    {
        return $this->data;
    }

    private function run()
    {
        $this->totalRecords = $this->query->count();

        $this->applyFilters()
            ->applyExtraFilters()
            ->applyIntervalFilters()
            ->applySortOrder();

        $this->filteredRecords = $this->hasFilters() ? $this->query->count() : $this->totalRecords;

        $this->setTotals()
            ->applyLimit();

        $this->data = $this->query->get();
    }

    private function applyFilters()
    {
        if (!$this->params['search']['value']) {
            return $this;
        }

        $arguments = collect(explode(' ', $this->params['search']['value']));

        $arguments->each(function ($argument) {
            $this->query->where(function ($query) use ($argument) {
                foreach ($this->params['columns'] as $column) {
                    if ($column['searchable'] == 'true') {
                        $query->orWhere($column['name'], 'LIKE', '%' . $argument . '%');
                    }
                }
            });
        });

        return $this;
    }

    private function applyExtraFilters()
    {
        $extraFilters = json_decode($this->params['extraFilters']);

        if (empty((array) $extraFilters)) {
            return $this;
        }

        $this->query->where(function ($query) use ($extraFilters) {
            foreach ($extraFilters as $table => $values) {
                foreach ($values as $column => $value) {
                    if ($value !== null && $value !== '') {
                        $query->where($table . '.' . $column, '=', $value);
                    }
                }
            }
        });

        return $this;
    }

    private function applyIntervalFilters()
    {
        $intervalFilters = json_decode($this->params['intervalFilters']);

        if (empty((array) $intervalFilters)) {
            return $this;
        }

        $this->query->where(function () use ($intervalFilters) {
            foreach ($intervalFilters as $table => $intervalObject) {
                foreach ($intervalObject as $column => $value) {
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

        $min = isset($value->dbDateFormat) ? $this->getFormattedDate($value->min, $value->dbDateFormat) : (int) $value->min;
        $this->query->where($table . '.' . $column, '>=', $min);

        return $this;
    }

    private function setMaxLimit($table, $column, $value)
    {
        if (!$value->max) {
            return $this;
        }

        $max = isset($value->dbDateFormat) ? $this->getFormattedDate($value->max, $value->dbDateFormat) : (int) $value->max;
        $this->query->where($table . '.' . $column, '<=', $max);

        return $this;
    }

    private function getFormattedDate(String $date, String $dbDateFormat)
    {
        $date = new Carbon($date);

        return $date->format($dbDateFormat);
    }

    private function applySortOrder()
    {
        if (empty($this->params['order'])) {
            return $this;
        }

        foreach ($this->params['order'] as $order) {
            $this->query->orderBy($this->params['columns'][$order['column']]['name'], $order['dir']);
        }

        return $this;
    }

    private function setTotals()
    {
        $totals = json_decode($this->params['totals']);

        if (empty((array) $totals)) {
            return $this;
        }

        $this->totals = [];

        foreach ($totals as $key => $column) {
            $this->totals[$key] = $this->query->sum($column);
        }

        return $this;
    }

    private function applyLimit()
    {
        $this->query->skip($this->params['start'])->take($this->params['length']);

        return $this->query;
    }

    private function hasFilters()
    {
        return $this->params['search']['value']
        || $this->params['extraFilters']
        || $this->params['intervalFilters'];
    }
}
