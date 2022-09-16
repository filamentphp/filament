<?php

namespace Filament\Support\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Finder\SplFileInfo;

class CheckMissingTranslationsCommand extends Command
{
    protected $signature = 'filament:check-translations
                            {lang*              : The lang to compare against the base translation. Accepts multiple values.}
                            {--D|dir=app        : The directory containing the translations. \'app\' and \'vendor\' are accepted values.}';

    protected $description = 'Scan translation files and compare each of them with the base translation. Outputs missing and deprecated translations.';

    public function handle()
    {
        $this->scan('filament');
        $this->scan('forms');
        $this->scan('support');
        $this->scan('tables');
        $this->scan('notifications');

        return self::SUCCESS;
    }

    protected function scan(string $package)
    {
        $langRoot = match ($dir = $this->option('dir')) {
            'app' => lang_path("vendor/{$package}"),
            'vendor' => base_path("vendor/filament/{$package}/resources/lang"),
            default => throw new InvalidOptionException("{$dir} is not a valid 'dir' option.")
        };

        $filesystem = app(Filesystem::class);

        if (! $filesystem->exists($langRoot)) {
            return;
        }

        $langs = collect($filesystem->directories($langRoot))
            ->mapWithKeys(static fn ($directory) => [$directory => (string) str($directory)->afterLast('/')])
            ->when(
                $langs = $this->argument('lang'),
                fn (Collection $availableLangs) => $availableLangs
                    ->filter(
                        fn ($lang) => in_array($lang, $langs)
                    )
            )
            ->all();

        foreach ($langs as $langDir => $lang) {
            $files = $filesystem->files($langDir);

            collect($files)
                ->mapWithKeys(function (SplFileInfo $file) use ($langRoot) {
                    $base = require implode(DIRECTORY_SEPARATOR, [$langRoot, 'en', $file->getRelativePathname()]);
                    $trans = require $file->getPathname();

                    $missingKeys = array_diff_key(
                        Arr::dot($base),
                        Arr::dot($trans)
                    );

                    $removedKeys = array_diff_key(
                        Arr::dot($trans),
                        Arr::dot($base)
                    );

                    return [
                        $file->getPathname() => [
                            'missing' => array_keys($missingKeys),
                            'removed' => array_keys($removedKeys),
                        ],
                    ];
                })
                ->tap(function ($files) use ($lang, $package) {
                    $missingCount = $files->sum(fn ($file) => count($file['missing']));
                    $removedCount = $files->sum(fn ($file) => count($file['removed']));

                    if ($missingCount == 0 && $removedCount == 0) {
                        $this->info("Package filament/{$package} has no misssing or deprecated translation keys for lang {$lang}!\n");
                    } elseif ($missingCount > 0 && $removedCount > 0) {
                        $this->warn("Package filament/{$package} has {$missingCount} misssing translation keys and {$removedCount} removed translation keys for lang {$lang}.\n");
                    } elseif ($missingCount > 0) {
                        $this->warn("Package filament/{$package} has {$missingCount} misssing translation keys for lang {$lang}.\n");
                    } elseif ($removedCount > 0) {
                        $this->warn("Package filament/{$package} has {$removedCount} removed translation keys for lang {$lang}.\n");
                    }
                })
                ->filter(static fn ($keys) => count($keys['missing']) || count($keys['removed']))
                ->each(function ($keys, $file) {
                    $this->table(
                        ['File', 'Missing Keys', 'Removed keys'],
                        [
                            [$file, implode(', ', $keys['missing']), implode(', ', $keys['removed'])],
                        ],
                    );
                });
        }
    }
}
