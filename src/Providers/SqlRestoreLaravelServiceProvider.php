<?php

namespace SqlRestoreLaravel\Providers;

use Illuminate\Support\ServiceProvider;
use SqlRestoreLaravel\Commands\AppMeditibbInit;

class SqlRestoreLaravelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AppMeditibbInit::class,
            ]);
        }
    }
}
