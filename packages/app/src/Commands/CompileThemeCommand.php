<?php

namespace Filament\Commands;

use Filament\Context;
use Filament\Facades\Filament;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class CompileThemeCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Compiles a Filament theme.';

    protected $signature = 'filament:compile-theme {context?} {--W|watch}';

    public function handle(): int
    {
        exec('npm -v', $npmVersion, $npmVersionExistCode);

        if ($npmVersionExistCode !== 0) {
            $this->error('Node.js is not installed. Please install before continuing.');

            return static::FAILURE;
        }

        $this->info("Using Node.js v{$npmVersion[0]}");

        $contextId = $this->argument('context');

        if (! $contextId) {
            $contextIds = collect(Filament::getContexts())
                ->map(fn (Context $context): string => $context->getId())
                ->filter(fn (string $contextId): bool => (
                    file_exists(resource_path("css/filament/{$contextId}/theme.css")) &&
                    file_exists(resource_path("css/filament/{$contextId}/tailwind.config.js"))
                ))
                ->all();

            if (empty($contextIds)) {
                $this->error('No themes found. Please create a theme using `php artisan make:filament-theme` before continuing.');

                return static::FAILURE;
            }

            $defaultContextId = in_array(Filament::getDefaultContext()->getId(), $contextIds) ?
                Filament::getDefaultContext()->getId() :
                Arr::first($contextIds);

            $contextId = (count($contextIds) > 1) ? $this->choice(
                'Which context\'s theme would you like to compile?',
                $contextIds,
                $defaultContextId,
            ) : Arr::first($contextIds);
        }

        exec("npx tailwindcss --input ./resources/css/filament/{$contextId}/theme.css --output ./public/css/filament/{$contextId}/theme.css --config ./resources/css/filament/{$contextId}/tailwind.config.js --minify" . ($this->option('watch') ? ' --watch' : ''));

        $this->components->info("Compiled resources/css/filament/{$contextId}/theme.css!");

        return static::SUCCESS;
    }
}
