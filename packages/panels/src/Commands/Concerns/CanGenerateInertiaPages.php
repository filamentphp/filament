<?php

namespace Filament\Commands\Concerns;

use Filament\Commands\FileGenerators\CustomPageClassGenerator;
use Filament\Inertia\InertiaPlugin;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Illuminate\Filesystem\Filesystem;
use Inertia\Middleware;
use ReflectionClass;

trait CanGenerateInertiaPages
{
    /** @var 'react' | 'vue' | 'svelte' | null */
    protected ?string $inertiaFramework = null;

    protected bool $hasInertiaTypeScript = false;

    protected function configureInertia(): void
    {
        $frameworks = array_values(array_filter(['vue', 'react', 'svelte'], fn (string $framework): bool => (bool) $this->option($framework)));
        $this->hasInertiaTypeScript = $this->option('ts') || $this->option('typescript');

        if (count($frameworks) > 1) {
            $this->components->error('Choose only one of --vue, --react, or --svelte.');

            throw new FailureCommandOutput;
        }

        $this->inertiaFramework = $frameworks[0] ?? null;

        if ($this->inertiaFramework === null) {
            if ($this->hasInertiaTypeScript || $this->option('ssr')) {
                $this->components->error('The --ts, --typescript, and --ssr options require --vue, --react, or --svelte.');

                throw new FailureCommandOutput;
            }

            return;
        }

        foreach (['resource', 'resource-namespace', 'type'] as $option) {
            if ($this->input->hasParameterOption(($option === 'resource') ? ['--resource', '-R'] : "--{$option}") || filled($this->option($option))) {
                $this->components->error('Inertia content is supported on custom pages, not resource pages. Remove the resource options.');

                throw new FailureCommandOutput;
            }
        }

        $this->checkInertiaDependencies($this->inertiaFramework);
    }

    /** @param 'react' | 'vue' | 'svelte' $framework */
    protected function checkInertiaDependencies(string $framework): void
    {
        $hasMissingDependencies = false;

        if (! class_exists(Middleware::class)) {
            $this->components->error('Install the PHP adapter first: composer require inertiajs/inertia-laravel:"^3.3"');
            $hasMissingDependencies = true;
        }

        $adapter = match ($framework) {
            'vue' => 'vue3',
            default => $framework,
        };
        $packages = [
            '@inertiajs/core', "@inertiajs/{$adapter}", 'vite', 'laravel-vite-plugin',
            ...match ($framework) {
                'vue' => ['vue', '@vitejs/plugin-vue'],
                'react' => ['react', 'react-dom', '@vitejs/plugin-react'],
                'svelte' => ['svelte', '@sveltejs/vite-plugin-svelte'],
            },
            ...($this->hasInertiaTypeScript ? ['typescript'] : []),
            ...(($this->hasInertiaTypeScript && ($framework === 'react')) ? ['@types/react', '@types/react-dom'] : []),
        ];

        foreach ($packages as $package) {
            if ($this->fileExists(base_path("node_modules/{$package}/package.json"))) {
                continue;
            }

            $this->components->error("Missing JavaScript package [{$package}]. Install it with your package manager, using a version compatible with your framework and Vite.");
            $hasMissingDependencies = true;
        }

        $typesPath = base_path('node_modules/@inertiajs/core/types/types.d.ts');

        if ((! $this->fileExists($typesPath)) || (! str_contains(app(Filesystem::class)->get($typesPath), 'externalNavigation?: ExternalNavigationOptions'))) {
            $this->components->error('Use matching Inertia core and adapter builds with externalNavigation support. The public Inertia 3.7 release does not include it; do not install that release for this integration.');
            $hasMissingDependencies = true;
        }

        if ($hasMissingDependencies) {
            $this->components->info('No files were generated. Install the prerequisites, then run this command again. See the custom page documentation for Inertia setup.');

            throw new FailureCommandOutput;
        }
    }

    /** @param 'react' | 'vue' | 'svelte' $framework */
    protected function createInertiaPage(string $framework): void
    {
        $directory = resource_path('js/filament');
        $extension = $this->hasInertiaTypeScript ? 'ts' : 'js';
        $componentExtension = match ($framework) {
            'react' => $this->hasInertiaTypeScript ? 'tsx' : 'jsx',
            'vue' => 'vue',
            'svelte' => 'svelte',
        };
        $component = (string) str($this->fqn)
            ->replaceStart(app()->getNamespace(), '')
            ->replace('\\', '/')
            ->replaceStart('Filament/', '')
            ->replaceStart('Pages/', '')
            ->replace('/Pages/', '/')
            ->prepend('Filament/');
        $componentPath = $directory . '/pages/' . substr($component, strlen('Filament/')) . ".{$componentExtension}";
        $classPath = str_replace('\\', '/', "{$this->pagesDirectory}/{$this->fqnEnd}.php");

        if ($framework === 'react') {
            $otherComponentPath = substr($componentPath, 0, -3) . ($this->hasInertiaTypeScript ? 'jsx' : 'tsx');

            if ($this->fileExists($otherComponentPath)) {
                $this->components->error("[{$otherComponentPath}] already defines [{$component}]. Rename or remove it before changing the component language.");

                throw new FailureCommandOutput;
            }
        }

        // Check all page collisions before writing or removing either file.
        foreach ([$classPath, $componentPath] as $path) {
            if ((! $this->option('force')) && $this->fileExists($path)) {
                $this->components->error("[{$path}] already exists. Use --force to overwrite the page and component. Shared renderer files will be preserved.");

                throw new FailureCommandOutput;
            }
        }

        $rendererPath = $this->getInertiaSharedPath('inertia', $extension);
        $resolverPath = $this->getInertiaSharedPath('resolve', $extension);
        $serverPath = $this->getInertiaSharedPath('ssr', $extension);

        if ($this->fileExists($rendererPath)) {
            $renderer = app(Filesystem::class)->get($rendererPath);

            foreach (array_diff(['vue', 'react', 'svelte'], [$framework]) as $otherFramework) {
                if (str_contains($renderer, "inertia/{$otherFramework}.js")) {
                    $this->components->error("[{$rendererPath}] already uses {$otherFramework}. Use that framework, or create this page and its separate renderer manually.");

                    throw new FailureCommandOutput;
                }
            }
        }

        $helper = $this->getRelativePath(
            dirname((new ReflectionClass(InertiaPlugin::class))->getFileName(), 3) . "/resources/js/inertia/{$framework}.js",
            $directory,
        );
        $replacements = [
            'helper' => str_replace("'", "\\'", $helper),
            'resolver' => './' . basename($resolverPath),
            'nameType' => $this->hasInertiaTypeScript ? ': string' : '',
            'componentTypeImport' => $this->hasInertiaTypeScript ? match ($framework) {
                'react' => "import type { ComponentType } from 'react'\n",
                'vue' => "import type { DefineComponent } from 'vue'\n",
                'svelte' => "import type { Component } from 'svelte'\n",
            } : '',
            'globType' => $this->hasInertiaTypeScript ? match ($framework) {
                'react' => '<{ default: ComponentType }>',
                'vue' => '<{ default: DefineComponent }>',
                'svelte' => '<{ default: Component }>',
            } : '',
        ];

        $this->writeFile($classPath, app(CustomPageClassGenerator::class, [
            'fqn' => $this->fqn,
            'view' => '',
            'clusterFqn' => $this->clusterFqn,
            'inertiaComponent' => $component,
        ]));
        $this->copyStubToApp('Inertia/' . $framework . '/Page' . ($this->hasInertiaTypeScript ? 'TypeScript' : ''), $componentPath);

        foreach ([
            $rendererPath => 'Inertia/Renderer',
            $resolverPath => "Inertia/{$framework}/Resolve",
            ...($this->option('ssr') ? [$serverPath => "Inertia/{$framework}/Server"] : []),
        ] as $path => $stub) {
            if ($this->fileExists($path)) {
                $this->components->info("Preserved [{$path}]. Check that it resolves [{$component}].");

                continue;
            }

            $this->copyStubToApp($stub, $path, $replacements);
        }

        $entry = $this->getRelativePath($rendererPath, base_path());
        $this->components->info('Register InertiaPlugin on your panel with:');
        $this->line("    InertiaPlugin::make()->renderer(fn (): string => app(\\Illuminate\\Foundation\\Vite::class)->asset('{$entry}'))");
        $this->components->info("Add [{$entry}] to your Vite inputs and configure the {$framework} plugin. Set preserveEntrySignatures: 'exports-only' in build.rollupOptions (build.rolldownOptions for Rolldown-based Vite), then run your asset build.");

        if ($this->hasInertiaTypeScript) {
            $this->components->info('Include resources/js/filament in your TypeScript configuration, with vite/client types and the compiler settings for your framework.');
        }

        if ($this->option('ssr')) {
            $this->components->info('Configure the generated SSR entry in Vite, build it, and run your Inertia SSR server. If your app already has an SSR entry, merge the Filament/ component dispatch into it rather than replacing it. Filament requires the filament-inertia root ID.');
        }

        $this->components->info('Your dependencies, panel, and build configuration have not been changed. See the custom page documentation for the remaining setup.');
    }

    protected function getInertiaSharedPath(string $name, string $extension): string
    {
        foreach (array_unique([$extension, 'js', 'ts']) as $existingExtension) {
            if ($this->fileExists($path = resource_path("js/filament/{$name}.{$existingExtension}"))) {
                return $path;
            }
        }

        return resource_path("js/filament/{$name}.{$extension}");
    }
}
