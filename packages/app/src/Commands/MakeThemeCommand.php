<?php

namespace Filament\Commands;

use Filament\Context;
use Filament\Facades\Filament;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class MakeThemeCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Creates a Filament theme.';

    protected $signature = 'make:filament-theme {context?} {--F|force}';

    public function handle(): int
    {
        $context = $this->argument('context');

        if ($context) {
            $context = Filament::getContext($context);
        }

        if (! $context) {
            $contexts = Filament::getContexts();

            /** @var Context $context */
            $context = (count($contexts) > 1) ? $contexts[$this->choice(
                'Which context would you like to create this for?',
                array_map(
                    fn (Context $context): string => $context->getId(),
                    $contexts,
                ),
                Filament::getDefaultContext()->getId(),
            )] : Arr::first($contexts);
        }

        $contextId = $context->getId();

        $cssFilePath = resource_path("css/filament/{$contextId}/theme.css");
        $tailwindConfigFilePath = resource_path("css/filament/{$contextId}/tailwind.config.js");

        if (! $this->option('force') && $this->checkForCollision([
            $cssFilePath,
            $tailwindConfigFilePath,
        ])) {
            return static::INVALID;
        }

        $classPathPrefix = (string) Str::of($context->getPageDirectory())
            ->afterLast('Filament/')
            ->beforeLast('Pages');

        $viewPathPrefix = Str::of($classPathPrefix)
            ->explode('/')
            ->map(fn ($segment) => Str::lower(Str::kebab($segment)))
            ->implode('/');

        $this->copyStubToApp('ThemeCss', $cssFilePath, [
            'context' => $contextId,
        ]);
        $this->copyStubToApp('ThemeTailwindConfig', $tailwindConfigFilePath, [
            'classPathPrefix' => $classPathPrefix,
            'viewPathPrefix' => $viewPathPrefix,
        ]);

        $this->call('filament:compile-theme', ['context' => $contextId]);

        $this->components->info("Successfully created resources/css/filament/{$contextId}/theme.css and resources/css/filament/{$contextId}/tailwind.config.js!");

        $this->components->info("Make sure to register the theme in the {$contextId} context provider using `theme(asset('css/filament/{$contextId}/theme.css'))`");

        return static::SUCCESS;
    }
}
