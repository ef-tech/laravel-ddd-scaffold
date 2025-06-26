<?php

namespace EfTech\DddScaffold\Commands;

use EfTech\DddScaffold\Traits\DeletesGitkeepFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRepositoryCommand extends Command
{
    use DeletesGitkeepFiles;
    protected $signature = 'ddd:make:repository {name : The name of the repository} {--domain= : The domain name}';
    protected $description = 'Create a new repository class and interface.';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'MyProject');

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
            $this->info("Created: " . str_replace(base_path() . '/', '', $interfacePath));

            // Recursively delete .gitkeep files from the directory and its parent directories
            // up to the domain root directory
            $this->deleteGitkeepFilesRecursively(dirname($interfacePath), base_path($domain));
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
            $this->info("[EloquentRepository] [{$eloquentName}] created at: " . str_replace(base_path() . '/', '', $eloquentPath));

            // Recursively delete .gitkeep files from the directory and its parent directories
            // up to the domain root directory
            $this->deleteGitkeepFilesRecursively(dirname($eloquentPath), base_path($domain));
        } else {
            $this->warn("Skipped (already exists): {$eloquentPath}");
        }
    }
}
