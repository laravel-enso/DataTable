<?php

namespace LaravelEnso\DataTable\app\Enums;

use LaravelEnso\Helpers\Classes\AbstractEnum;

class ActionButtons extends AbstractEnum
{
    public function __construct(String $route)
    {
        $this->data = [
            'show'     => request()->user()->can('access-route', $route.'.show') ?: false,
            'edit'     => request()->user()->can('access-route', $route.'.edit') ?: false,
            'delete'   => request()->user()->can('access-route', $route.'.destroy') ?: false,
            'download' => request()->user()->can('access-route', $route.'.download') ?: false,
        ];
    }
}
