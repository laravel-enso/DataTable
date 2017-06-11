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
        $this->increaseTotalsIndex();
            $this->increaseRenderIndex();
    }

    private function increaseTotalsIndex()
    {
        if (!isset($this->data['totals'])) {
            return false;
        }

        $tmp = array_flip($this->data['totals']);
        $tmp = $this->increaseColumnIndex($tmp);
        $this->data['totals'] = array_flip($tmp);

        return $this;
    }

    private function increaseRenderIndex()
    {
        if (!isset($this->data['render'])) {
            return false;
        }

        $this->data['render'] = $this->increaseColumnIndex($this->data['render']);
    }

    private function increaseColumnIndex($array)
    {
        return array_map(function($index) {
            return ++$index;
        }, $array);
    }
}
