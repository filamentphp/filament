<?php

namespace Filament\Support\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Finder\SplFileInfo;
use function Termwind\{render};

class CheckMissingTranslationsCommand extends Command
{
    protected $signature = 'filament:check-translations
                            {lang*              : The lang to compare against the base translation. Accepts multiple values.}
                            {--B|baseLang=en    : The base lang to compare with.}
                            {--D|dir=app        : The directory containing the translations. \'app\' and \'vendor\' are accepted values.}';

    protected $description = 'Scan translation files and compare each of them with the base translation. Outputs missing and deprecated translations.';

    public function handle()
    {
        $this->scan('filament');
        $this->scan('forms');
        $this->scan('support');
        $this->scan('tables');

        return self::SUCCESS;
    }

    protected function scan(string $package)
    {
        $baseLang = $this->option('baseLang');

        $langRoot = match ($dir = $this->option('dir')) {
            'app' => lang_path("vendor/{$package}"),
            'vendor' => base_path("vendor/filament/{$package}/resources/lang"),
            default => throw new InvalidOptionException("{$dir} is not a valid 'dir' option.")
        };

        $filesystem = app(Filesystem::class);

        if (! $filesystem->exists($langRoot)) {
            return;
        }

        $this->announceScanning($package);

        $langs = collect($filesystem->directories($langRoot))
            ->mapWithKeys(static fn ($directory) => [$directory => (string) str($directory)->afterLast('/')])
            ->when($this->argument('lang'), fn (Collection $langs) => $langs->filter(fn ($lang) => in_array($lang, $this->argument('lang'))))
            ->all();

        foreach ($langs as $langDir => $lang) {
            $files = $filesystem->files($langDir);

            collect($files)
                ->mapWithKeys(function (SplFileInfo $file) use ($langRoot, $baseLang) {
                    $base = require $langRoot . DIRECTORY_SEPARATOR . $baseLang . DIRECTORY_SEPARATOR . $file->getRelativePathname();
                    $trans = require $file->getPathname();

                    $missingKeys = array_diff_key(
                        Arr::dot($base),
                        Arr::dot($trans)
                    );

                    $deprecatedKeys = array_diff_key(
                        Arr::dot($trans),
                        Arr::dot($base)
                    );

                    return [
                        $file->getPathname() => [
                            'missing' => array_keys($missingKeys),
                            'deprecated' => array_keys($deprecatedKeys),
                        ],
                    ];
                })
                ->tap(function ($files) use ($lang) {
                    $missingCount = $files->sum(fn ($file) => count($file['missing']));
                    $deprecatedCount = $files->sum(fn ($file) => count($file['deprecated']));

                    if ($missingCount === 0 && $deprecatedCount === 0) {
                        $this->banner('success', 'Lang %s has no misssing or deprecated translation keys!', $lang);
                    } else {
                        $this->banner('warning', 'Lang %s has %d misssing translation keys and %d deprecated translation keys.', $lang, $missingCount, $deprecatedCount);
                    }
                })
                ->filter(static fn ($keys) => count($keys['missing']) || count($keys['deprecated']))
                ->each(function ($keys, $file) {
                    $this->table(
                        ['File', 'Missing Keys', 'Deprecated keys'],
                        [
                            [$file, implode(', ', $keys['missing']), implode(', ', $keys['deprecated'])],
                        ],
                    );
                });
        }
    }

    protected function announceScanning(string $package): void
    {
        render(<<<HTML
            <div class="px-2 py-4 mt-2 text-white bg-sky-600">
                Scanning for missing and deprecated translations inside <span class="font-bold bg-white text-sky-600">&nbsp;filament/{$package}&nbsp;</span> package.
            </div>
        HTML);
    }

    protected function banner(string $style, string $message, ...$replacements): void
    {
        $color = match ($style) {
            'success' => 'green',
            'warning' => 'orange',
            'danger' => 'red',
            default => 'blue'
        };

        $content = sprintf($message, ...$replacements);

        render(<<<HTML
            <div class="px-2 py-4 text-white bg-{$color}-600">
                {$content}
            </div>
        HTML);
    }
}
