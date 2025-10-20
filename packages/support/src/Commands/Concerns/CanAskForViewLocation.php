<?php

namespace Filament\Support\Commands\Concerns;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Arr;
use Illuminate\View\FileViewFinder;

use function Laravel\Prompts\select;

trait CanAskForViewLocation
{
    /**
     * @return array{
     *     0: string,
     *     1: string,
     * }
     */
    protected function askForViewLocation(string $view, string $question = 'Where would you like to create the Blade view?', ?string $defaultNamespace = null): array
    {
        $viewFactory = app(Factory::class);

        $paths = [];

        /** @var array<string> $viewPaths */
        $viewPaths = config('view.paths') ?? [];

        if (str(Arr::first($viewPaths))->startsWith(base_path())) {
            $paths[''] = Arr::first($viewPaths);
        }

        /** @var FileViewFinder $viewFinder */
        $viewFinder = $viewFactory->getFinder();

        foreach ($viewFinder->getHints() as $namespace => $hintPaths) {
            foreach ($hintPaths as $path) {
                if (! str($path)->startsWith(base_path())) {
                    continue;
                }

                if (str($path)->startsWith(base_path('vendor'))) {
                    continue;
                }

                if ($path === resource_path('views/vendor/livewire')) {
                    continue;
                }

                $paths[$namespace] = $path;

                break;
            }
        }

        $options = array_map(
            fn (string $path): string => (string) str($path)->after(base_path()),
            $paths,
        );

        $namespace = ($defaultNamespace !== null) && array_key_exists($defaultNamespace, $paths)
            ? $defaultNamespace
            : (count($options) > 1
                ? select(
                    label: $question,
                    options: $options,
                )
                : array_key_first($options));

        if (blank($namespace)) {
            return [
                $view,
                resource_path(
                    (string) str($view)
                        ->replace('.', '/')
                        ->prepend('views/')
                        ->append('.blade.php'),
                ),
            ];
        }

        return [
            "{$namespace}::{$view}",
            (string) str($view)
                ->replace('.', '/')
                ->prepend("{$paths[$namespace]}/")
                ->append('.blade.php'),
        ];
    }
}
