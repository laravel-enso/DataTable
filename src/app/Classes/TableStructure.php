<?php

namespace LaravelEnso\DataTable\app\Classes;

abstract class TableStructure
{
    protected $data;

    public function getData()
    {
        return $this->data;
    }
}
