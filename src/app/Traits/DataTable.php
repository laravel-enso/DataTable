<?php

namespace LaravelEnso\DataTable\app\Traits;

use LaravelEnso\DataTable\app\Classes\TableBuilder;
use LaravelEnso\DataTable\app\Classes\TableInit;
use LaravelEnso\DataTable\app\Http\ExcelRequestParser;
use LaravelEnso\DataTable\app\Jobs\TableExportJob;

trait DataTable
{
    public function initTable()
    {
        return (
            new TableInit(new $this->tableStructureClass())
        )->getData();
    }

    public function getTableData()
    {
        \Log::info(request()->all());
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

        $this->dispatch(new TableExportJob(request()->user(), $data));

        return ['message' => __('The requested report was started.  It can take a few minutes before you have it in your inbox')];
    }
}
