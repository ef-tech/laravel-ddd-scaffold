<?php

namespace EfTech\DddScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InitCommand extends Command
{
    protected $signature = 'ddd:init {name=myproject}';
    protected $description = 'Generate base DDD structure for your project';

    public function handle()
    {
        $base = base_path($this->argument('name'));

        $directories = [
            '/Application/DTOs',
            '/Application/Exceptions',
            '/Application/Services',
            '/Application/UseCases',
            '/Domain/Exceptions',
            '/Domain/Models',
            '/Domain/Repositories',
            '/Domain/Rules',
            '/Domain/Services',
            '/Domain/ValueObjects',
            '/Infrastructure/Exceptions',
            '/Infrastructure/Repositories',
            '/Infrastructure/Services',
            '/Support/Contracts',
            '/Support/Enum',
            '/Support/Exceptions',
        ];

        foreach ($directories as $dir) {
            $fullPath = $base . $dir;
            File::ensureDirectoryExists($fullPath);
        }

        $this->info('✅ DDD structure scaffolding complete!');
    }
}
