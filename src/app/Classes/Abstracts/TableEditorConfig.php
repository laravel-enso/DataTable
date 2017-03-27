<?php

namespace LaravelEnso\DataTable\app\Classes\Abstracts;

use LaravelEnso\Helpers\Classes\AbstractObject;

abstract class TableEditorConfig extends AbstractObject
{
    public $editableModel;

    public function getEditableValidations()
    {
    }
}
