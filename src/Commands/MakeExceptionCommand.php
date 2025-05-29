<?php

namespace EfTech\DddScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeExceptionCommand extends Command
{
    protected $signature = 'ddd:make:exception 
        {name : Exception class name (e.g. TaskNotFoundException)} 
        {--domain= : Domain name (default from config)} 
        {--type=domain : domain, application, infrastructure, or support}';

    protected $description = 'Generate a new exception class in the specified layer';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'App');
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

        $stubPath = config('ddd-scaffold.stubs_path', __DIR__.'/../../stubs').'/exception.stub';
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

        $this->info("Exception [{$name}] created at: {$path}");
    }
}
