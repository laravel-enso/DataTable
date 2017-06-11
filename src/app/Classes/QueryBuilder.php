<?php

namespace LaravelEnso\DataTable\app\Classes;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class QueryBuilder
{
    private $query;
    private $totalRecords;
    private $filteredRecords;
    private $totals;
    private $data;

    public function __construct(Builder $query)
    {
        $this->query = $query;
        $this->build();
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

    private function build()
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
        if (!request('search')['value']) {
            return $this;
        }

        $arguments = collect(explode(' ', request()->search['value']));

        $arguments->each(function($argument) {
            $this->query->where(function ($query) use ($argument) {
                foreach (request('columns') as $column) {
                    if ($column['searchable'] == 'true') {
                        $query->orWhere($column['name'], 'LIKE', '%'.$argument.'%');
                    }
                }
            });
        });

        return $this;
    }

    private function applyExtraFilters()
    {
        $extraFilters = (array) json_decode(request('extraFilters'));

        if (!count($extraFilters)) {
            return $this;
        }

        $this->query->where(function ($query) use ($extraFilters) {
            foreach ($extraFilters as $table => $values) {
                foreach ((array) $values as $column => $value) {
                    if ($value !== null && $value !== '') {
                        $query->where($table.'.'.$column, '=', $value);
                    }
                }
            }
        });

        return $this;
    }

    private function applyIntervalFilters()
    {
        $intervalFilters = (array) json_decode(request('intervalFilters'));

        if (!count($intervalFilters)) {
            return $this;
        }

        $this->query->where(function () use ($intervalFilters) {
            foreach ($intervalFilters as $table => $intervalObject) {
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
        $date = new Carbon($date);

        return $date->format($dbDateFormat);
    }

    public function applySortOrder()
    {
        if (!empty(request('order'))) {
            foreach (request('order') as $order) {
                $this->query->orderBy(request()->columns[$order['column']]['name'], $order['dir']);
            }
        }

        return $this;
    }

    public function setTotals()
    {
        $totals = (array) json_decode(request('totals'));

        if (!count($totals)) {
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
        $this->query->skip(request()->start)->take(request()->length);

        return $this;
    }

    private function hasFilters()
    {
        return request('search')['value']
            || !count(request('extraFilters'))
            || !count(request('intervalFilters'));
    }
}
