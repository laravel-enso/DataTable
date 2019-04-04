<?php

namespace LaravelEnso\DataTable\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LaravelEnso\Core\app\Models\User;
use LaravelEnso\DataTable\app\Exports\TableExport;
use LaravelEnso\DataTable\app\Notifications\TableReportNotification;
use LaravelEnso\Helpers\Classes\Obj;

class TableReportJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $data;
    private $fileName;
    private $fullPathFile;

    public function __construct(User $user, Obj $data)
    {
        $this->timeout = config('datatable.timeout') ?? 30;
        $this->user = $user;
        $this->data = $data;
        $this->fileName = __('Report');
        $this->fullPathFile =
            config('laravel-enso.paths.exports').DIRECTORY_SEPARATOR.$this->fileName.'.xlsx';
    }

    public function handle()
    {
        (new TableExport($this->fileName, $this->data))->run();
        $this->sendReport();
        $this->cleanUp();
    }

    private function sendReport()
    {
        $this->user->notify(
            new TableReportNotification(
                storage_path('app'.DIRECTORY_SEPARATOR.$this->fullPathFile)
            )
        );
    }

    private function cleanUp()
    {
        \Storage::delete($this->fullPathFile);
    }
}
