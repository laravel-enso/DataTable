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
            __DIR__.'/../resources/assets/js/components/core' => base_path('resources/assets/js/components/core'),
        ], 'table-component');
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
