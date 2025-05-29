<?php

namespace EfTech\DddScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePresenterCommand extends Command
{
    protected $signature = 'ddd:make:presenter
        {name : Presenter class name (e.g. TaskPresenter)}
        {--domain= : Domain name (default from config)}';

    protected $description = 'Generate a new application presenter class';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'App');

        $namespace = Str::studly($domain).'\\Application\\Presenters';
        $path = base_path("{$domain}/Application/Presenters/{$name}.php");

        if (File::exists($path)) {
            $this->error("{$name} already exists at: {$path}");
            return;
        }

        $stubPath = config('ddd-scaffold.stubs_path', __DIR__.'/../../stubs').'/presenter.stub';
        if (! File::exists($stubPath)) {
            $this->error("Stub file not found: {$stubPath}");
            return;
        }

        $stub = File::get($stubPath);
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $name],
            $stub
        );

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $gitkeepPath = dirname($path).'/.gitkeep';
        if (File::exists($gitkeepPath)) {
            File::delete($gitkeepPath);
        }

        $this->info("Presenter [{$name}] created at: {$path}");
    }
}
