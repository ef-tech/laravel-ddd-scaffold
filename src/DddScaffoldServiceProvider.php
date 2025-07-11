<?php

namespace EfTech\DddScaffold;

use EfTech\DddScaffold\Commands\InitCommand;
use EfTech\DddScaffold\Commands\MakeAggregateCommand;
use EfTech\DddScaffold\Commands\MakeConstantCommand;
use EfTech\DddScaffold\Commands\MakeDtoCommand;
use EfTech\DddScaffold\Commands\MakeEntityCommand;
use EfTech\DddScaffold\Commands\MakeEnumCommand;
use EfTech\DddScaffold\Commands\MakeExceptionCommand;
use EfTech\DddScaffold\Commands\MakeMapperCommand;
use EfTech\DddScaffold\Commands\MakePresenterCommand;
use EfTech\DddScaffold\Commands\MakeQueryCommand;
use EfTech\DddScaffold\Commands\MakeRepositoryCommand;
use EfTech\DddScaffold\Commands\MakeRuleCommand;
use EfTech\DddScaffold\Commands\MakeServiceCommand;
use EfTech\DddScaffold\Commands\MakeTestCommand;
use EfTech\DddScaffold\Commands\MakeUseCaseCommand;
use EfTech\DddScaffold\Commands\MakeValueObjectCommand;
use Illuminate\Support\ServiceProvider;

class DddScaffoldServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InitCommand::class,
                MakeUseCaseCommand::class,
                MakeDtoCommand::class,
                MakeEntityCommand::class,
                MakeValueObjectCommand::class,
                MakeRepositoryCommand::class,
                MakeServiceCommand::class,
                MakeExceptionCommand::class,
                MakeEnumCommand::class,
                MakeQueryCommand::class,
                MakePresenterCommand::class,
                MakeRuleCommand::class,
                MakeConstantCommand::class,
                MakeTestCommand::class,
                MakeAggregateCommand::class,
                MakeMapperCommand::class,
            ]);
        }

        $this->mergeConfigFrom(
            __DIR__.'/../config/ddd-scaffold.php',
            'ddd-scaffold'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ddd-scaffold.php' => config_path('ddd-scaffold.php'),
            ], 'ddd-scaffold-config');
        }
    }
}
