<?php

namespace EfTech\DddScaffold\Commands;

use EfTech\DddScaffold\Traits\DeletesGitkeepFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeAggregateCommand extends Command
{
    use DeletesGitkeepFiles;
    protected $signature = 'ddd:make:aggregate {name : The name of the aggregate} {--domain= : The domain name}';

    protected $description = 'Create a new domain aggregate class.';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'MyProject');
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

        $namespace = Str::studly($domain).'\\Domain\\Aggregates'.
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

        // Recursively delete .gitkeep files from the directory and its parent directories
        // up to the domain root directory
        $this->deleteGitkeepFilesRecursively(dirname($path), base_path($domain));

        $this->info("[Aggregate] [{$class}] created at: ".str_replace(base_path().'/', '', $path));
    }
}
