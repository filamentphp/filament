<?php

namespace Filament\Support\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    use CanManipulateFiles;

    protected $signature = 'filament:install {--scaffold} {--actions} {--app} {--forms} {--notifications} {--tables} {--widgets} {--F|force}';

    protected $description = 'Install Filament.';

    public function __invoke(): int
    {
        if ($this->option('app')) {
            $this->installDefaultAppContext();
        }

        if ($this->option('scaffold')) {
            $this->installScaffolding();
        }

        $this->call(UpgradeCommand::class);

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

    protected function installDefaultAppContext(): void
    {
        $path = app_path('Providers/Filament/AdminContextProvider.php');

        if (! $this->option('force') && $this->checkForCollision([$path])) {
            return;
        }

        $this->copyStubToApp('AdminContextProvider', $path);

        if (! Str::contains($appConfig = file_get_contents(config_path('app.php')), 'App\\Providers\\Filament\\AdminContextProvider::class')) {
            file_put_contents(config_path('app.php'), str_replace(
                'App\\Providers\\RouteServiceProvider::class,' . PHP_EOL,
                'App\\Providers\\Filament\\AdminContextProvider::class,' . PHP_EOL . '        App\\Providers\\RouteServiceProvider::class,' . PHP_EOL,
                $appConfig,
            ));
        }

        $this->components->info('Successfully created AdminContextProvider.php!');
    }

    protected function installScaffolding(): void
    {
        static::updateNpmPackages();

        $filesystem = app(Filesystem::class);
        $filesystem->delete(resource_path('js/bootstrap.js'));
        $filesystem->copyDirectory(__DIR__ . '/../../stubs/scaffolding', base_path());

        // Uninstall the filament/forms CSS
        if (
            (! $this->option('actions')) &&
            (! $this->option('forms')) &&
            (! $this->option('tables'))
        ) {
            $css = $filesystem->get(resource_path('css/app.css'));
            $css = (string) str($css)
                ->replace('@import \'../../vendor/filament/forms/dist/index.css\';', '')
                ->trim();
            $filesystem->put(resource_path('css/app.css'), $css);
        }

        // Install filament/notifications into the layout Blade file
        if (
            $this->option('actions') ||
            $this->option('notifications') ||
            $this->option('tables')
        ) {
            $layout = $filesystem->get(resource_path('views/layouts/app.blade.php'));
            $layout = (string) str($layout)
                ->replace('{{ $slot }}', '{{ $slot }}' . PHP_EOL . PHP_EOL . '        @livewire(\'notifications\')');
            $filesystem->put(resource_path('views/layouts/app.blade.php'), $layout);
        }

        $this->components->info('Scaffolding installed successfully.');

        $this->components->info('Please run `npm install && npm run dev` to compile your new assets.');
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
     * @param  array<string, string>  $packages
     * @return array<string, string>
     */
    protected static function updateNpmPackageArray(array $packages): array
    {
        return array_merge(
            [
                '@tailwindcss/forms' => '^0.5.2',
                '@tailwindcss/typography' => '^0.5.4',
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

    protected function installUpgradeCommand(): void
    {
        $path = base_path('composer.json');

        if (! file_exists($path)) {
            return;
        }

        $configuration = json_decode(file_get_contents($path), associative: true);

        $command = '@php artisan filament:upgrade';

        if (in_array($command, $configuration['scripts']['post-update-cmd'] ?? [])) {
            return;
        }

        $configuration['scripts']['post-update-cmd'] ??= [];
        $configuration['scripts']['post-update-cmd'][] = $command;

        file_put_contents(
            $path,
            str_replace(
                search: "    \"keywords\": [\n        \"framework\",\n        \"laravel\"\n    ],",
                replace: '    "keywords": ["framework", "laravel"],',
                subject: json_encode($configuration, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            ),
        );
    }
}
