<?php

namespace LaravelEnso\DataTable\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LaravelEnso\Core\app\Models\User;
use LaravelEnso\DataTable\app\Exports\TableReport;
use LaravelEnso\DataTable\app\Notifications\TableExportNotification;
use LaravelEnso\Helpers\Classes\Object;

class TableExportJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $data;
    private $fileName;
    private $fullPathFile;

    public function __construct(User $user, Object $data)
    {
        $this->user = $user;
        $this->data = $data;
        $this->fileName = __('Report');
        $this->fullPathFile = config('laravel-enso.paths.exports').DIRECTORY_SEPARATOR.$this->fileName.'.xlsx';
    }

    public function handle()
    {
        (new TableReport($this->fileName, $this->data))->run();
        $this->sendReport();
        $this->cleanUp();
    }

    private function sendReport()
    {
        $this->user->notify(new TableExportNotification(storage_path(DIRECTORY_SEPARATOR.$this->fullPathFile)));
    }

    private function cleanUp()
    {
        \Storage::delete($this->fullPathFile);
    }
}
