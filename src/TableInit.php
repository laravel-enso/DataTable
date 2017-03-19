<?php

namespace LaravelEnso\DataTable;

use LaravelEnso\DataTable\Abstracts\TableStructure;

class TableInit
{
    private $structure;

    public function __construct(TableStructure $tableStructure)
    {
        $this->structure = $tableStructure->data;

        $this->computeHeader()
             ->computeResponsivePriority()
             ->computeNotSearchable()
             ->computeNotSortable()
             ->computeEditable()
             ->computeCrtNo()
             ->computeStyling();
    }

    private function computeHeader()
    {
        $this->structure['header'] = array_column($this->structure['columns'], 'label');

        foreach ($this->structure['columns'] as &$value) {
            unset($value['label']);
        }

        return $this;
    }

    private function computeResponsivePriority()
    {
        if (!isset($this->structure['responsivePriority']) || !is_array($this->structure['responsivePriority'])) {
            return $this;
        }

        $responsivePriority = $this->structure['responsivePriority'];

        $priority = 1;

        foreach ($responsivePriority as $column) {
            $this->structure['columns'][$column]['responsivePriority'] = $priority;

            $priority++;
        }

        unset($this->structure['responsivePriority']);

        return $this;
    }

    private function computeNotSearchable()
    {
        if (!isset($this->structure['notSearchable']) || !is_array($this->structure['notSearchable'])) {
            return $this;
        }

        $notSearchableArray = $this->structure['notSearchable'];

        foreach ($notSearchableArray as $column) {
            $this->structure['columns'][$column]['searchable'] = false;
        }

        unset($this->structure['notSearchable']);

        return $this;
    }

    private function computeNotSortable()
    {
        if (!isset($this->structure['notSortable']) || !is_array($this->structure['notSortable'])) {
            return $this;
        }

        $notSortableArray = $this->structure['notSortable'];

        foreach ($notSortableArray as $column) {
            $this->structure['columns'][$column]['sortable'] = false;
        }
        unset($this->structure['notSortable']);

        return $this;
    }

    private function computeEditable()
    {
        if (!isset($this->structure['editable']) || !is_array($this->structure['editable'])) {
            return $this;
        }

        $editableArray = $this->structure['editable'];

        foreach ($editableArray as $column) {
            $this->structure['columns'][$column]['class'] = (isset($this->structure['columns'][$column]['class']) ? $this->structure['columns'][$column]['class'] : '').' editable';
            $this->structure['columns'][$column]['editField'] = $this->structure['columns'][$column]['name'];
        }

        return $this;
    }

    private function computeCrtNo()
    {
        if (!isset($this->structure['crtNo'])) {
            return $this;
        }

        if (isset($this->structure['totals'])) {
            $tmp = array_flip($this->structure['totals']);
            $tmp = $this->incrementColumnsKeys($tmp);

            $this->structure['totals'] = array_flip($tmp);
        }

        if (isset($this->structure['render'])) {
            $this->structure['render'] = $this->incrementColumnsKeys($this->structure['render']);
        }

        return $this;
    }

    private function incrementColumnsKeys($array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = $value + 1;
        }

        return $array;
    }

    private function computeStyling()
    {
        if (!isset($this->structure['headerAlign']) && (!isset($this->structure['bodyAlign']))) {
            return false;
        }

        $class = isset($this->structure['headerAlign']) ? ' dt-head-'.$this->structure['headerAlign'] : '';
        $class .= isset($this->structure['bodyAlign']) ? ' dt-body-'.$this->structure['bodyAlign'] : '';

        foreach (array_keys($this->structure['columns']) as $key) {
            $this->structure['columns'][$key]['class'] = (isset($this->structure['columns'][$key]['class']) ? $this->structure['columns'][$key]['class'] : '').$class;
        }

        return $this;
    }

    public function getResponse()
    {
        return $this->structure;
    }
}
