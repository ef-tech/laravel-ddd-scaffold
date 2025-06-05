<?php

namespace EfTech\DddScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeAggregateCommand extends Command
{
    protected $signature = 'ddd:make:aggregate
        {name : Aggregate class path (e.g. Customer/CustomerAggregate)}
        {--domain= : Domain root (default from config)}';

    protected $description = 'Generate a Domain Aggregate class';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'Backoffice');
        $stubPath = (config('ddd-scaffold.stubs_path') ?? __DIR__.'/../../stubs').'/aggregate.stub';

        if (! File::exists($stubPath)) {
            $this->error("Stub file not found: {$stubPath}");
            return;
        }

        $relativePath = str_replace('\\', '/', $name).'.php';
        $path = base_path("{$domain}/Domain/Aggregates/{$relativePath}");

        if (File::exists($path)) {
            $this->error("Aggregate already exists: {$path}");
            return;
        }

        File::ensureDirectoryExists(dirname($path));

        $namespace = Str::studly($domain)."\\Domain\\Aggregates".
            (Str::contains($name, '/') ? '\\'.str_replace('/', '\\', dirname($name)) : '');
        $class = class_basename($name);

        $stub = File::get($stubPath);
        $rendered = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $class],
            $stub
        );

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $rendered);

        $gitkeepPath = dirname($path).'/.gitkeep';
        if (File::exists($gitkeepPath)) {
            File::delete($gitkeepPath);
        }

        $this->info("Aggregate created at: {$path}");
    }
}
