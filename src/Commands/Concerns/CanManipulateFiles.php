<?php

namespace Filament\Commands\Concerns;

use Illuminate\Support\Str;

trait CanManipulateFiles
{
    protected function checkForCollision($paths)
    {
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $this->error("$path already exists, aborting.");

                return true;
            }
        }

        return false;
    }

    protected function copyStubToApp($stub, $targetPath, $replacements = [])
    {
        $stub = Str::of(file_get_contents(__DIR__ . "/../../../stubs/{$stub}.stub"));

        foreach ($replacements as $key => $replacement) {
            $stub = $stub->replace("{{{$key}}}", $replacement);
        }

        $stub = (string) $stub;

        $this->writeFile($targetPath, $stub);
    }

    protected function writeFile($path, $contents)
    {
        $currentDirectory = '';

        Str::of($path)
            ->explode('/')
            ->slice(0, -1)
            ->each(function ($directory) use (&$currentDirectory) {
                $currentDirectory .= "/{$directory}";

                if (is_dir($currentDirectory)) return;

                mkdir($currentDirectory);
            });

        file_put_contents($path, $contents);
    }
}
