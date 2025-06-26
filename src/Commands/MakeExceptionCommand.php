<?php

namespace EfTech\DddScaffold\Commands;

use EfTech\DddScaffold\Traits\DeletesGitkeepFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeExceptionCommand extends Command
{
    use DeletesGitkeepFiles;
    protected $signature = 'ddd:make:exception {name : The name of the exception class} {--domain= : The domain name} {--type=domain : The layer of the exception}';

    protected $description = 'Create a new exception class.';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'MyProject');
        $type = $this->option('type') ?? 'domain';

        $validTypes = ['domain', 'application', 'infrastructure', 'support'];
        if (! in_array($type, $validTypes)) {
            $this->error("Invalid type: {$type}. Allowed types: ".implode(', ', $validTypes));

            return;
        }

        $namespace = Str::studly($domain).'\\'.Str::studly($type).'\\Exceptions';
        $path = base_path("{$domain}/".Str::studly($type)."/Exceptions/{$name}.php");

        if (File::exists($path)) {
            $this->error("{$name} already exists at: {$path}");

            return;
        }

        $stubPath = (config('ddd-scaffold.stubs_path') ?? __DIR__.'/../../stubs').'/exception.stub';
        if (! File::exists($stubPath)) {
            $this->error("Stub file not found: {$stubPath}");

            return;
        }

        $stub = File::get($stubPath);
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $name],
            $stub
        );

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        // Recursively delete .gitkeep files from the directory and its parent directories
        // up to the domain root directory
        $this->deleteGitkeepFilesRecursively(dirname($path), base_path($domain));

        $this->info("[Exception] [{$name}] created at: ".str_replace(base_path().'/', '', $path));
    }
}
