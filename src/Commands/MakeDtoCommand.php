<?php

namespace EfTech\DddScaffold\Commands;

use EfTech\DddScaffold\Traits\DeletesGitkeepFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeDtoCommand extends Command
{
    use DeletesGitkeepFiles;
    protected $signature = 'ddd:make:dto {name : The name of the DTO class} {--domain= : The domain name}';
    protected $description = 'Create a new data transfer object class.';

    public function handle(): void
    {
        $input = $this->argument('name');
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'MyProject');

        $segments = collect(explode('/', $input))->filter()->values();
        $baseClass = $segments->pop();
        $className = Str::studly(Str::finish($baseClass, 'Data'));

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

        // Recursively delete .gitkeep files from the directory and its parent directories
        // up to the domain root directory
        $this->deleteGitkeepFilesRecursively(dirname($path), base_path($domain));

        $this->info("[DTO] [{$className}] created at: " . str_replace(base_path() . '/', '', $path));
    }
}
