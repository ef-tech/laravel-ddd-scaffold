<?php

namespace EfTech\DddScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InitCommand extends Command
{
    protected $signature = 'ddd:init {name=MyProject}';
    protected $description = 'Generate base DDD structure for your project';

    public function handle()
    {
        $name = $this->argument('name');
        $base = base_path($name);

        $directories = [
            '/Application/DTOs',
            '/Application/Enums',
            '/Application/Exceptions',
            '/Application/Mappers',
            '/Application/Presenters',
            '/Application/Queries',
            '/Application/Services',
            '/Application/UseCases',
            '/Domain/Aggregates',
            '/Domain/Entities',
            '/Domain/Exceptions',
            '/Domain/Repositories',
            '/Domain/Rules',
            '/Domain/Services',
            '/Domain/ValueObjects',
            '/Infrastructure/Enums',
            '/Infrastructure/Exceptions',
            '/Infrastructure/Mappers',
            '/Infrastructure/Repositories',
            '/Infrastructure/Services',
            '/Support/Constants',
            '/Support/Enums',
            '/Support/Exceptions',
        ];

        foreach ($directories as $dir) {
            $fullPath = $base.$dir;
            File::ensureDirectoryExists($fullPath);
            $this->createGitkeepFile($fullPath);
        }

        $this->createConfigFile($name);

        $this->info('DDD structure scaffolding complete!');
    }

    private function createGitkeepFile(string $path): void
    {
        $gitkeepPath = $path.'/.gitkeep';
        if (! File::exists($gitkeepPath) && empty(File::files($path))) {
            File::put($gitkeepPath, '');
        }
    }

    private function createConfigFile(string $name): void
    {
        $configPath = config_path('ddd-scaffold.php');

        if (File::exists($configPath)) {
            $this->info("Config file already exists at {$configPath}. Skipping creation.");
            return;
        }

        File::copy(__DIR__.'/../../config/ddd-scaffold.php', $configPath);

        $content = File::get($configPath);
        $content = preg_replace("/'default_domain'\s*=>\s*'.*?'/", "'default_domain' => '{$name}'", $content);
        File::put($configPath, $content);

        $this->info("Config file created at {$configPath} with default_domain set to '{$name}'");
    }
}
