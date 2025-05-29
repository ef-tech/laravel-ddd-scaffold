<?php

namespace EfTech\DddScaffold;

use EfTech\DddScaffold\Commands\InitCommand;
use Illuminate\Support\ServiceProvider;

class DddScaffoldServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InitCommand::class,
            ]);
        }
    }

    public function boot(): void
    {
        //
    }
}
