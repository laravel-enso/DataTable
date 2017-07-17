<?php

namespace LaravelEnso\DataTable\app\Exports;

use LaravelEnso\Helpers\Classes\Object;

class TableReport
{
    private $fileName;
    private $data;
    private $header;

    public function __construct(string $fileName, Object $data)
    {
        $this->fileName = $fileName;
        $this->setHeader($data);
        $this->setData($data);
    }

    public function run()
    {
        \Excel::create($this->fileName, function ($excel) {
            $excel->sheet('Sheet1', function ($sheet) {
                $sheet->fromArray($this->data)
                    ->setAutoFilter()
                    ->freezeFirstRowAndColumn()
                    ->setAllBorders('thin')
                    ->row(1, function ($cells) {
                        $cells->setFontWeight('bold');
                    });
            });
        })->store('xlsx');
    }

    private function setData(Object $data)
    {
        $this->data = [];

        $data->records->each(function ($record) {
            $element = [];

            foreach ($record->toArray() as $key => $value) {
                if (!isset($this->header[$key])) {
                    continue;
                }

                $element[$this->header[$key]] = $value;
            }

            $this->data[] = $element;
        });
    }

    private function setHeader(Object $data)
    {
        $this->header = collect($data->header)->pluck('label', 'data')->toArray();
    }
}
