<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class CompileThemeCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Compiles a Filament theme.';

    protected $signature = 'filament:compile-theme {panel?} {--W|watch}';

    public function handle(): int
    {
        exec('npm -v', $npmVersion, $npmVersionExistCode);

        if ($npmVersionExistCode !== 0) {
            $this->error('Node.js is not installed. Please install before continuing.');

            return static::FAILURE;
        }

        $this->info("Using Node.js v{$npmVersion[0]}");

        $panelId = $this->argument('panel');

        if (! $panelId) {
            $panelIds = collect(Filament::getPanels())
                ->map(fn (Panel $panel): string => $panel->getId())
                ->filter(fn (string $panelId): bool => (
                    file_exists(resource_path("css/filament/{$panelId}/theme.css")) &&
                    file_exists(resource_path("css/filament/{$panelId}/tailwind.config.js"))
                ))
                ->all();

            if (empty($panelIds)) {
                $this->error('No themes found. Please create a theme using `php artisan make:filament-theme` before continuing.');

                return static::FAILURE;
            }

            $defaultPanelId = in_array(Filament::getDefaultPanel()->getId(), $panelIds) ?
                Filament::getDefaultPanel()->getId() :
                Arr::first($panelIds);

            $panelId = (count($panelIds) > 1) ? $this->choice(
                'Which panel\'s theme would you like to compile?',
                $panelIds,
                $defaultPanelId,
            ) : Arr::first($panelIds);
        }

        exec('npm install tailwindcss @tailwindcss/forms @tailwindcss/typography postcss autoprefixer tippy.js --save-dev');
        exec("npx tailwindcss --input ./resources/css/filament/{$panelId}/theme.css --output ./public/css/filament/{$panelId}/theme.css --config ./resources/css/filament/{$panelId}/tailwind.config.js --minify" . ($this->option('watch') ? ' --watch' : ''));

        $this->components->info("Compiled resources/css/filament/{$panelId}/theme.css!");

        return static::SUCCESS;
    }
}
