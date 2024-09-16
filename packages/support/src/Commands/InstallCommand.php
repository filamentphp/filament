<?php

namespace Filament\Support\Commands;

use Filament\PanelProvider;
use Filament\Support\Commands\Concerns\CanGeneratePanels;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;

#[AsCommand(name: 'filament:install')]
class InstallCommand extends Command
{
    use CanGeneratePanels;
    use CanManipulateFiles;

    protected $signature = 'filament:install {--scaffold} {--actions} {--forms} {--infolists} {--notifications} {--panels} {--tables} {--widgets} {--F|force}';

    protected $description = 'Install Filament.';

    public function __invoke(): int
    {
        if ($this->option('panels')) {
            if (! $this->installAdminPanel()) {
                return static::FAILURE;
            }
        }

        if ($this->option('scaffold')) {
            $this->installScaffolding();
        }

        $this->call(UpgradeCommand::class);

        $this->installUpgradeCommand();

        $this->askToStar();

        return static::SUCCESS;
    }

    protected function askToStar(): void
    {
        if ($this->option('no-interaction')) {
            return;
        }

        if (confirm(
            label: 'All done! Would you like to show some love by starring the Filament repo on GitHub?',
            default: true,
        )) {
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
    }

    protected function installAdminPanel(): bool
    {
        if (! class_exists(PanelProvider::class)) {
            $this->components->error('Please require [filament/filament] before attempting to install the Panel Builder.');

            return false;
        }

        return $this->generatePanel(default: 'admin', force: $this->option('force'));
    }

    protected function installScaffolding(): void
    {
        static::updateNpmPackages();

        $filesystem = app(Filesystem::class);
        $filesystem->delete(resource_path('js/bootstrap.js'));
        $filesystem->copyDirectory(__DIR__ . '/../../stubs/scaffolding', base_path());

        // Install filament/notifications into the layout Blade file
        if (
            $this->option('actions') ||
            $this->option('notifications') ||
            $this->option('tables')
        ) {
            $layout = $filesystem->get(resource_path('views/components/layouts/app.blade.php'));
            $layout = (string) str($layout)
                ->replace('{{ $slot }}', '{{ $slot }}' . PHP_EOL . PHP_EOL . '        @livewire(\'notifications\')');
            $filesystem->put(resource_path('views/components/layouts/app.blade.php'), $layout);
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
        return [
            '@tailwindcss/forms' => '^0.5.2',
            '@tailwindcss/typography' => '^0.5.4',
            'autoprefixer' => '^10.4.7',
            'postcss' => '^8.4.14',
            'postcss-nesting' => '^12.0.1',
            'tailwindcss' => '^3.1',
            ...Arr::except($packages, [
                'axios',
                'lodash',
            ]),
        ];
    }

    protected function installUpgradeCommand(): void
    {
        $path = base_path('composer.json');

        if (! file_exists($path)) {
            return;
        }

        $configuration = json_decode(file_get_contents($path), associative: true);

        $command = '@php artisan filament:upgrade';

        if (in_array($command, $configuration['scripts']['post-autoload-dump'] ?? [])) {
            return;
        }

        $configuration['scripts']['post-autoload-dump'] ??= [];
        $configuration['scripts']['post-autoload-dump'][] = $command;

        file_put_contents(
            $path,
            (string) str(json_encode($configuration, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
                ->append(PHP_EOL)
                ->replace(
                    search: "    \"keywords\": [\n        \"laravel\",\n        \"framework\"\n    ],",
                    replace: '    "keywords": ["laravel", "framework"],',
                )
                ->replace(
                    search: "    \"keywords\": [\n        \"framework\",\n        \"laravel\"\n    ],",
                    replace: '    "keywords": ["framework", "laravel"],',
                ),
        );
    }
}
