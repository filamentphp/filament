<?php

namespace Filament\Forms;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class FormsPreset
{
    public const NPM_PACKAGES_TO_ADD = [
        '@tailwindcss/forms' => '^0.3',
        '@tailwindcss/typography' => '^0.4',
        'alpinejs' => '^3.4',
        'tailwindcss' => '^2.2',
    ];

    public const NPM_PACKAGES_TO_REMOVE = [
        'axios',
        'lodash',
    ];

    public static function install(): void
    {
        static::updatePackages();

        $filesystem = new Filesystem();
        $filesystem->delete(resource_path('js/bootstrap.js'));
        $filesystem->copyDirectory(__DIR__ . '/../stubs', base_path());
    }

    protected static function updatePackages(bool $dev = true): void
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = static::updatePackageArray(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : []
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    protected static function updatePackageArray(array $packages): array
    {
        return array_merge(
            static::NPM_PACKAGES_TO_ADD,
            Arr::except($packages, static::NPM_PACKAGES_TO_REMOVE)
        );
    }
}
