<?php

namespace LaravelEnso\DataTable\app\Classes;

class TableInit
{
    private $data;
    private $route;

    public function __construct(TableStructure $tableStructure, string $route)
    {
        $this->data = $tableStructure->getData();
        $this->route = $route;
        $this->run();
    }

    public function getData()
    {
        return $this->data;
    }

    private function run()
    {
        $this->setHeader()
            ->setResponsivePriority()
            ->setNotSearchable()
            ->setNotSortable()
            ->computeTotals()
            ->setEditable()
            ->computeCrtNo()
            ->setActionButtons()
            ->setLanguage();

        unset($this->data['enumMappings']);
    }

    private function setHeader()
    {
        $this->data['header'] = [];

        foreach ($this->data['columns'] as &$value) {
            $this->data['header'][] = array_shift($value);
        }

        return $this;
    }

    private function setResponsivePriority()
    {
        if (!isset($this->data['responsivePriority'])) {
            return $this;
        }

        $this->setSecondaryPriorityColumns();
        $this->setPrimaryPriorityColumns();
        unset($this->data['responsivePriority']);

        return $this;
    }

    private function setSecondaryPriorityColumns()
    {
        $priority = count($this->data['responsivePriority']) + 1;

        foreach ($this->data['columns'] as &$column) {
            $column['responsivePriority'] = $priority;
        }
    }

    private function setPrimaryPriorityColumns()
    {
        $priority = 1;

        foreach ($this->data['responsivePriority'] as &$column) {
            $this->data['columns'][$column]['responsivePriority'] = $priority++;
        }
    }

    private function setNotSearchable()
    {
        if (!isset($this->data['notSearchable'])) {
            return $this;
        }

        foreach ($this->data['notSearchable'] as $column) {
            $this->data['columns'][$column]['searchable'] = false;
        }

        unset($this->data['notSearchable']);

        return $this;
    }

    private function setNotSortable()
    {
        if (!isset($this->data['notSortable'])) {
            return $this;
        }

        foreach ($this->data['notSortable'] as $column) {
            $this->data['columns'][$column]['sortable'] = false;
        }

        unset($this->data['notSortable']);

        return $this;
    }

    private function computeTotals()
    {
        if (!isset($this->data['totals'])) {
            return $this;
        }

        $totals = [];

        foreach ($this->data['totals'] as $column) {
            $field = $this->data['columns'][$column]['name'];
            $totals[$column] = $field;
        }

        $this->data['totals'] = $totals;

        return $this;
    }

    private function setEditable()
    {
        if (!isset($this->data['editable'])) {
            return $this;
        }

        foreach ($this->data['editable'] as $key => $column) {
            $this->data['columns'][$column]['class'] = trim(
                (isset($this->data['columns'][$column]['class']) ? $this->data['columns'][$column]['class'] : '').' editable'
            );

            $this->data['columns'][$column]['editField'] = $this->data['columns'][$column]['name'];
            $this->data['editable'][$key] = ['name' => $this->data['columns'][$column]['name']];
        }

        return $this;
    }

    private function computeCrtNo()
    {
        if (!isset($this->data['crtNo'])) {
            return $this;
        }

        $this->data = (new CrtNoComputor($this->data))->getData();

        return $this;
    }

    private function setActionButtons()
    {
        $this->data = (new ActionButtonBuilder($this->data, $this->route))->getData();

        return $this;
    }

    private function setLanguage()
    {
        $this->data['language'] = \File::exists(resource_path('dt-lang/'.app()->getLocale().'.json'))
            ? json_decode(\File::get(resource_path('dt-lang/'.app()->getLocale().'.json')))
            : null;
    }
}
