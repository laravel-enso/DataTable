<?php

namespace LaravelEnso\DataTable\app\Traits;

use LaravelEnso\DataTable\app\Classes\TableBuilder;
use LaravelEnso\DataTable\app\Classes\TableInit;
use LaravelEnso\DataTable\app\Http\ExcelRequestParser;
use LaravelEnso\DataTable\app\Jobs\TableReportJob;

trait DataTable
{
    public function initTable()
    {
        return (new TableInit(
            new $this->tableStructureClass(),
            request()->route()->getName()
        ))->getData();
    }

    public function getTableData()
    {
        return (new TableBuilder(
            new $this->tableStructureClass(),
            $this->getTableQuery(),
            request()->all()
        ))->getTableData();
    }

    public function exportExcel()
    {
        $data = (new TableBuilder(
            new $this->tableStructureClass(),
            $this->getTableQuery(),
            (new ExcelRequestParser())->getParams()
        ))->getExcelData();

        $this->dispatch(new TableReportJob(request()->user(), $data));

        return [
            'message' => __(config('labels.emailReportRequest')),
        ];
    }
}
