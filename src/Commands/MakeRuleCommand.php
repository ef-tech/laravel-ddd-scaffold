<?php

namespace EfTech\DddScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRuleCommand extends Command
{
    protected $signature = 'ddd:make:rule
        {name : Rule class name (e.g. TaskStatusRule)}
        {--domain= : Domain name (default from config)}';

    protected $description = 'Create a new domain rule class.';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'MyProject');

        $namespace = Str::studly($domain).'\\Domain\\Rules';
        $path = base_path("{$domain}/Domain/Rules/{$name}.php");

        if (File::exists($path)) {
            $this->error("{$name} already exists at: {$path}");
            return;
        }

        $stubPath = (config('ddd-scaffold.stubs_path') ?? __DIR__.'/../../stubs').'/rule.stub';
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

        $gitkeepPath = dirname($path).'/.gitkeep';
        if (File::exists($gitkeepPath)) {
            File::delete($gitkeepPath);
        }

        $this->info("Rule [{$name}] created at: {$path}");
    }
}
