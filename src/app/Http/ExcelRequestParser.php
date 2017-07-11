<?php

namespace LaravelEnso\DataTable\app\Http;

class ExcelRequestParser
{
	private $params;

	public function __construct()
	{
		$this->params = request()->all();
		$this->run();
	}

	public function getParams()
	{
		return $this->params;
	}

	private function run()
	{
		$this->computeColumns();
		$this->params['start'] = 0;
		$this->params['length'] = $this->getLength();
		$this->params['search'] = (array) json_decode($this->params['search']);
	}

	private function computeColumns()
	{
		foreach ($this->params['columns'] as &$value) {
			$value = (array) json_decode($value);
		}
	}

	private function getLength()
	{
		$limit = config('datatable.excelRowLimit');

        if ($this->params['recordsDisplay'] > $limit) {
            throw new \EnsoException(__("Limit of rows exceeded by").' '.($this->params['recordsTotal'] - $limit));
        }

        return $limit;
	}
}