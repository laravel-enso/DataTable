<?php

namespace LaravelEnso\DataTable;

use Illuminate\Support\ServiceProvider;

class DataTableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/app/resources/assets/js/components' => resource_path('assets/js/vendor/laravel-enso/components'),
        ], 'datatable-component');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
