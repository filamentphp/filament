<?php

namespace Filament\Commands;

use Composer\InstalledVersions;
use Filament\Support\Commands\Concerns\CanConfigureVite;
use Filament\Support\Commands\Concerns\CanManageJavaScriptPackages;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\HasPanel;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Filament\Support\Commands\Exceptions\SuccessCommandOutput;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:filament-theme', aliases: [
    'filament:make-theme',
    'filament:theme',
])]
class MakeThemeCommand extends Command
{
    use CanConfigureVite;
    use CanManageJavaScriptPackages;
    use CanManipulateFiles;
    use HasPanel;

    protected $description = 'Create a new Filament panel theme';

    protected $name = 'make:filament-theme';

    protected Filesystem $filesystem;

    protected string $themePath;

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:make-theme',
        'filament:theme',
    ];

    /**
     * @return array<InputArgument>
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument(
                name: 'panel',
                mode: InputArgument::OPTIONAL,
                description: 'The ID of the panel to create the theme for',
            ),
        ];
    }

    /**
     * @return array<InputOption>
     */
    protected function getOptions(): array
    {
        return [
            new InputOption(
                name: 'panel',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The panel to create the resource in',
            ),
            new InputOption(
                name: 'pm',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The package manager to use (npm, yarn)',
            ),
            new InputOption(
                name: 'skip-install',
                mode: InputOption::VALUE_NONE,
                description: 'Skip installing JavaScript dependencies',
            ),
            new InputOption(
                name: 'skip-build',
                mode: InputOption::VALUE_NONE,
                description: 'Skip building JavaScript assets',
            ),
            new InputOption(
                name: 'force',
                shortcut: 'F',
                mode: InputOption::VALUE_NONE,
                description: 'Overwrite the contents of the files if they already exist',
            ),
        ];
    }

    public function handle(Filesystem $filesystem): int
    {
        $this->filesystem = $filesystem;

        try {
            $this->configurePanel(question: 'Which panel would you like to create this theme for?');
            $this->configurePackageManager();

            $this->themePath = "resources/css/filament/{$this->panel->getId()}/theme.css";

            $this->installDependencies();
            $this->createThemeSourceFiles();

            $this->abortIfNotVite();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        } catch (SuccessCommandOutput) {
            return static::SUCCESS;
        }

        $pendingActions = [];

        // Try to register in `vite.config.js`
        if (! $this->registerInViteConfig()) {
            $pendingActions[] = "Add a new item to the Laravel plugin's `input` array in `vite.config.js`: `{$this->themePath}`.";
        }

        // Try to register in panel provider
        if (! $this->registerInPanelProvider()) {
            $pendingActions[] = "Register the theme in the {$this->panel->getId()} panel provider using `->viteTheme('{$this->themePath}')`";
        }

        // Show pending manual actions if any
        if (count($pendingActions) > 0) {
            $this->components->warn('Action is required to complete the theme setup:');
            $this->components->bulletList($pendingActions);
            $this->newLine();
        }

        return $this->buildJavaScriptAssets('theme') ? static::SUCCESS : static::FAILURE;
    }

    protected function installDependencies(): void
    {
        $this->installJavaScriptDependencies([
            'tailwindcss@latest',
            '@tailwindcss/vite',
        ]);
    }

    protected function createThemeSourceFiles(): void
    {
        $cssFilePath = resource_path("css/filament/{$this->panel->getId()}/theme.css");

        if (! $this->option('force') && $this->checkForCollision([
            $cssFilePath,
        ])) {
            throw new FailureCommandOutput;
        }

        $classDirectory = (string) str(Arr::first($this->panel->getPageDirectories()))
            ->afterLast('Filament/')
            ->beforeLast('Pages');

        $viewDirectory = str($classDirectory)
            ->explode('/')
            ->map(fn ($segment) => Str::lower(Str::kebab($segment)))
            ->implode('/');

        $this->copyStubToApp('ThemeCss', $cssFilePath, [
            'classDirectory' => filled($classDirectory) ? $classDirectory : '',
            'filamentThemeCssPath' => $this->escapeCssString(
                $this->getRelativePath(
                    (string) InstalledVersions::getInstallPath('filament/filament') . '/resources/css/theme.css',
                    dirname($cssFilePath),
                ),
            ),
            'panel' => $this->panel->getId(),
            'viewDirectory' => filled($viewDirectory) ? $viewDirectory : '',
        ]);

        $this->components->info("Filament theme [resources/css/filament/{$this->panel->getId()}/theme.css] created successfully.");
    }

    protected function abortIfNotVite(): void
    {
        if (glob(base_path('vite.config.*s'))) {
            return;
        }

        $panelId = $this->panel->getId();
        $publicPath = "public/css/filament/{$panelId}/theme.css";

        $this->components->warn('Action is required to complete the theme setup:');
        $this->components->bulletList([
            "It looks like you don't have Vite installed. Please use your asset bundling system of choice to compile `{$this->themePath}` into `{$publicPath}`.",
            "If you're not currently using a bundler, we recommend using Vite. Alternatively, you can use the Tailwind CLI with the following command:",
            "npx @tailwindcss/cli --input ./{$this->themePath} --output ./{$publicPath} --minify",
            "Make sure to register the theme in the {$panelId} panel provider using `->theme(asset('css/filament/{$panelId}/theme.css'))`",
        ]);

        throw new SuccessCommandOutput;
    }

    protected function registerInViteConfig(): bool
    {
        return $this->registerViteInput($this->themePath);
    }

    protected function registerInPanelProvider(): bool
    {
        $panelId = $this->panel->getId();

        // Find the panel provider file
        $providerPath = app_path('Providers/Filament/' . Str::studly($panelId) . 'PanelProvider.php');

        if (! $this->filesystem->exists($providerPath)) {
            return false;
        }

        $contents = $this->filesystem->get($providerPath);

        // Check if already registered
        if (str_contains($contents, 'viteTheme(')) {
            $this->components->info('viteTheme() already configured in panel provider.');

            return true;
        }

        // Look for ->id('panelId') to confirm we're in the right file
        $idPattern = '/(->id\s*\(\s*[\'"]' . preg_quote($panelId, '/') . '[\'"]\s*\))(\s*\n)/';
        if (! preg_match($idPattern, $contents)) {
            return false;
        }

        // Try to insert after ->path() first, then fall back to ->id()
        $pathPattern = '/(->path\s*\(\s*[\'"][^\'"]*[\'"]\s*\))(\s*\n)/';

        if (preg_match($pathPattern, $contents)) {
            $pattern = $pathPattern;
        } else {
            // No ->path() found, insert after ->id() instead
            $pattern = $idPattern;
        }

        $replacement = '$1' . "\n            ->viteTheme('{$this->themePath}')" . '$2';
        $newContents = preg_replace($pattern, $replacement, $contents, 1);

        if ($newContents === null || $newContents === $contents) {
            return false;
        }

        $this->filesystem->put($providerPath, $newContents);
        $this->components->info('Added viteTheme() to panel provider.');

        return true;
    }
}
