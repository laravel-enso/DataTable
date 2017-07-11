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
        $this->data->each(function($value) {
        	foreach ($this->getDataFromEnums() as $key => $data) {
                $value->$key = $data[$value->$key];
            }
        });
    }

    private function getDataFromEnums()
    {
        $dataFromEnum = [];

        foreach ($this->enumMappings as $column => $enumClass) {
            $enum = new $enumClass();
            $dataFromEnum[$column] = $enum->getData();
        }

        return $dataFromEnum;
    }
}