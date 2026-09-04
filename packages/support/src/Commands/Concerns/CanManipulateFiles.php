<?php

namespace Filament\Support\Commands\Concerns;

use Filament\Support\Commands\FileGenerators\Contracts\FileGenerator;
use Filament\Support\Commands\FileGenerators\Contracts\HasSkippedColumns;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use ReflectionClass;

use function Laravel\Prompts\confirm;

trait CanManipulateFiles
{
    /**
     * Columns already reported to the user as skipped, to avoid warning about the same column more than once per command.
     *
     * @var array<string, string>
     */
    protected array $reportedSkippedColumns = [];

    /**
     * @param  string | array<string>  $paths
     */
    protected function checkForCollision(string | array $paths): bool
    {
        foreach (Arr::wrap($paths) as $path) {
            if (! $this->fileExists($path)) {
                continue;
            }

            if (
                (! app()->runningUnitTests()) &&
                (! confirm(basename($path) . ' already exists, do you want to overwrite it?'))
            ) {
                $this->components->error("{$path} already exists, aborting.");

                return true;
            }

            unlink($path);
        }

        return false;
    }

    /**
     * @param  array<string, string>  $replacements
     */
    protected function copyStubToApp(string $stub, string $targetPath, array $replacements = []): void
    {
        $filesystem = app(Filesystem::class);

        if (! $this->fileExists($stubPath = base_path("stubs/filament/{$stub}.stub"))) {
            $stubPath = $this->getDefaultStubPath() . "/{$stub}.stub";
        }

        $stub = str($filesystem->get($stubPath));

        foreach ($replacements as $key => $replacement) {
            $stub = $stub->replace("{{ {$key} }}", $replacement);
        }

        $stub = (string) $stub;

        $this->writeFile($targetPath, $stub);
    }

    protected function fileExists(string $path): bool
    {
        $filesystem = app(Filesystem::class);

        return $filesystem->exists($path);
    }

    protected function escapeCssString(string $value): string
    {
        return (string) preg_replace_callback(
            '/[\x00-\x1F\x7F]/',
            static fn (array $matches): string => '\\' . dechex(ord($matches[0])) . ' ',
            str_replace(['\\', "'"], ['\\\\', "\\'"], $value),
        );
    }

    protected function getRelativePath(string $path, string $from): string
    {
        $path = $this->normalizePath($path);
        $from = $this->normalizePath($from);
        $pathSegments = explode('/', ltrim($path, '/'));
        $fromSegments = explode('/', ltrim($from, '/'));
        $isPathOnWindowsDrive = preg_match('/^[a-z]:\//i', $path) === 1;
        $isFromOnWindowsDrive = preg_match('/^[a-z]:\//i', $from) === 1;
        $isPathOnNetworkShare = str_starts_with($path, '//');
        $isFromOnNetworkShare = str_starts_with($from, '//');

        if (
            ($isPathOnWindowsDrive || $isFromOnWindowsDrive) &&
            (
                ! ($isPathOnWindowsDrive && $isFromOnWindowsDrive) ||
                (strcasecmp($pathSegments[0], $fromSegments[0]) !== 0)
            )
        ) {
            return $path;
        }

        if (
            ($isPathOnNetworkShare || $isFromOnNetworkShare) &&
            (
                ! ($isPathOnNetworkShare && $isFromOnNetworkShare) ||
                (strcasecmp($pathSegments[0], $fromSegments[0]) !== 0) ||
                (strcasecmp($pathSegments[1], $fromSegments[1]) !== 0)
            )
        ) {
            return $path;
        }

        if (str_starts_with($path, '/') !== str_starts_with($from, '/')) {
            return $path;
        }

        $isCaseInsensitive = $isPathOnWindowsDrive || $isPathOnNetworkShare;

        while (
            filled($pathSegments) &&
            filled($fromSegments) &&
            ($isCaseInsensitive
                ? (strcasecmp($pathSegments[0], $fromSegments[0]) === 0)
                : ($pathSegments[0] === $fromSegments[0]))
        ) {
            array_shift($pathSegments);
            array_shift($fromSegments);
        }

        return str_repeat('../', count($fromSegments)) . implode('/', $pathSegments);
    }

    protected function normalizePath(string $path): string
    {
        $isNetworkPath = str_starts_with($path, '\\\\') || str_starts_with($path, '//');
        $isAbsolute = str_starts_with($path, '/') || str_starts_with($path, '\\');
        $normalizedSegments = [];

        foreach (explode('/', str_replace('\\', '/', $path)) as $segment) {
            if (($segment === '') || ($segment === '.')) {
                continue;
            }

            if (($segment === '..') && filled($normalizedSegments) && (array_last($normalizedSegments) !== '..')) {
                array_pop($normalizedSegments);

                continue;
            }

            $normalizedSegments[] = $segment;
        }

        return ($isNetworkPath ? '//' : ($isAbsolute ? '/' : '')) . implode('/', $normalizedSegments);
    }

    protected function writeFile(string $path, string | FileGenerator $contents): void
    {
        $filesystem = app(Filesystem::class);

        $filesystem->ensureDirectoryExists(
            pathinfo($path, PATHINFO_DIRNAME),
        );

        if ($contents instanceof FileGenerator) {
            $filesystem->put($path, $contents->generate());

            if ($contents instanceof HasSkippedColumns) {
                $this->reportSkippedColumns($contents->getSkippedColumns());
            }

            return;
        }

        $filesystem->put($path, $contents);
    }

    /**
     * @param  array<string, string>  $skippedColumns  Column names, keyed by name with their database type as the value.
     */
    protected function reportSkippedColumns(array $skippedColumns): void
    {
        $newlySkippedColumns = array_diff_key($skippedColumns, $this->reportedSkippedColumns);

        if (empty($newlySkippedColumns)) {
            return;
        }

        $this->reportedSkippedColumns = [
            ...$this->reportedSkippedColumns,
            ...$newlySkippedColumns,
        ];

        $columnsList = collect($newlySkippedColumns)
            ->map(fn (string $type, string $name): string => "{$name} ({$type})")
            ->join(', ');

        $this->components->warn("Binary columns were skipped, as they cannot be displayed in the browser: {$columnsList}. Add them to your model's `\$hidden` property to prevent errors.");
    }

    protected function getDefaultStubPath(): string
    {
        $reflectionClass = new ReflectionClass($this);

        return (string) str($reflectionClass->getFileName())
            ->beforeLast('Commands')
            ->append('../stubs');
    }
}
