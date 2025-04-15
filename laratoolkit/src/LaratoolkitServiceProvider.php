<?php

namespace Laratoolkit;

use Illuminate\Support\ServiceProvider;
use Laratoolkit\Commands\GenerateCrud;

class LaratoolkitServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateCrud::class,
            ]);
        }
    }

    public function register()
    {
        //
    }
}
