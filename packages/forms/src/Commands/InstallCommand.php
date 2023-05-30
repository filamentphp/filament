<?php

namespace Filament\Forms\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class InstallCommand extends Command
{
    protected $signature = 'forms:install';

    protected $description = 'Set up form builder CSS and JS in a fresh Laravel installation';

    public function __invoke(): int
    {
        static::updateNpmPackages();

        $filesystem = app(Filesystem::class);
        $filesystem->delete(resource_path('js/bootstrap.js'));
        $filesystem->copyDirectory(__DIR__ . '/../../stubs/scaffolding', base_path());

        $this->info('Scaffolding installed successfully.');

        $this->comment('Please run `npm install && npm run dev` to compile your new assets.');

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

    protected static function updateNpmPackageArray(array $packages): array
    {
        return array_merge(
            [
                '@tailwindcss/forms' => '^0.5.2',
                '@tailwindcss/typography' => '^0.5.4',
                'alpinejs' => '^3.10.3',
                'autoprefixer' => '^10.4.7',
                'postcss' => '^8.4.14',
                'tailwindcss' => '^3.1',
            ],
            Arr::except($packages, [
                'axios',
                'lodash',
            ]),
        );
    }
}
