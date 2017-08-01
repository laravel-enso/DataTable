<?php

namespace LaravelEnso\DataTable\app\Classes;

use LaravelEnso\DataTable\app\Enums\ActionButtons;

class ActionButtonBuilder
{
    private $route;

    public function __construct(array $data, string $route)
    {
        $this->data = $data;
        $this->setRoute($route);
        $this->run();
    }

    public function getData()
    {
        return $this->data;
    }

    public function run()
    {
        $this->setStandardActionButtons()
            ->setCustomActionButtons()
            ->setCustomButtons();

        unset($this->data['customActionButtons']);
    }

    private function setRoute(string $route)
    {
        $route = explode('.', $route);
        array_pop($route);
        $this->route = implode('.', $route);

        return $this;
    }

    private function setStandardActionButtons()
    {
        if (!isset($this->data['actionButtons'])) {
            return $this;
        }

        $label = $this->data['actionButtons'];
        $this->data['actionButtons'] = [];
        $this->data['actionButtons']['label'] = $label;
        $this->data['actionButtons']['standard'] =
            (new ActionButtons($this->route))->getData();
        $this->data['actionButtons']['custom'] = [];

        return $this;
    }

    private function setCustomActionButtons()
    {
        if (!isset($this->data['customActionButtons'])) {
            return $this;
        }

        foreach ($this->data['customActionButtons'] as $customButton) {
            if (isset($customButton['route'])
                && !request()->user()->can('access-route', $customButton['route'])) {
                continue;
            }

            $this->data['actionButtons']['custom'][] = $customButton;
        }

        unset($this->data['customActionButtons']);

        return $this;
    }

    private function setCustomButtons()
    {
        $this->data['customButtons'] = request()->user()->can(
            'access-route', $this->route.'.exportExcel'
            ) ? true : false;
    }
}
