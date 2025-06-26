<?php

namespace EfTech\DddScaffold\Commands;

use EfTech\DddScaffold\Traits\DeletesGitkeepFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeEntityCommand extends Command
{
    use DeletesGitkeepFiles;
    protected $signature = 'ddd:make:entity {name : The name of the entity class} {--domain= : The domain name} {--type=domain : The type of the entity (domain, application, presenters)}';
    protected $description = 'Create a new entity class.';

    public function handle(): void
    {
        $input = $this->argument('name');
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'MyProject');
        $type = $this->option('type');

        $segments = collect(explode('/', $input))->filter()->values();
        $baseClass = $segments->pop();
        $className = Str::studly($baseClass);

        $subNamespace = $segments->map(fn($s) => Str::studly($s))->implode('\\');
        $subPath = $segments->implode('/');

        $baseNamespace = Str::studly($domain);
        $basePath = base_path($domain);

        if ($type === 'application' || $type === 'presenters') {
            $layerNamespace = 'Application\\Presenters\\Entities';
            $layerPath = 'Application/Presenters/Entities';
            $entityType = 'Application';
        } else {
            $layerNamespace = 'Domain\\Entities';
            $layerPath = 'Domain/Entities';
            $entityType = 'Domain';
        }

        $namespace = $baseNamespace.'\\'.$layerNamespace.($subNamespace ? "\\{$subNamespace}" : '');
        $path = $basePath."/{$layerPath}".($subPath ? "/{$subPath}" : '')."/{$className}.php";

        if (File::exists($path)) {
            $this->error("{$className} already exists at: {$path}");

            return;
        }

        $stubPath = (config('ddd-scaffold.stubs_path') ?? __DIR__.'/../../stubs').'/entity.stub';
        if (! File::exists($stubPath)) {
            $this->error("Stub file not found: {$stubPath}");

            return;
        }

        $stub = File::get($stubPath);
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $className],
            $stub
        );

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        // Recursively delete .gitkeep files from the directory and its parent directories
        // up to the domain root directory
        $this->deleteGitkeepFilesRecursively(dirname($path), base_path($domain));

        $this->info("[{$entityType} Entity] [{$className}] created at: ".str_replace(base_path().'/', '', $path));
    }
}
