<?php

namespace EfTech\DddScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'ddd:make:repository {name : e.g. Task} {--domain= : Domain name (default from config)}';
    protected $description = 'Generate RepositoryInterface + EloquentRepository implementation';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'App');

        $interfaceName = $name.'RepositoryInterface';
        $eloquentName = 'Eloquent'.$name.'Repository';

        $interfaceNamespace = Str::studly($domain).'\\Domain\\Repositories';
        $eloquentNamespace = Str::studly($domain).'\\Infrastructure\\Repositories';
        $entityNamespace = Str::studly($domain).'\\Domain\\Entities';

        $stubDir = (config('ddd-scaffold.stubs_path') ?? __DIR__.'/../../stubs');

        // --- Interface ---
        $interfacePath = base_path("{$domain}/Domain/Repositories/{$interfaceName}.php");
        if (! File::exists($interfacePath)) {
            $stub = File::get($stubDir.'/repository-interface.stub');
            $content = str_replace(
                ['{{ namespace }}', '{{ class }}', '{{ entity }}', '{{ entity_namespace }}'],
                [$interfaceNamespace, $interfaceName, $name, $entityNamespace],
                $stub
            );
            File::ensureDirectoryExists(dirname($interfacePath));
            File::put($interfacePath, $content);
            $this->info("Created: {$interfaceName}");

            $gitkeepPath = dirname($interfacePath).'/.gitkeep';
            if (File::exists($gitkeepPath)) {
                File::delete($gitkeepPath);
            }
        } else {
            $this->warn("Skipped (already exists): {$interfacePath}");
        }

        // --- Eloquent ---
        $eloquentPath = base_path("{$domain}/Infrastructure/Repositories/{$eloquentName}.php");
        if (! File::exists($eloquentPath)) {
            $stub = File::get($stubDir.'/repository-eloquent.stub');
            $content = str_replace(
                [
                    '{{ namespace }}', '{{ class }}', '{{ entity }}', '{{ interface }}', '{{ interface_namespace }}',
                    '{{ entity_namespace }}',
                ],
                [$eloquentNamespace, $eloquentName, $name, $interfaceName, $interfaceNamespace, $entityNamespace],
                $stub
            );
            File::ensureDirectoryExists(dirname($eloquentPath));
            File::put($eloquentPath, $content);
            $this->info("Created: {$eloquentName}");

            $gitkeepPath = dirname($eloquentPath).'/.gitkeep';
            if (File::exists($gitkeepPath)) {
                File::delete($gitkeepPath);
            }
        } else {
            $this->warn("Skipped (already exists): {$eloquentPath}");
        }
    }
}
