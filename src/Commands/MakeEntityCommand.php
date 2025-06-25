<?php

namespace EfTech\DddScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeEntityCommand extends Command
{
    protected $signature = 'ddd:make:entity {name : The name of the entity class} {--domain= : The domain name}';
    protected $description = 'Create a new domain entity class.';

    public function handle(): void
    {
        $input = $this->argument('name');
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'MyProject');

        $segments = collect(explode('/', $input))->filter()->values();
        $baseClass = $segments->pop();
        $className = Str::studly($baseClass);

        $subNamespace = $segments->map(fn($s) => Str::studly($s))->implode('\\');
        $subPath = $segments->implode('/');

        $namespace = Str::studly($domain).'\\Domain\\Entities'.($subNamespace ? "\\{$subNamespace}" : '');
        $basePath = base_path("{$domain}/Domain/Entities");
        $path = base_path("{$domain}/Domain/Entities".($subPath ? "/{$subPath}" : '')."/{$className}.php");

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

        $gitkeepPath = dirname($path).'/.gitkeep';
        if (File::exists($gitkeepPath)) {
            File::delete($gitkeepPath);
        }

        $this->info("Domain Entity [{$className}] created at: {$path}");
    }
}
