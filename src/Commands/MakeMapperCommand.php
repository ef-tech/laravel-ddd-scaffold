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
        {--model= : Eloquent model FQCN using / (e.g. App/Models/Customer)}
        {--entity= : Domain entity FQCN using / (e.g. Backoffice/Domain/Entity/Customer)}';

    protected $description = 'Generate a mapper for converting between Eloquent model and Domain entity';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'Backoffice');
        $modelFqcn = str_replace('/', '\\', $this->option('model') ?? 'App/Models/DummyModel');
        $entityFqcn = str_replace('/', '\\', $this->option('entity') ?? "{$domain}/Domain/Entity/DummyEntity");

        $stubPath = (config('ddd-scaffold.stubs_path') ?? __DIR__.'/../../stubs').'/mapper.stub';

        if (! File::exists($stubPath)) {
            $this->error("Stub file not found: {$stubPath}");
            return;
        }

        $relativePath = str_replace('\\', '/', $name).'.php';
        $outputPath = base_path("{$domain}/Infrastructure/Mapper/{$relativePath}");

        if (File::exists($outputPath)) {
            $this->error("Mapper already exists: {$outputPath}");
            return;
        }

        File::ensureDirectoryExists(dirname($outputPath));

        $namespace = "{$domain}\\Infrastructure\\Mapper".
            (Str::contains($name, '/') ? '\\'.str_replace('/', '\\', dirname($name)) : '');
        $class = class_basename($name);

        $stub = File::get($stubPath);
        $rendered = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ eloquent_model_namespace }}',
                '{{ domain_entity_namespace }}',
            ],
            [
                $namespace,
                $class,
                $modelFqcn,
                $entityFqcn,
            ],
            $stub
        );

        File::put($outputPath, $rendered);

        $this->info("Mapper created at: {$outputPath}");
    }
}
