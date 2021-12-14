<?php

namespace LibreTranslateLaravel;

use Illuminate\Support\ServiceProvider;
use SqlRestoreLaravel\Commands\AppMeditibbInit;

class LibreTranslateServiceProvider extends ServiceProvider
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
