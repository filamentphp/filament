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
            $this->ignorePublishedAssets();
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
            $layout = $filesystem->get(resource_path('views/layouts/app.blade.php'));
            $layout = (string) str($layout)
                ->replace('{{ $slot }}', '{{ $slot }}' . PHP_EOL . PHP_EOL . '        @livewire(\'notifications\')');
            $filesystem->put(resource_path('views/layouts/app.blade.php'), $layout);

            $hasNotifications = true;
        }

        $cssDirectory = resource_path('css');
        $packagesCssImports = collect([
            'support',
            'actions',
            'forms',
            'infolists',
            ...($hasNotifications ? ['notifications'] : []),
            'schemas',
            'tables',
            'widgets',
        ])
            ->filter(fn (string $package): bool => InstalledVersions::isInstalled("filament/{$package}"))
            ->map(function (string $package) use ($cssDirectory): string {
                $packageCssPath = $this->getRelativePath(
                    (string) InstalledVersions::getInstallPath("filament/{$package}") . '/resources/css/index.css',
                    $cssDirectory,
                );

                return "@import '{$this->escapeCssString($packageCssPath)}';";
            })
            ->implode(PHP_EOL);

        $paginationViewsPath = $this->escapeCssString(
            $this->getRelativePath(
                (string) InstalledVersions::getInstallPath('laravel/framework') . '/src/Illuminate/Pagination/resources/views',
                $cssDirectory,
            ),
        );

        $css = $filesystem->get(resource_path('css/app.css'));
        $css = (string) str($css)
            ->replace('{{ filamentCssImports }}', $packagesCssImports)
            ->replace('{{ laravelPaginationViewsPath }}', $paginationViewsPath);
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

        if (! is_array($configuration)) {
            $this->components->warn('Could not update [composer.json] because it could not be parsed as JSON. Please add "@php artisan filament:upgrade" to the "post-autoload-dump" scripts manually.');

            return;
        }

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

    protected function ignorePublishedAssets(): void
    {
        $path = base_path('.gitignore');

        if (! file_exists($path)) {
            return;
        }

        $contents = file_get_contents($path);

        preg_match('/\r\n|\n|\r/', $contents, $lineEndingMatches);

        $lineEnding = $lineEndingMatches[0] ?? PHP_EOL;

        $lines = preg_split('/\r\n|\n|\r/', $contents);

        $existingRules = collect($lines)
            ->map(fn (string $rule): string => trim(rtrim($rule), '/'))
            ->all();

        $assetsPath = trim((string) config('filament.assets_path'), '/');

        $newRules = collect(['css', 'fonts', 'js'])
            ->map(fn (string $directory): string => collect(['public', $assetsPath, $directory, 'filament'])
                ->filter()
                ->implode('/'))
            ->reject(fn (string $rule): bool => in_array($rule, $existingRules))
            ->all();

        if (blank($newRules)) {
            return;
        }

        foreach ($newRules as $newRule) {
            $previousRule = null;
            $previousRuleIndex = null;

            foreach ($lines as $lineIndex => $line) {
                $rule = trim(rtrim($line), '/');

                if (
                    (! str_starts_with($rule, 'public/')) ||
                    (strcmp($rule, $newRule) >= 0) ||
                    (($previousRule !== null) && (strcmp($rule, $previousRule) <= 0))
                ) {
                    continue;
                }

                $previousRule = $rule;
                $previousRuleIndex = $lineIndex;
            }

            if ($previousRuleIndex !== null) {
                array_splice($lines, $previousRuleIndex + 1, 0, ["/{$newRule}"]);

                continue;
            }

            while (($lines !== []) && (end($lines) === '')) {
                array_pop($lines);
            }

            if ($lines !== []) {
                $lines[] = '';
            }

            $lines[] = "/{$newRule}";
        }

        if (end($lines) !== '') {
            $lines[] = '';
        }

        file_put_contents(
            $path,
            implode($lineEnding, $lines),
        );

        $this->components->info('Added the published Filament assets to [.gitignore].');
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
