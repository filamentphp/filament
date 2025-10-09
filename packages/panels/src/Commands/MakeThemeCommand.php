<?php

namespace Filament\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\HasPanel;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Filament\Support\Commands\Exceptions\SuccessCommandOutput;
use Illuminate\Console\Command;
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
    use CanManipulateFiles;
    use HasPanel;

    protected $description = 'Create a new Filament panel theme';

    protected $name = 'make:filament-theme';

    protected string $pm;

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
                name: 'force',
                shortcut: 'F',
                mode: InputOption::VALUE_NONE,
                description: 'Overwrite the contents of the files if they already exist',
            ),
        ];
    }

    public function handle(): int
    {
        try {
            $this->configurePanel(question: 'Which panel would you like to create this theme for?');
            $this->configurePackageManager();

            $this->installDependencies();
            $this->createThemeSourceFiles();

            $this->abortIfNotVite();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        } catch (SuccessCommandOutput) {
            return static::SUCCESS;
        }

        $this->components->warn('Action is required to complete the theme setup:');
        $this->components->bulletList([
            "First, add a new item to the Laravel plugin's `input` array in `vite.config.js`: `resources/css/filament/{$this->panel->getId()}/theme.css`.",
            "Next, register the theme in the {$this->panel->getId()} panel provider using `->viteTheme('resources/css/filament/{$this->panel->getId()}/theme.css')`",
            "Finally, run `{$this->pm} run build` to compile the theme.",
        ]);

        return static::SUCCESS;
    }

    protected function configurePackageManager(): void
    {
        $this->pm = $this->option('pm') ?? 'npm';

        exec("{$this->pm} -v", $pmVersion, $pmVersionExistCode);

        if ($pmVersionExistCode !== 0) {
            $this->error('Node.js is not installed. Please install before continuing.');

            throw new FailureCommandOutput;
        }

        $this->info("Using {$this->pm} v{$pmVersion[0]}");
    }

    protected function installDependencies(): void
    {
        $installCommand = match ($this->pm) {
            'yarn' => 'yarn add',
            default => "{$this->pm} install",
        };

        exec("{$installCommand} tailwindcss@latest @tailwindcss/vite --save-dev");

        $this->components->info('Dependencies installed successfully.');
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

        $this->components->warn('Action is required to complete the theme setup:');
        $this->components->bulletList([
            "It looks like you don't have Vite installed. Please use your asset bundling system of choice to compile `resources/css/filament/{$this->panel->getId()}/theme.css` into `public/css/filament/{$this->panel->getId()}/theme.css`.",
            "If you're not currently using a bundler, we recommend using Vite. Alternatively, you can use the Tailwind CLI with the following command:",
            "npx @tailwindcss/cli --input ./resources/css/filament/{$this->panel->getId()}/theme.css --output ./public/css/filament/{$this->panel->getId()}/theme.css --config ./resources/css/filament/{$this->panel->getId()}/tailwind.config.js --minify",
            "Make sure to register the theme in the {$this->panel->getId()} panel provider using `->theme(asset('css/filament/{$this->panel->getId()}/theme.css'))`",
        ]);

        throw new SuccessCommandOutput;
    }
}
