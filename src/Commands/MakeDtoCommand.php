<?php

namespace EfTech\DddScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeDtoCommand extends Command
{
    protected $signature = 'ddd:make:dto {name : e.g. TaskData or Admin/TaskData} {--domain= : Domain name (default from config)}';
    protected $description = 'Generate a new DTO class under Application/DTOs';

    public function handle(): void
    {
        $input = $this->argument('name');
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'App');

        $segments = collect(explode('/', $input))->filter()->values();
        $baseClass = $segments->pop();
        $className = Str::studly($baseClass);

        $subNamespace = $segments->map(fn($s) => Str::studly($s))->implode('\\');
        $subPath = $segments->implode('/');

        $namespace = Str::studly($domain).'\\Application\\DTOs'.($subNamespace ? "\\{$subNamespace}" : '');
        $basePath = base_path("{$domain}/Application/DTOs");
        $path = base_path("{$domain}/Application/DTOs".($subPath ? "/{$subPath}" : '')."/{$className}.php");

        if (File::exists($path)) {
            $this->error("{$className} already exists at {$path}.");
            return;
        }

        $stubPath = (config('ddd-scaffold.stubs_path') ?? __DIR__.'/../../stubs').'/dto.stub';
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

        $gitkeepPath = $basePath.'/.gitkeep';
        if (File::exists($gitkeepPath)) {
            File::delete($gitkeepPath);
        }

        $this->info("DTO [{$className}] created at: {$path}");
    }
}
