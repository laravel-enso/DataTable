<?php

namespace LaravelEnso\DataTable\app\Enums;

use LaravelEnso\Helpers\Classes\AbstractEnum;

class ActionButtonsEnum extends AbstractEnum
{
    public function __construct(String $route)
    {
        $this->data = [

            'show'     => request()->user()->hasAccessTo($route.'.show') ?: false,
            'edit'     => request()->user()->hasAccessTo($route.'.edit') ?: false,
            'download' => request()->user()->hasAccessTo($route.'.download') ?: false,
            'delete'   => request()->user()->hasAccessTo($route.'.destroy') ?: false,
        ];
    }
}
