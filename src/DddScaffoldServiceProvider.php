<?php

namespace EfTech\DddScaffold;

use EfTech\DddScaffold\Commands\InitCommand;
use EfTech\DddScaffold\Commands\MakeUseCaseCommand;
use Illuminate\Support\ServiceProvider;

class DddScaffoldServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InitCommand::class,
                MakeUseCaseCommand::class
            ]);
        }
    }

    public function boot(): void
    {
        //
    }
}
