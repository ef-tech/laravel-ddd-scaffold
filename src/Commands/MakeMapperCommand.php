<?php

namespace EfTech\DddScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeMapperCommand extends Command
{
    protected $signature = 'ddd:make:mapper
        {name : Mapper class path (e.g. Customer/CustomerMapper)}
        {--domain= : Domain root (default from config)}
        {--entity= : Domain entity FQCN using / (e.g. Backoffice/Domain/Entity/Customer)}
        {--model= : Eloquent model FQCN using / (e.g. App/Models/Customer)}
        {--dto= : DTO class FQCN using / (e.g. Backoffice/Application/DTO/CustomerData)}';

    protected $description = 'Generate a mapper for converting between Eloquent model and Domain entity';

    public function handle(): void
    {
        $isDto = $this->option('dto') !== null;
        $isModel = $this->option('model') !== null;

        if ($isDto && $isModel) {
            $this->error("You cannot specify both --dto and --model options at the same time.");
            return;
        }

        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'Backoffice');
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
        $mapperDir = $isDto ? 'Application/Mapper' : 'Infrastructure/Mapper';
        $outputPath = base_path("{$domain}/{$mapperDir}/{$relativePath}");

        if (File::exists($outputPath)) {
            $this->error("Mapper already exists: {$outputPath}");
            return;
        }

        File::ensureDirectoryExists(dirname($outputPath));

        $namespace = "{$domain}\\".($isDto ? 'Application\\Mapper' : 'Infrastructure\\Mapper').
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

        $this->info("Mapper created at: {$outputPath}");
    }
}
