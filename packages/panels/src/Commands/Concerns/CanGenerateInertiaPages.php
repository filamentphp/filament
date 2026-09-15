<?php

namespace Filament\Commands\Concerns;

use Filament\Commands\FileGenerators\CustomPageClassGenerator;
use Filament\Inertia\InertiaPlugin;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Illuminate\Filesystem\Filesystem;
use Inertia\Middleware;
use ReflectionClass;

use function Laravel\Prompts\confirm;

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
            if ($this->hasInertiaTypeScript || $this->option('ssr') || $this->option('no-ssr') || $this->option('inertia-entry') || $this->option('inertia-pages') || $this->option('inertia-ssr-entry')) {
                $this->components->error('Inertia setup options require --vue, --react, or --svelte.');

                throw new FailureCommandOutput;
            }

            return;
        }

        if ($this->option('no-ssr') && ($this->option('ssr') || $this->option('inertia-ssr-entry'))) {
            $this->components->error('Choose --no-ssr or SSR setup options, not both.');

            throw new FailureCommandOutput;
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

        $missingPackages = [];

        foreach ($packages as $package) {
            if ($this->fileExists(base_path("node_modules/{$package}/package.json"))) {
                continue;
            }

            $this->components->error("Missing JavaScript package [{$package}]. Install it with your package manager, using a version compatible with your framework and Vite.");
            if (! str_starts_with($package, '@inertiajs/')) {
                $missingPackages[] = $package;
            }
            $hasMissingDependencies = true;
        }

        if ($missingPackages !== []) {
            $this->components->info('Install the missing packages with npm (or the equivalent command for your package manager), choosing compatible versions:');
            $this->line('    npm install ' . implode(' ', $missingPackages));
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
        $extension = $this->hasInertiaTypeScript ? 'ts' : 'js';
        $plugin = $this->panel->hasPlugin('inertia') ? $this->panel->getPlugin('inertia') : null;
        $configuredEntry = ($plugin instanceof InertiaPlugin) ? $plugin->getRendererEntry() : null;

        if ($plugin && ($configuredEntry === null) && (! $this->option('inertia-entry'))) {
            $this->components->error('Cannot determine the configured renderer source without evaluating renderer(). Set rendererEntry() on the plugin, or pass --inertia-entry and --inertia-pages. No files were generated.');

            throw new FailureCommandOutput;
        }

        $rendererPath = ($entry = $this->option('inertia-entry') ?? $configuredEntry)
            ? $this->getInertiaSourcePath($entry)
            : $this->getInertiaSharedPath('inertia', $extension);

        if (($configuredEntry !== null) && ($rendererPath !== $this->getInertiaSourcePath($configuredEntry))) {
            $this->components->error('The requested entry differs from the panel rendererEntry(). Update the panel configuration explicitly before generating into another renderer.');

            throw new FailureCommandOutput;
        }

        if ((! in_array(pathinfo($rendererPath, PATHINFO_EXTENSION), ['js', 'ts'])) || in_array(pathinfo($rendererPath, PATHINFO_FILENAME), ['resolve', 'ssr'])) {
            $this->components->error('Use a .js or .ts renderer entry, with a name other than resolve or ssr.');

            throw new FailureCommandOutput;
        }

        $directory = dirname($rendererPath);

        if (($directory !== resource_path('js/filament')) && $this->fileExists($rendererPath) && (! $this->option('inertia-pages'))) {
            $this->components->error('Cannot determine the component directory of a custom renderer. Pass --inertia-pages; arbitrary JavaScript resolvers are not inspected.');

            throw new FailureCommandOutput;
        }

        $pagesDirectory = $this->option('inertia-pages') ? $this->getInertiaSourcePath($this->option('inertia-pages')) : "{$directory}/pages";
        $resolverPath = $this->getInertiaSharedPath('resolve', $extension, $directory);
        $serverPath = $this->getInertiaSharedPath('ssr', $extension, $directory);
        $hasCustomResolver = $this->fileExists($rendererPath) && (! $this->fileExists($resolverPath));
        $existingServerPath = $this->option('inertia-ssr-entry') ? $this->getInertiaSourcePath($this->option('inertia-ssr-entry')) : null;

        if (($existingServerPath !== null) && (! $this->fileExists($existingServerPath))) {
            $this->components->error('--inertia-ssr-entry must identify an existing SSR entry. Omit it and use --ssr to generate a new server.');

            throw new FailureCommandOutput;
        }

        foreach (['js', 'ts', 'jsx', 'tsx'] as $serverExtension) {
            $candidate = resource_path("js/ssr.{$serverExtension}");
            if (($existingServerPath === null) && $this->fileExists($candidate)) {
                $existingServerPath = $candidate;
            }
        }

        $hasServer = $this->fileExists($serverPath);
        $existingServerBundle = config('inertia.ssr.bundle');

        if (! filled($existingServerBundle)) {
            $existingServerBundle = null;

            foreach (['js', 'mjs'] as $bundleExtension) {
                $candidate = base_path("bootstrap/ssr/ssr.{$bundleExtension}");

                if ($this->fileExists($candidate)) {
                    $existingServerBundle = $candidate;

                    break;
                }
            }
        }

        $generateServer = (! $this->option('no-ssr')) && ($this->option('ssr') || ($existingServerPath !== null) || $hasServer);

        if ((! $generateServer) && ($existingServerBundle === null) && (! $this->option('no-ssr')) && (! $this->fileExists($rendererPath)) && $this->input->isInteractive()) {
            $generateServer = confirm('Generate an Inertia SSR entry?', default: false);
        }

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
        $componentPath = $pagesDirectory . '/' . substr($component, strlen('Filament/')) . ".{$componentExtension}";
        $classPath = str_replace('\\', '/', "{$this->pagesDirectory}/{$this->fqnEnd}.php");

        $sharedPaths = [$rendererPath, $resolverPath, $serverPath, ...(($existingServerPath !== null) ? [$existingServerPath] : []), ...(($existingServerBundle !== null) ? [$existingServerBundle] : [])];

        if (array_intersect(array_map($this->normalizePath(...), [$classPath, $componentPath]), array_map($this->normalizePath(...), $sharedPaths))) {
            $this->components->error('The page or component destination conflicts with a shared renderer, resolver or SSR entry. Choose a different destination; --force cannot overwrite shared files.');

            throw new FailureCommandOutput;
        }

        $this->checkInertiaFilePaths([
            $classPath, $componentPath, ...$sharedPaths,
        ]);

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

                if ($this->option('ssr')) {
                    $this->components->warn('To add SSR to a customized page, follow the manual SSR setup documentation or use --ssr when generating a new page. Do not use --force just to add SSR: it replaces your page and component. No files were changed.');
                }

                throw new FailureCommandOutput;
            }
        }

        if ($this->fileExists($rendererPath)) {
            $renderer = app(Filesystem::class)->get($rendererPath);

            foreach (array_diff(['vue', 'react', 'svelte'], [$framework]) as $otherFramework) {
                if (str_contains($renderer, "inertia/{$otherFramework}.js")) {
                    $this->components->error("[{$rendererPath}] already uses {$otherFramework}. Use that framework, or create this page and its separate renderer manually.");

                    throw new FailureCommandOutput;
                }
            }
        }

        $helper = $this->getInertiaImportPath(
            dirname((new ReflectionClass(InertiaPlugin::class))->getFileName(), 3) . "/resources/js/inertia/{$framework}.js",
            $directory,
        );
        $replacements = [
            'helper' => str_replace("'", "\\'", $helper),
            'resolver' => './' . basename($resolverPath),
            'pages' => $this->getInertiaImportPath($pagesDirectory, $directory),
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
            ...(! $hasCustomResolver ? [$resolverPath => "Inertia/{$framework}/Resolve"] : []),
            ...(($generateServer && ($existingServerPath === null) && ($existingServerBundle === null) && (! $hasCustomResolver)) ? [$serverPath => "Inertia/{$framework}/Server"] : []),
        ] as $path => $stub) {
            if ($this->fileExists($path)) {
                $this->components->info("Preserved [{$path}]. Check that it resolves [{$component}].");

                continue;
            }

            $this->copyStubToApp($stub, $path, $replacements);
        }

        $entry = $this->getRelativePath($rendererPath, base_path());
        $this->components->info('Remaining setup (existing application configuration has not been changed):');

        if (! $plugin) {
            $this->line("    Register InertiaPlugin::make()->rendererEntry('{$entry}') on your panel.");
        } elseif ($configuredEntry === null) {
            $this->line("    Panel plugin is registered, but its renderer binding is unverified. Ensure renderer() points to the built [{$entry}].");
        } else {
            $this->line("    Panel rendererEntry() already selects [{$entry}].");
        }

        $manifestPath = public_path('build/manifest.json');
        $manifest = $this->fileExists($manifestPath) ? json_decode(app(Filesystem::class)->get($manifestPath), associative: true) : null;

        if (is_array($manifest) && isset($manifest[$entry]['isEntry']) && $manifest[$entry]['isEntry']) {
            $this->line("    [{$entry}] is present in the last Vite build. Rebuild to include the new page.");
        } else {
            $this->line("    Vite input is unverified. Add [{$entry}] if needed (custom manifests are not inspected), then build your assets.");
        }

        $this->line("    Verify your {$framework} Vite plugin and preserveEntrySignatures: 'exports-only' in build.rollupOptions (build.rolldownOptions for Rolldown-based Vite). Arbitrary build configuration is not inspected.");
        if ($hasCustomResolver) {
            $this->line("    Custom resolver location is unknown; no resolver or server was generated. Register [{$component}] from [{$componentPath}] in your existing resolver and configure SSR manually if needed.");
        } else {
            $this->line("    Existing resolvers are not rewritten. Ensure [{$resolverPath}] resolves [{$component}] from [{$componentPath}].");
        }

        $this->line("    Custom renderers must default-export Filament's createRenderer(), not a native createInertiaApp() bootstrap. Custom JavaScript wiring is not verified.");

        if ($this->hasInertiaTypeScript) {
            $this->line("    Include [{$directory}] and [{$pagesDirectory}] in your TypeScript configuration, with vite/client types and your framework compiler settings; these are not inspected.");
            $this->line('    Run your framework typechecker before shipping. See the TypeScript setup recipes for compatible compiler/checker versions and SSR Node types; optional checker tooling is not installed or verified.');
        }

        if ($existingServerPath !== null) {
            $this->line("    Preserved existing SSR entry [{$existingServerPath}]; no second server was generated.");
            $this->line("    Dispatch page.component.startsWith('Filament/') to createInertiaApp() with id: 'filament-inertia' and your Filament resolver. Keep your framework's SSR render/setup options and the native application's fallback branch/root ID. See the custom page documentation for a complete dispatch example.");
        } elseif ($hasServer) {
            $this->line("    Preserved Filament SSR entry [{$serverPath}]. Verify your existing Vite SSR entry includes it, then rebuild and restart that server. Its configuration and running service are not verified.");
        } elseif ($existingServerBundle !== null) {
            $this->line("    Detected configured or built SSR bundle [{$existingServerBundle}]; no second server was generated.");
            $this->line('    Its source entry and running service are not verified. Locate its source in your Vite configuration and add the Filament/ dispatch there, then rebuild and restart that server. Do not edit the built bundle.');
        } elseif ($generateServer && (! $hasCustomResolver)) {
            $this->line("    Configure [{$serverPath}] as your Vite SSR entry, build it and run the SSR server. If you already have a server elsewhere, do not start a second one: merge the Filament/ dispatch into it instead. Custom SSR locations require --inertia-ssr-entry.");
        } else {
            $this->line('    SSR generation skipped. To add it later, follow the manual SSR setup documentation or pass --ssr when generating a new page. Do not use --force on a customized page just to add SSR. This does not disable application SSR; configure existing SSR dispatch manually if enabled. Use --no-ssr to explicitly skip generation.');
        }

        $this->components->info('Dependencies, middleware, native Inertia bootstraps, panel, Vite, TypeScript and SSR configuration are never installed or edited by this command. Package presence/API checks do not verify peer-version compatibility. See the custom page documentation for manual setup.');
    }

    protected function getInertiaSourcePath(string $path): string
    {
        if (($path === '') || str_starts_with($path, '/') || preg_match('#(^|/)\.\.(/|$)|[^a-zA-Z0-9_./-]#', $path)) {
            $this->components->error('Inertia source paths must be relative to the application root, without parent traversal, and contain only letters, numbers, slashes, dots, underscores or hyphens.');

            throw new FailureCommandOutput;
        }

        return $this->normalizePath(base_path($path));
    }

    protected function getInertiaImportPath(string $path, string $from): string
    {
        $relativePath = rtrim($this->getRelativePath($path, $from), '/');

        return match (true) {
            $relativePath === '' => '.',
            ($relativePath === '..') || str_starts_with($relativePath, '../') => $relativePath,
            default => "./{$relativePath}",
        };
    }

    /** @param array<string> $paths */
    protected function checkInertiaFilePaths(array $paths): void
    {
        $filesystem = app(Filesystem::class);
        $paths = array_map($this->normalizePath(...), $paths);

        foreach ($paths as $path) {
            if ($this->fileExists($path) && (! $filesystem->isFile($path))) {
                $this->components->error("[{$path}] must be a file. No files were generated.");

                throw new FailureCommandOutput;
            }

            for ($directory = dirname($path); dirname($directory) !== $directory; $directory = dirname($directory)) {
                if (in_array($directory, $paths, true) || ($this->fileExists($directory) && (! $filesystem->isDirectory($directory)))) {
                    $this->components->error("[{$directory}] must be a directory, not an existing or planned file. No files were generated.");

                    throw new FailureCommandOutput;
                }
            }
        }
    }

    protected function getInertiaSharedPath(string $name, string $extension, ?string $directory = null): string
    {
        $directory ??= resource_path('js/filament');

        foreach (array_unique([$extension, 'js', 'ts']) as $existingExtension) {
            if ($this->fileExists($path = "{$directory}/{$name}.{$existingExtension}")) {
                return $path;
            }
        }

        return "{$directory}/{$name}.{$extension}";
    }
}
