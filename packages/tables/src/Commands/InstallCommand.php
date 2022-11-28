<?php

namespace Filament\Tables\Commands;

use Filament\Support\Commands\Concerns\CanInstallUpgradeCommand;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class InstallCommand extends Command
{
    use CanInstallUpgradeCommand;

    protected $signature = 'tables:install';

    protected $description = 'Set up table builder CSS and JS in a fresh Laravel installation.';

    public function __invoke(): int
    {
        static::updateNpmPackages();

        $filesystem = app(Filesystem::class);
        $filesystem->delete(resource_path('js/bootstrap.js'));
        $filesystem->copyDirectory(__DIR__ . '/../../stubs/scaffolding', base_path());

        $this->components->info('Scaffolding installed successfully.');

        $this->components->info('Please run `npm install && npm run dev` to compile your new assets.');

        $this->installUpgradeCommand();

        if ($this->confirm('All done! Would you like to show some love by starring the Filament repo on GitHub?', true)) {
            if (PHP_OS_FAMILY === 'Darwin') {
                exec('open https://github.com/filamentphp/filament');
            }
            if (PHP_OS_FAMILY === 'Linux') {
                exec('xdg-open https://github.com/filamentphp/filament');
            }
            if (PHP_OS_FAMILY === 'Windows') {
                exec('start https://github.com/filamentphp/filament');
            }

            $this->components->info('Thank you!');
        }

        return static::SUCCESS;
    }

    protected static function updateNpmPackages(bool $dev = true): void
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), associative: true);

        $packages[$configurationKey] = static::updateNpmPackageArray(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : []
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

    /**
     * @param array<string, string> $packages
     * @return array<string, string>
     */
    protected static function updateNpmPackageArray(array $packages): array
    {
        return array_merge(
            [
                '@alpinejs/focus' => '^3.10.3',
                '@tailwindcss/forms' => '^0.5.2',
                '@tailwindcss/typography' => '^0.5.4',
                'alpinejs' => '^3.10.3',
                'autoprefixer' => '^10.4.7',
                'tailwindcss' => '^3.1',
            ],
            Arr::except($packages, [
                'axios',
                'lodash',
            ]),
        );
    }
}
