<?php

namespace Filament\Support\Commands;

use Composer\InstalledVersions;
use Filament\PanelProvider;
use Filament\Support\Commands\Concerns\CanGeneratePanels;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanOpenUrlInBrowser;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\confirm;

#[AsCommand(name: 'filament:install', aliases: [
    'install:filament',
])]
class InstallCommand extends Command
{
    use CanGeneratePanels;
    use CanManipulateFiles;
    use CanOpenUrlInBrowser;

    protected $description = 'Install Filament';

    protected $name = 'filament:install';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'install:filament',
    ];

    /**
     * @return array<InputOption>
     */
    protected function getOptions(): array
    {
        return [
            new InputOption(
                name: 'panels',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Install the panel builder and create the first panel',
            ),
            new InputOption(
                name: 'scaffold',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Install the Filament packages for use outside of panels, in your Blade or Livewire application',
            ),
            new InputOption(
                name: 'notifications',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Install the Filament flash notifications into the scaffolded layout file',
            ),
            new InputOption(
                name: 'force',
                shortcut: 'F',
                mode: InputOption::VALUE_NONE,
                description: 'Overwrite the contents of the files if they already exist',
            ),
        ];
    }

    public function __invoke(): int
    {
        try {
            $this->installAdminPanel();
            $this->installScaffolding();
            $this->installUpgradeCommand();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->call(UpgradeCommand::class);

        $this->askToStar();

        return static::SUCCESS;
    }

    protected function installAdminPanel(): void
    {
        if (! $this->option('panels')) {
            return;
        }

        if (! class_exists(PanelProvider::class)) {
            $this->components->error('Please require [filament/filament] before attempting to install the Panel Builder.');

            throw new FailureCommandOutput;
        }

        $this->generatePanel(defaultId: 'admin', isForced: $this->option('force'));
    }

    protected function installScaffolding(): void
    {
        if (! $this->option('scaffold')) {
            return;
        }

        $filesystem = app(Filesystem::class);
        $filesystem->copyDirectory(__DIR__ . '/../../stubs/scaffolding', base_path());

        $hasNotifications = false;

        if (
            InstalledVersions::isInstalled('filament/notifications') &&
            ($this->option('notifications') || confirm(
                label: 'Do you want to send flash notifications using Filament?',
                default: true,
            ))
        ) {
            $layout = $filesystem->get(resource_path('views/components/layouts/app.blade.php'));
            $layout = (string) str($layout)
                ->replace('{{ $slot }}', '{{ $slot }}' . PHP_EOL . PHP_EOL . '        @livewire(\'notifications\')');
            $filesystem->put(resource_path('views/components/layouts/app.blade.php'), $layout);

            $hasNotifications = true;
        }

        $packagesCssImports = collect([
            'actions',
            'forms',
            'infolists',
            ...($hasNotifications ? ['notifications'] : []),
            'schemas',
            'tables',
            'widgets',
        ])
            ->filter(fn (string $package): bool => InstalledVersions::isInstalled("filament/{$package}"))
            ->implode('/resources/css/index.css\';' . PHP_EOL . '@import \'../../vendor/filament/');

        $css = $filesystem->get(resource_path('css/app.css'));
        $css = (string) str($css)->replace(
            '@import \'../../vendor/filament/support/resources/css/index.css\';',
            '@import \'../../vendor/filament/support/resources/css/index.css\';' . PHP_EOL . "@import '../../vendor/filament/{$packagesCssImports}/resources/css/index.css';",
        );
        $filesystem->put(resource_path('css/app.css'), $css);

        $this->components->info('Scaffolding installed successfully.');

        $this->components->info('Please run `npm run build` to compile your new assets.');
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

    protected function askToStar(): void
    {
        if ($this->option('no-interaction')) {
            return;
        }

        if (! confirm(
            label: 'All done! Would you like to show some love by starring the Filament repo on GitHub?',
            default: true,
        )) {
            return;
        }

        $this->openUrlInBrowser('https://github.com/filamentphp/filament');
    }
}
