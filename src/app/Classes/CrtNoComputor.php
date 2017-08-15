<?php

namespace LaravelEnso\DataTable\app\Classes;

class CrtNoComputor
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->run();
    }

    public function getData()
    {
        return $this->data;
    }

    private function run()
    {
        $this->increaseTotalsIndex()
            ->increaseBooleanIndex()
            ->increaseRenderIndex();
    }

    private function increaseTotalsIndex()
    {
        if (isset($this->data['totals'])) {
            $this->data['totals'] = array_flip(
                $this->increaseColumnIndex(
                    array_flip($this->data['totals'])
                )
            );
        }

        return $this;
    }

    private function increaseBooleanIndex()
    {
        if (isset($this->data['boolean'])) {
            $this->data['boolean'] = $this->increaseColumnIndex($this->data['boolean']);
        }

        return $this;
    }

    private function increaseRenderIndex()
    {
        if (isset($this->data['render'])) {
            $this->data['render'] = $this->increaseColumnIndex($this->data['render']);
        }

        return $this;
    }

    private function increaseColumnIndex($array)
    {
        return array_map(function ($index) {
            return ++$index;
        }, $array);
    }
}
