<?php

namespace LaravelEnso\DataTable\app\Classes;

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
            ->setHeaderButtons();

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

        $actions = collect($this->data['actionButtons']);

        $this->data['actionButtons'] = [];

        $this->data['actionButtons']['label']    = $this->data['actions'] ?: 'Actions';
        $this->data['actionButtons']['standard'] = $this->filterAllowedActions($actions);
        $this->data['actionButtons']['custom']   = [];

        return $this;
    }

    private function filterAllowedActions($actions)
    {
        return $actions->filter(function ($action) {
            return request()->user()
                ->can('access-route', $this->route . '.' . $action);
        })->values();
    }

    private function setCustomActionButtons()
    {
        if (!isset($this->data['customActionButtons'])) {
            return $this;
        }

        foreach ($this->data['customActionButtons'] as $customButton) {
            if (isset($customButton['route'])
                && !request()->user()
                    ->can('access-route', $customButton['route'])) {
                continue;
            }

            $this->data['actionButtons']['custom'][] = $customButton;
        }

        unset($this->data['customActionButtons']);

        return $this;
    }

    private function setHeaderButtons()
    {
        $this->data['headerButtons'] = isset($this->data['headerButtons']) ? collect($this->data['headerButtons'])->filter(function($action) {
            return request()->user()
                ->can('access-route', $this->route . '.' . $action);
        }) : [];
    }
}
