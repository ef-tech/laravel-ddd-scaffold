<?php

namespace EfTech\DddScaffold\Traits;

use Illuminate\Support\Facades\File;

trait DeletesGitkeepFiles
{
    /**
     * Recursively delete .gitkeep files from a directory and its parent directories.
     *
     * @param  string  $directory  The directory to start deleting .gitkeep files from
     * @param  string|null  $stopAt  The directory to stop at (will not delete .gitkeep files above this directory)
     * @return void
     */
    protected function deleteGitkeepFilesRecursively(string $directory, ?string $stopAt = null): void
    {
        // Normalize paths to ensure consistent comparison
        $directory = rtrim($directory, '/');
        $stopAt = $stopAt ? rtrim($stopAt, '/') : null;

        // Stop if we've reached the stop directory or the root directory
        if ($stopAt && $directory === $stopAt) {
            return;
        }

        // Delete .gitkeep in the current directory
        $gitkeepPath = $directory.'/.gitkeep';
        if (File::exists($gitkeepPath)) {
            File::delete($gitkeepPath);
        }

        // Move up to the parent directory and continue recursively
        $parentDirectory = dirname($directory);

        // Stop if we've reached the root directory or if parent is the same as current
        // (which can happen at the filesystem root)
        if ($parentDirectory !== $directory) {
            $this->deleteGitkeepFilesRecursively($parentDirectory, $stopAt);
        }
    }
}
