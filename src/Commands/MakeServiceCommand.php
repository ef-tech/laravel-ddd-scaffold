<?php

namespace EfTech\DddScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeServiceCommand extends Command
{
    protected $signature = 'ddd:make:service 
        {name : Service name (e.g. CreateTaskService)} 
        {--domain= : Domain name (default from config)} 
        {--type=application : application or domain}';

    protected $description = 'Generate a new ApplicationService or DomainService class';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'App');
        $type = $this->option('type') === 'domain' ? 'Domain' : 'Application';

        $namespace = Str::studly($domain)."\\{$type}\\Services";
        $path = base_path("{$domain}/{$type}/Services/{$name}.php");

        if (File::exists($path)) {
            $this->error("{$name} already exists at: {$path}");
            return;
        }

        $stubName = $type === 'Domain' ? 'domain-service.stub' : 'application-service.stub';
        $stubPath = config('ddd-scaffold.stubs_path', __DIR__.'/../../stubs').'/'.$stubName;

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

        $this->info("{$type} service [{$name}] created at: {$path}");
    }
}
