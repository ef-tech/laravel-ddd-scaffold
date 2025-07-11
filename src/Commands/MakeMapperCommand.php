<?php

namespace EfTech\DddScaffold\Commands;

use EfTech\DddScaffold\Traits\DeletesGitkeepFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeMapperCommand extends Command
{
    use DeletesGitkeepFiles;
    protected $signature = 'ddd:make:mapper {name : The name of the mapper class} {--domain= : The domain name} {--entity= : The domain entity} {--model= : The eloquent model} {--dto= : The DTO class}';

    protected $description = 'Create a new mapper class.';

    public function handle(): void
    {
        $isDto = $this->option('dto') !== null;
        $isModel = $this->option('model') !== null;

        if ($isDto && $isModel) {
            $this->error('You cannot specify both --dto and --model options at the same time.');

            return;
        }

        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'MyProject');
        $entityFqcn = str_replace('/', '\\', $this->option('entity') ?? "{$domain}/Domain/Entity/DummyEntity");
        $modelFqcn = str_replace('/', '\\', $this->option('model') ?? 'App/Models/DummyModel');
        $dtoFqcn = str_replace('/', '\\', $this->option('dto') ?? "{$domain}/Application/DTOs/DummyEntity");

        $isDto = $this->option('dto') !== null;

        $stubPath = (config('ddd-scaffold.stubs_path') ?? __DIR__.'/../../stubs')
            .($isDto ? '/mapper-dto.stub' : '/mapper-model.stub');

        if (! File::exists($stubPath)) {
            $this->error("Stub file not found: {$stubPath}");

            return;
        }

        $relativePath = str_replace('\\', '/', $name).'.php';
        $mapperDir = $isDto ? 'Application/Mappers' : 'Infrastructure/Mappers';
        $outputPath = base_path("{$domain}/{$mapperDir}/{$relativePath}");

        if (File::exists($outputPath)) {
            $this->error("Mapper already exists: {$outputPath}");

            return;
        }

        File::ensureDirectoryExists(dirname($outputPath));

        $namespace = Str::studly($domain).'\\'.($isDto ? 'Application\\Mappers' : 'Infrastructure\\Mappers').
            (Str::contains($name, '/') ? '\\'.str_replace('/', '\\', dirname($name)) : '');
        $class = class_basename($name);

        $stub = File::get($stubPath);
        $rendered = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ eloquent_model_namespace }}',
                '{{ domain_entity_namespace }}',
                '{{ dto_namespace }}',
            ],
            [
                $namespace,
                $class,
                $modelFqcn,
                $entityFqcn,
                $dtoFqcn,
            ],
            $stub
        );

        File::put($outputPath, $rendered);

        // Recursively delete .gitkeep files from the directory and its parent directories
        // up to the domain root directory
        $this->deleteGitkeepFilesRecursively(dirname($outputPath), base_path($domain));

        $this->info("[Mapper] [{$class}] created at: ".str_replace(base_path().'/', '', $outputPath));
    }
}
