<?php

namespace LaravelEnso\DataTable;

use Illuminate\Support\ServiceProvider;

class DataTableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config' => config_path('enso'),
        ], 'datatable-config');

        $this->publishes([
            __DIR__.'/config' => config_path('enso'),
        ], 'enso-config');

        $this->publishes([
            __DIR__.'/resources/dt-lang' => resource_path('dt-lang'),
        ], 'datatable-lang');

        $this->publishes([
            __DIR__.'/resources/DataTable' => app_path('DataTable'),
        ], 'datatable-class');

        $this->mergeConfigFrom(__DIR__.'/config/datatable.php', 'datatable');
    }

    public function register()
    {
        //
    }
}
