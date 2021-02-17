<?php

namespace Filament\Commands\Concerns;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

trait CanManipulateFiles
{
    protected function checkForCollision($paths)
    {
        $filesystem = new Filesystem();

        foreach ($paths as $path) {
            if ($filesystem->exists($path)) {
                $this->error("$path already exists, aborting.");

                return true;
            }
        }

        return false;
    }

    protected function copyStubToApp($stub, $targetPath, $replacements = [])
    {
        $filesystem = new Filesystem();

        $stub = Str::of($filesystem->get(__DIR__ . "/../../../stubs/{$stub}.stub"));

        foreach ($replacements as $key => $replacement) {
            $stub = $stub->replace("{{{$key}}}", $replacement);
        }

        $stub = (string) $stub;

        $this->writeFile($targetPath, $stub);
    }

    protected function writeFile($path, $contents)
    {
        $filesystem = new Filesystem();

        $filesystem->ensureDirectoryExists(
            (string) Str::of($path)
                ->beforeLast('/'),
        );

        $filesystem->put($path, $contents);
    }
}
