<?php

namespace EfTech\DddScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeTestCommand extends Command
{
    protected $signature = 'ddd:make:test {name : The name of the test class} {--domain= : The domain name}';

    protected $description = 'Create a new test class.';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $domain = $this->option('domain') ?? config('ddd-scaffold.default_domain', 'MyProject');
        $framework = config('ddd-scaffold.testing_framework', 'phpunit');

        // Determine stub file based on framework
        $stubName = match (strtolower($framework)) {
            'pest' => 'test.pest.stub',
            default => 'test.phpunit.stub',
        };

        $stubPath = (config('ddd-scaffold.stubs_path') ?? __DIR__.'/../../stubs').'/'.$stubName;

        if (! File::exists($stubPath)) {
            $this->error("Stub file not found: {$stubPath}");
            return;
        }

        $isPest = $framework === 'pest';
        $relativePath = str_replace('\\', '/', $name).'Test.php';

        $outputPath = base_path("tests/".Str::studly($domain)."/{$relativePath}");

        if (File::exists($outputPath)) {
            $this->error("Test file already exists: {$outputPath}");
            return;
        }

        File::ensureDirectoryExists(dirname($outputPath));

        $namespace = "Tests\\".Str::studly($domain)."\\".Str::replace('/', '\\', dirname($name));
        $class = class_basename($name).'Test';

        $stub = File::get($stubPath);
        $rendered = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $class],
            $stub
        );

        File::put($outputPath, $rendered);

        $this->info("Test created at: {$outputPath}");
    }
}
