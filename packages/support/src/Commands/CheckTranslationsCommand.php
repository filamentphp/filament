<?php

namespace Filament\Support\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Finder\SplFileInfo;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

#[AsCommand(name: 'filament:check-translations')]
class CheckTranslationsCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'filament:check-translations
                            {locales* : The locales to check.}
                            {--source=vendor : The directory containing the translations to check - either \'vendor\' or \'app\'.}';

    protected $description = 'Check for missing and removed translations';

    public function handle(): int
    {
        $this->scan('filament');
        $this->scan('actions');
        $this->scan('forms');
        $this->scan('infolists');
        $this->scan('notifications');
        $this->scan('spark-billing-provider');
        $this->scan('spatie-laravel-google-fonts-plugin');
        $this->scan('spatie-laravel-media-library-plugin');
        $this->scan('spatie-laravel-settings-plugin');
        $this->scan('spatie-laravel-tags-plugin');
        $this->scan('spatie-laravel-translatable-plugin');
        $this->scan('support');
        $this->scan('tables');
        $this->scan('widgets');

        return self::SUCCESS;
    }

    protected function scan(string $package): void
    {
        $localeRootDirectory = match ($source = $this->option('source')) {
            'app' => lang_path("vendor/{$package}"),
            'vendor' => base_path("vendor/filament/{$package}/resources/lang"),
            default => throw new InvalidOptionException("{$source} is not a valid translation source. Must be `vendor` or `app`.")
        };

        $filesystem = app(Filesystem::class);

        if (! $filesystem->exists($localeRootDirectory)) {
            return;
        }

        collect($filesystem->directories($localeRootDirectory))
            ->mapWithKeys(static fn (string $directory): array => [$directory => (string) str($directory)->afterLast(DIRECTORY_SEPARATOR)])
            ->when(
                $locales = $this->argument('locales'),
                fn (Collection $availableLocales): Collection => $availableLocales->filter(fn (string $locale): bool => in_array($locale, $locales))
            )
            ->each(function (string $locale, string $localeDir) use ($filesystem, $localeRootDirectory, $package) {
                $files = $filesystem->allFiles($localeDir);
                $baseFiles = $filesystem->allFiles(implode(DIRECTORY_SEPARATOR, [$localeRootDirectory, 'en']));

                $localeFiles = collect($files)->map(fn ($file) => (string) str($file->getPathname())->after(DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR));
                $englishFiles = collect($baseFiles)->map(fn ($file) => (string) str($file->getPathname())->after(DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR));
                $missingFiles = $englishFiles->diff($localeFiles);
                $removedFiles = $localeFiles->diff($englishFiles);
                $path = implode(DIRECTORY_SEPARATOR, [$localeRootDirectory, $locale]);

                if ($missingFiles->count() > 0 && $removedFiles->count() > 0) {
                    warning("[!] Package filament/{$package} has {$missingFiles->count()} missing translation " . Str::plural('file', $missingFiles->count()) . " and {$removedFiles->count()} removed translation " . Str::plural('file', $missingFiles->count()) . ' for ' . locale_get_display_name($locale, 'en') . ".\n");
                } elseif ($missingFiles->count() > 0) {
                    warning("[!] Package filament/{$package} has {$missingFiles->count()} missing translation " . Str::plural('file', $missingFiles->count()) . ' for ' . locale_get_display_name($locale, 'en') . ".\n");
                } elseif ($removedFiles->count() > 0) {
                    warning("[!] Package filament/{$package} has {$removedFiles->count()} removed translation " . Str::plural('file', $removedFiles->count()) . ' for ' . locale_get_display_name($locale, 'en') . ".\n");
                }

                if ($missingFiles->count() > 0 || $removedFiles->count() > 0) {
                    table(
                        [$path, ''],
                        array_merge(
                            array_map(fn (string $file): array => [$file, 'Missing'], $missingFiles->toArray()),
                            array_map(fn (string $file): array => [$file, 'Removed'], $removedFiles->toArray()),
                        ),
                    );
                }

                collect($files)
                    ->reject(function ($file) use ($localeRootDirectory) {
                        return ! file_exists(implode(DIRECTORY_SEPARATOR, [$localeRootDirectory, 'en', $file->getRelativePathname()]));
                    })
                    ->mapWithKeys(function (SplFileInfo $file) use ($localeDir, $localeRootDirectory) {
                        $expectedKeys = require implode(DIRECTORY_SEPARATOR, [$localeRootDirectory, 'en', $file->getRelativePathname()]);
                        $actualKeys = require $file->getPathname();

                        return [
                            (string) str($file->getPathname())->after("{$localeDir}/") => [
                                'missing' => array_keys(array_diff_key(
                                    Arr::dot($expectedKeys),
                                    Arr::dot($actualKeys)
                                )),
                                'removed' => array_keys(array_diff_key(
                                    Arr::dot($actualKeys),
                                    Arr::dot($expectedKeys)
                                )),
                            ],
                        ];
                    })
                    ->tap(function (Collection $files) use ($locale, $package) {
                        $missingKeysCount = $files->sum(fn ($file): int => count($file['missing']));
                        $removedKeysCount = $files->sum(fn ($file): int => count($file['removed']));

                        $locale = locale_get_display_name($locale, 'en');

                        if ($missingKeysCount == 0 && $removedKeysCount == 0) {
                            info("[✓] Package filament/{$package} has no missing or removed translation keys for {$locale}!\n");
                        } elseif ($missingKeysCount > 0 && $removedKeysCount > 0) {
                            warning("[!] Package filament/{$package} has {$missingKeysCount} missing translation " . Str::plural('key', $missingKeysCount) . " and {$removedKeysCount} removed translation " . Str::plural('key', $removedKeysCount) . " for {$locale}.\n");
                        } elseif ($missingKeysCount > 0) {
                            warning("[!] Package filament/{$package} has {$missingKeysCount} missing translation " . Str::plural('key', $missingKeysCount) . " for {$locale}.\n");
                        } else {
                            warning("[!] Package filament/{$package} has {$removedKeysCount} removed translation " . Str::plural('key', $removedKeysCount) . " for {$locale}.\n");
                        }
                    })
                    ->filter(static fn ($keys): bool => count($keys['missing']) || count($keys['removed']))
                    ->each(function ($keys, string $file) {
                        table(
                            [$file, ''],
                            [
                                ...array_map(fn (string $key): array => [$key, 'Missing'], $keys['missing']),
                                ...array_map(fn (string $key): array => [$key, 'Removed'], $keys['removed']),
                            ],
                        );
                    });
            });
    }
}
