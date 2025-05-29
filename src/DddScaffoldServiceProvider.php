<?php

namespace EfTech\DddScaffold;

use EfTech\DddScaffold\Commands\InitCommand;
use EfTech\DddScaffold\Commands\MakeDomainModelCommand;
use EfTech\DddScaffold\Commands\MakeDtoCommand;
use EfTech\DddScaffold\Commands\MakeEnumCommand;
use EfTech\DddScaffold\Commands\MakeExceptionCommand;
use EfTech\DddScaffold\Commands\MakePresenterCommand;
use EfTech\DddScaffold\Commands\MakeQueryCommand;
use EfTech\DddScaffold\Commands\MakeRepositoryCommand;
use EfTech\DddScaffold\Commands\MakeRuleCommand;
use EfTech\DddScaffold\Commands\MakeServiceCommand;
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
                MakeDomainModelCommand::class,
                MakeValueObjectCommand::class,
                MakeRepositoryCommand::class,
                MakeServiceCommand::class,
                MakeExceptionCommand::class,
                MakeEnumCommand::class,
                MakeQueryCommand::class,
                MakePresenterCommand::class,
                MakeRuleCommand::class,
            ]);
        }
    }

    public function boot(): void
    {
        //
    }
}
