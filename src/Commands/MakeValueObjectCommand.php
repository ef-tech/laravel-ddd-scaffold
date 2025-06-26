<?php

namespace EfTech\DddScaffold\Commands;

use EfTech\DddScaffold\Traits\DeletesGitkeepFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeValueObjectCommand extends Command
{
    use DeletesGitkeepFiles;
    protected $signature = 'ddd:make:vo {name : The name of the value object class} {--domain= : The domain name}';
    protected $description = 'Create a new value object class.';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'MyProject');

        $namespace = Str::studly($domain).'\\Domain\\ValueObjects';
        $path = base_path("{$domain}/Domain/ValueObjects/{$name}.php");

        if (File::exists($path)) {
            $this->error("{$name} already exists at: {$path}");

            return;
        }

        $stubPath = (config('ddd-scaffold.stubs_path') ?? __DIR__.'/../../stubs').'/vo.stub';
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

        $this->info("[ValueObject] [{$name}] created at: ".str_replace(base_path().'/', '', $path));
    }
}
