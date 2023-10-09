<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use function Laravel\Prompts\select;

class MakeThemeCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Create a new Filament panel theme';

    protected $signature = 'make:filament-theme {panel?} {--F|force}';

    public function handle(): int
    {
        exec('npm -v', $npmVersion, $npmVersionExistCode);

        if ($npmVersionExistCode !== 0) {
            $this->error('Node.js is not installed. Please install before continuing.');

            return static::FAILURE;
        }

        $this->info("Using NPM v{$npmVersion[0]}");

        exec('npm install tailwindcss @tailwindcss/forms @tailwindcss/typography postcss autoprefixer --save-dev');

        $panel = $this->argument('panel');

        if ($panel) {
            $panel = Filament::getPanel($panel);
        }

        if (! $panel) {
            $panels = Filament::getPanels();

            /** @var Panel $panel */
            $panel = (count($panels) > 1) ? $panels[select(
                label: 'Which panel would you like to create this for?',
                options: array_map(
                    fn (Panel $panel): string => $panel->getId(),
                    $panels,
                ),
                default: Filament::getDefaultPanel()->getId(),
            )] : Arr::first($panels);
        }

        $panelId = $panel->getId();

        $cssFilePath = resource_path("css/filament/{$panelId}/theme.css");
        $tailwindConfigFilePath = resource_path("css/filament/{$panelId}/tailwind.config.js");

        if (! $this->option('force') && $this->checkForCollision([
            $cssFilePath,
            $tailwindConfigFilePath,
        ])) {
            return static::INVALID;
        }

        $classPathPrefix = (string) str(Arr::first($panel->getPageDirectories()))
            ->afterLast('Filament/')
            ->beforeLast('Pages');

        $viewPathPrefix = str($classPathPrefix)
            ->explode('/')
            ->map(fn ($segment) => Str::lower(Str::kebab($segment)))
            ->implode('/');

        $this->copyStubToApp('ThemeCss', $cssFilePath, [
            'panel' => $panelId,
        ]);
        $this->copyStubToApp('ThemeTailwindConfig', $tailwindConfigFilePath, [
            'classPathPrefix' => $classPathPrefix,
            'viewPathPrefix' => $viewPathPrefix,
        ]);

        $this->components->info("Filament theme [resources/css/filament/{$panelId}/theme.css] and [resources/css/filament/{$panelId}/tailwind.config.js] created successfully.");

        if (! file_exists(base_path('vite.config.js'))) {
            $this->components->warn('Action is required to complete the theme setup:');
            $this->components->bulletList([
                "It looks like you don't have Vite installed. Please use your asset bundling system of choice to compile `resources/css/filament/{$panelId}/theme.css` into `public/css/filament/{$panelId}/theme.css`.",
                "If you're not currently using a bundler, we recommend using Vite. Alternatively, you can use the Tailwind CLI with the following command:",
                "npx tailwindcss --input ./resources/css/filament/{$panelId}/theme.css --output ./public/css/filament/{$panelId}/theme.css --config ./resources/css/filament/{$panelId}/tailwind.config.js --minify",
                "Make sure to register the theme in the {$panelId} panel provider using `->theme(asset('css/filament/{$panelId}/theme.css'))`",
            ]);

            return static::SUCCESS;
        }

        $postcssConfigPath = base_path('postcss.config.js');

        if (! file_exists($postcssConfigPath)) {
            $this->copyStubToApp('ThemePostcssConfig', $postcssConfigPath);

            $this->components->info('Filament theme [postcss.config.js] created successfully.');
        }

        $this->components->warn('Action is required to complete the theme setup:');
        $this->components->bulletList([
            "First, add a new item to the `input` array of `vite.config.js`: `resources/css/filament/{$panelId}/theme.css`.",
            "Next, register the theme in the {$panelId} panel provider using `->viteTheme('resources/css/filament/{$panelId}/theme.css')`",
            'Finally, run `npm run build` to compile the theme.',
        ]);

        return static::SUCCESS;
    }
}
