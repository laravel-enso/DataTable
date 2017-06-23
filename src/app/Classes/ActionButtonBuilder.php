<?php

namespace LaravelEnso\DataTable\app\Classes;

use LaravelEnso\DataTable\app\Enums\ActionButtons;

class ActionButtonBuilder
{
    private $route;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->run();
    }

    public function getData()
    {
        return $this->data['actionButtons'];
    }

    private function run()
    {
        $this->setRoute()
            ->setStandardActionButtons()
            ->setCustomActionButtons();
    }

    private function setRoute()
    {
        $route = explode('.', request()->route()->getName());
        array_pop($route);
        $this->route = implode('.', $route);

        return $this;
    }

    private function setStandardActionButtons()
    {
        $label = $this->data['actionButtons'];
        $this->data['actionButtons'] = [];
        $this->data['actionButtons']['label'] = $label;
        $this->data['actionButtons']['standard'] = (new ActionButtons($this->route))->getData();

        return $this;
    }

    private function setCustomActionButtons()
    {
        $this->data['actionButtons']['custom'] = [];

        if (!isset($this->data['customActionButtons'])) {
            return $this;
        }

        foreach ($this->data['customActionButtons'] as $customButton) {
            if (isset($customButton['route']) && !request()->user()->can('access-route', $customButton['route'])) {
                continue;
            }

            $this->data['actionButtons']['custom'][] = $customButton;
        }

        unset($this->data['customActionButtons']);

        return $this;
    }
}
