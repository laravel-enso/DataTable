<?php

namespace LaravelEnso\DataTable\App\Classes\Abstracts;

use LaravelEnso\Helpers\Classes\AbstractObject;

abstract class TableEditorConfig extends AbstractObject
{
    public $editableModel;

    public function getEditableValidations()
    {
    }
}
