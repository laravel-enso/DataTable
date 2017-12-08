<?php

namespace LaravelEnso\DataTable\app\Classes;

use Illuminate\Database\Eloquent\Collection;

class EnumComputor
{
    private $data;
    private $enumMappings;

    public function __construct(Collection $data, array $enumMappings)
    {
        $this->data = $data;
        $this->enumMappings = $enumMappings;
    }

    public function run()
    {
        $this->computeEnums();
    }

    private function computeEnums()
    {
        $this->data->each(function ($value) {
            foreach ($this->getDataFromEnums() as $key => $data) {
                $value->$key = is_null($value->$key) ? null : $data[$value->$key];
            }
        });
    }

    private function getDataFromEnums()
    {
        $dataFromEnum = [];

        foreach ($this->enumMappings as $column => $enumClass) {
            $dataFromEnum[$column] = (new $enumClass())->getData();
        }

        return $dataFromEnum;
    }
}
