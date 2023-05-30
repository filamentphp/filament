<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class MakeThemeCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Create a new Filament panel theme';

    protected $signature = 'make:filament-theme {panel?} {--F|force}';

    public function handle(): int
    {
        $panel = $this->argument('panel');

        if ($panel) {
            $panel = Filament::getPanel($panel);
        }

        if (! $panel) {
            $panels = Filament::getPanels();

            /** @var Panel $panel */
            $panel = (count($panels) > 1) ? $panels[$this->choice(
                'Which panel would you like to create this for?',
                array_map(
                    fn (Panel $panel): string => $panel->getId(),
                    $panels,
                ),
                Filament::getDefaultPanel()->getId(),
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

        $classPathPrefix = (string) Str::of($panel->getPageDirectory())
            ->afterLast('Filament/')
            ->beforeLast('Pages');

        $viewPathPrefix = Str::of($classPathPrefix)
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

        $this->call('filament:compile-theme', ['panel' => $panelId]);

        $this->components->info("Successfully created resources/css/filament/{$panelId}/theme.css and resources/css/filament/{$panelId}/tailwind.config.js!");

        $this->components->info("Make sure to register the theme in the {$panelId} panel provider using `theme(asset('css/filament/{$panelId}/theme.css'))`");

        return static::SUCCESS;
    }
}
