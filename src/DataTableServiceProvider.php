<?php

namespace LaravelEnso\DataTable;

use Illuminate\Support\ServiceProvider;

class DataTableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/resources/assets/js/components' => resource_path('assets/js/vendor/laravel-enso/components'),
        ], 'datatable-component');

        $this->publishes([
            __DIR__.'/config/datatable.php' => config_path('datatable.php'),
        ], 'datatable-config');

        $this->publishes([
            __DIR__.'/config/datatable.php' => config_path('datatable.php'),
        ], 'enso-config');

        $this->publishes([
            __DIR__.'/resources/assets/js/modules' => resource_path('assets/js/vendor/laravel-enso/modules'),
        ], 'datatable-options');

        $this->publishes([
            __DIR__.'/resources/dt-lang' => resource_path('dt-lang'),
        ], 'datatable-lang');

        $this->publishes([
            __DIR__.'/resources/DataTable' => app_path('DataTable'),
        ], 'datatable-class');

        $this->publishes([
            __DIR__.'/resources/assets/js/components' => resource_path('assets/js/vendor/laravel-enso/components'),
        ], 'enso-update');

        $this->mergeConfigFrom(__DIR__.'/config/datatable.php', 'datatable');
    }

    public function register()
    {
        //
    }
}
