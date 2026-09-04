<?php

namespace Filament\Support\Commands\Concerns;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Arr;
use Illuminate\View\FileViewFinder;

use function Filament\Support\get_composer_vendor_directory;
use function Filament\Support\is_path_within_directory;
use function Filament\Support\is_path_within_vendor_directory;
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
        $defaultViewPath = Arr::first($viewPaths);
        $publishedVendorViewPath = resource_path('views/vendor');

        if (
            ($defaultViewPath !== null) &&
            is_path_within_directory($defaultViewPath, base_path()) &&
            (! is_path_within_directory($defaultViewPath, get_composer_vendor_directory())) &&
            (
                (! is_path_within_vendor_directory($defaultViewPath, base_path())) ||
                is_path_within_directory($defaultViewPath, $publishedVendorViewPath)
            )
        ) {
            $paths[''] = $defaultViewPath;
        }

        /** @var FileViewFinder $viewFinder */
        $viewFinder = $viewFactory->getFinder();

        foreach ($viewFinder->getHints() as $namespace => $hintPaths) {
            foreach ($hintPaths as $path) {
                if (! is_path_within_directory($path, base_path())) {
                    continue;
                }

                if (is_path_within_directory($path, get_composer_vendor_directory())) {
                    continue;
                }

                if (is_path_within_vendor_directory($path, base_path()) && (! is_path_within_directory($path, $publishedVendorViewPath))) {
                    continue;
                }

                if ($path === resource_path('views/vendor/livewire')) {
                    continue;
                }

                if (filled($paths[''] ?? null) && str($path)->startsWith($paths[''])) {
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
                    default: $this->input->isInteractive() ? null : array_key_first($options),
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
