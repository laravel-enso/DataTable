<?php

namespace LaravelEnso\DataTable\app\Classes;

use LaravelEnso\Localisation\app\Models\Language;

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
            ->setLocales();

        unset($this->data['enumMappings']);
    }

    private function setHeader()
    {
        $this->data['header'] = [];

        foreach ($this->data['columns'] as &$value) {
            $this->data['header'][] = $value['label'];
            unset($value['label']);
        }

        return $this;
    }

    private function setResponsivePriority()
    {
        if (!isset($this->data['responsivePriority'])) {
            return $this;
        }

        $this->setSecondaryColumns();
        $this->setPrimaryColumns();
        unset($this->data['responsivePriority']);

        return $this;
    }

    private function setSecondaryColumns()
    {
        $priority = count($this->data['responsivePriority']) + 1;

        foreach ($this->data['columns'] as &$column) {
            $column['responsivePriority'] = $priority;
        }
    }

    private function setPrimaryColumns()
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

        $this->data['totals'] = collect($this->data['totals'])
            ->reduce(function ($totals, $column) {
                $totals[$column] = $this->data['columns'][$column]['name'];

                return $totals;
            });

        return $this;
    }

    private function setEditable()
    {
        if (!isset($this->data['editable'])) {
            return $this;
        }

        foreach ($this->data['editable'] as $key => $column) {
            $this->data['columns'][$column]['class'] = trim(
                (isset($this->data['columns'][$column]['class'])
                    ? $this->data['columns'][$column]['class']
                    : '').' editable'
            );

            $this->data['columns'][$column]['editField'] = $this->data['columns'][$column]['name'];

            $this->data['editable'][$key] = [
                'name' => $this->data['columns'][$column]['name'],
            ];
        }

        return $this;
    }

    private function computeCrtNo()
    {
        if (isset($this->data['crtNo'])) {
            $this->data = (new CrtNoComputor($this->data))->getData();
        }

        return $this;
    }

    private function setActionButtons()
    {
        $this->data = (new ActionButtonBuilder($this->data, $this->route))->getData();

        return $this;
    }

    private function setLocales()
    {
        Language::pluck('name')->each(function ($locale) {
            $langFile = \File::exists(resource_path('dt-lang/'.$locale.'.json'))
                ? json_decode(\File::get(resource_path('dt-lang/'.$locale.'.json')))
                : json_decode(\File::get(resource_path('dt-lang/'.config('app.fallback_locale').'.json')));

            $this->data['locales'][$locale] = $langFile;
        });
    }
}
