<?php

namespace LaravelEnso\DataTable\app\Classes;

use LaravelEnso\Helpers\Classes\AbstractObject;

abstract class TableStructure extends AbstractObject
{
    protected $data;

    public function getData()
    {
        return $this->data;
    }
}
