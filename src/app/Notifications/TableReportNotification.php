<?php

namespace LaravelEnso\DataTable\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TableReportNotification extends Notification
{
    use Queueable;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        app()->setLocale($notifiable->preferences->global->lang);

        return (new MailMessage())
            ->subject(__('Export Notification'))
            ->view('laravel-enso/datatable::emails.exportDone', [
                'lines' => [
                    __('You will find attached the requested report.'),
                    __('Thank you for using our application!'),
                ],
            ])
            ->attach($this->file);
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
