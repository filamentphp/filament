<?php

namespace Filament\Support;

use BladeUI\Icons\Factory as BladeIconsFactory;
use Composer\InstalledVersions;
use Filament\Commands\CacheComponentsCommand;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\ColorManager;
use Filament\Support\Commands\AboutCommand as FilamentAboutCommand;
use Filament\Support\Commands\AssetsCommand;
use Filament\Support\Commands\CheckTranslationsCommand;
use Filament\Support\Commands\InstallCommand;
use Filament\Support\Commands\MakeIssueCommand;
use Filament\Support\Commands\OptimizeClearCommand;
use Filament\Support\Commands\OptimizeCommand;
use Filament\Support\Commands\UpgradeCommand;
use Filament\Support\Components\ComponentManager;
use Filament\Support\Components\Contracts\ScopedComponentManager;
use Filament\Support\Enums\GridDirection;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Icons\IconManager;
use Filament\Support\Livewire\Partials\DataStoreOverride;
use Filament\Support\Livewire\Partials\PartialsComponentHook;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\ViewManager;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\View\ComponentAttributeBag;
use Laravel\Octane\Events\RequestReceived;
use Livewire\Livewire;
use Livewire\Mechanisms\DataStore;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

class SupportServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament')
            ->hasCommands([
                AssetsCommand::class,
                CheckTranslationsCommand::class,
                FilamentAboutCommand::class,
                InstallCommand::class,
                MakeIssueCommand::class,
                OptimizeClearCommand::class,
                OptimizeCommand::class,
                UpgradeCommand::class,
            ])
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        $this->app->scoped(
            AssetManager::class,
            fn () => new AssetManager,
        );

        $this->app->singleton(
            CliManager::class,
            fn () => new CliManager,
        );

        $this->app->scoped(
            ScopedComponentManager::class,
            fn () => $this->app->make(ComponentManager::class)->clone(),
        );
        $this->app->booted(fn () => ComponentManager::resolveScoped());
        class_exists(RequestReceived::class) && Event::listen(RequestReceived::class, fn () => ComponentManager::resolveScoped());

        $this->app->scoped(
            ColorManager::class,
            fn () => new ColorManager,
        );

        $this->app->scoped(
            IconManager::class,
            fn () => new IconManager,
        );

        $this->app->scoped(
            ViewManager::class,
            fn () => new ViewManager,
        );

        $this->app->scoped(
            HtmlSanitizerInterface::class,
            fn (): HtmlSanitizer => new HtmlSanitizer(
                (new HtmlSanitizerConfig)
                    ->allowSafeElements()
                    ->allowRelativeLinks()
                    ->allowRelativeMedias()
                    ->allowAttribute('class', allowedElements: '*')
                    ->allowAttribute('data-color', allowedElements: '*')
                    ->allowAttribute('data-from-breakpoint', allowedElements: '*')
                    ->allowAttribute('data-type', allowedElements: '*')
                    ->allowAttribute('style', allowedElements: '*')
                    ->allowAttribute('width', allowedElements: 'img')
                    ->allowAttribute('height', allowedElements: 'img')
                    ->withMaxInputLength(500000),
            ),
        );

        $this->app->scoped(
            'originalRequest',
            function () {
                if (! Livewire::isLivewireRequest()) {
                    return request();
                }

                $persistentMiddleware = app(PersistentMiddleware::class);

                /** @phpstan-ignore-next-line */
                $request = invade($persistentMiddleware)->makeFakeRequest();

                /** @phpstan-ignore-next-line */
                invade($persistentMiddleware)->getRouteFromRequest($request);

                return $request;
            },
        );

        $this->app->bind(DataStore::class, DataStoreOverride::class);

        $this->callAfterResolving(BladeIconsFactory::class, function (BladeIconsFactory $factory): void {
            $factory->add('filament', [
                'path' => __DIR__ . '/../resources/svg',
                'prefix' => 'fi',
            ]);
        });
    }

    public function packageBooted(): void
    {
        app('livewire')->componentHook(new PartialsComponentHook);

        FilamentAsset::register([
            Js::make('support', __DIR__ . '/../dist/index.js'),
        ], 'filament/support');

        Blade::directive('captureSlots', function (string $expression): string {
            return "<?php \$slotContents = get_defined_vars(); \$slots = collect({$expression})->mapWithKeys(fn (string \$slot): array => [\$slot => \$slotContents[\$slot] ?? null])->all(); unset(\$slotContents) ?>";
        });

        Blade::directive('filamentScripts', function (string $expression): string {
            return "<?php echo \Filament\Support\Facades\FilamentAsset::renderScripts({$expression}) ?>";
        });

        Blade::directive('filamentStyles', function (string $expression): string {
            return "<?php echo \Filament\Support\Facades\FilamentAsset::renderStyles({$expression}) ?>";
        });

        Blade::extend(function ($view) {
            return preg_replace('/\s*@trim\s*/m', '', $view);
        });

        ComponentAttributeBag::macro('color', function (string | HasColor $component, string | array | null $color): ComponentAttributeBag {
            if (is_array($color)) {
                return $this
                    ->class(['fi-color'])
                    ->style(FilamentColor::getComponentCustomStyles($component, $color));
            }

            return $this->class(FilamentColor::getComponentClasses($component, $color));
        });

        ComponentAttributeBag::macro('grid', function (array | int | null $columns = [], GridDirection $direction = GridDirection::Row): ComponentAttributeBag {
            if (! is_array($columns)) {
                $columns = ['lg' => $columns];
            }

            $columns = array_filter($columns);

            $columns['default'] ??= 1;

            return $this
                ->class([
                    'fi-grid',
                    'fi-grid-direction-col' => $direction === GridDirection::Column,
                    ...array_map(
                        fn (string $breakpoint): string => match ($breakpoint) {
                            'default' => ($columns[$breakpoint] > 1) ? 'fi-grid-cols' : '',
                            default => "{$breakpoint}:fi-grid-cols",
                        },
                        array_keys($columns),
                    ),
                ])
                ->style(array_map(
                    fn (string $breakpoint, int $columns): string => match ($direction) {
                        GridDirection::Row => '--cols-' . str_replace('!', 'n', str_replace('@', 'c', $breakpoint)) . ": repeat({$columns}, minmax(0, 1fr))",
                        GridDirection::Column => '--cols-' . str_replace('!', 'n', str_replace('@', 'c', $breakpoint)) . ": {$columns}",
                    },
                    array_keys($columns),
                    array_values($columns),
                ));
        });

        ComponentAttributeBag::macro('gridColumn', function (array | int | string | null $span = [], array | int | null $start = [], array | int | string | null $order = [], bool $isHidden = false): ComponentAttributeBag {
            if (! is_array($span)) {
                $span = ['lg' => $span];
            }

            if (! is_array($start)) {
                $start = ['lg' => $start];
            }

            if (! is_array($order)) {
                $order = ['lg' => $order];
            }

            $span = array_filter($span);

            $start = array_filter($start);

            $order = array_filter($order);

            return $this
                ->class([
                    'fi-grid-col',
                    'fi-hidden' => $isHidden || (($span['default'] ?? null) === 'hidden'),
                    ...array_map(
                        fn (string $breakpoint): string => match ($breakpoint) {
                            'default' => '',
                            default => "{$breakpoint}:fi-grid-col-span",
                        },
                        array_keys($span),
                    ),
                    ...array_map(
                        fn (string $breakpoint): string => match ($breakpoint) {
                            'default' => 'fi-grid-col-start',
                            default => "{$breakpoint}:fi-grid-col-start",
                        },
                        array_keys($start),
                    ),
                    ...array_map(
                        fn (string $breakpoint): string => match ($breakpoint) {
                            'default' => 'fi-grid-col-order',
                            default => "{$breakpoint}:fi-grid-col-order",
                        },
                        array_keys($order),
                    ),
                ])
                ->style([
                    ...array_map(
                        fn (string $breakpoint, int | string $span): string => '--col-span-' . str_replace('!', 'n', str_replace('@', 'c', $breakpoint)) . ': ' . match ($span) {
                            'full' => '1 / -1',
                            default => "span {$span} / span {$span}",
                        },
                        array_keys($span),
                        array_values($span),
                    ),
                    ...array_map(
                        fn (string $breakpoint, int $start): string => '--col-start-' . str_replace('!', 'n', str_replace('@', 'c', $breakpoint)) . ': ' . $start,
                        array_keys($start),
                        array_values($start),
                    ),
                    ...array_map(
                        fn (string $breakpoint, int $order): string => '--col-order-' . str_replace('!', 'n', str_replace('@', 'c', $breakpoint)) . ': ' . $order,
                        array_keys($order),
                        array_values($order),
                    ),
                ]);
        });

        Str::macro('sanitizeHtml', function (string $html): string {
            return app(HtmlSanitizerInterface::class)->sanitize($html);
        });

        Stringable::macro('sanitizeHtml', function (): Stringable {
            /** @phpstan-ignore-next-line */
            return new Stringable(Str::sanitizeHtml($this->value));
        });

        Str::macro('ucwords', function (string $value): string {
            return implode(' ', array_map(
                [Str::class, 'ucfirst'],
                explode(' ', $value),
            ));
        });

        Stringable::macro('ucwords', function (): Stringable {
            /** @phpstan-ignore-next-line */
            return new Stringable(Str::ucwords($this->value));
        });

        if (class_exists(InstalledVersions::class)) {
            $packages = [
                'filament',
                'forms',
                'notifications',
                'support',
                'tables',
                'actions',
                'infolists',
                'schemas',
                'widgets',
            ];

            AboutCommand::add('Filament', static fn () => [
                'Version' => InstalledVersions::getPrettyVersion('filament/support'),
                'Packages' => collect($packages)
                    ->filter(fn (string $package): bool => InstalledVersions::isInstalled("filament/{$package}"))
                    ->join(', '),
                'Views' => function () use ($packages): string {
                    $publishedViewPaths = collect($packages)
                        ->filter(fn (string $package): bool => is_dir(resource_path("views/vendor/{$package}")));

                    if (! $publishedViewPaths->count()) {
                        return '<fg=green;options=bold>NOT PUBLISHED</>';
                    }

                    return "<fg=red;options=bold>PUBLISHED:</> {$publishedViewPaths->join(', ')}";
                },
                'Blade Icons' => function (): string {
                    return File::exists(app()->bootstrapPath('cache/blade-icons.php'))
                        ? '<fg=green;options=bold>CACHED</>'
                        : '<fg=yellow;options=bold>NOT CACHED</>';
                },
                'Panel Components' => function (): string {
                    if (! class_exists(CacheComponentsCommand::class)) {
                        return '<options=bold>NOT AVAILABLE</>';
                    }

                    $path = app()->bootstrapPath('cache/filament/panels');

                    return File::isDirectory($path) && ! File::isEmptyDirectory($path)
                        ? '<fg=green;options=bold>CACHED</>'
                        : '<fg=yellow;options=bold>NOT CACHED</>';
                },
            ]);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->package->basePath('/../config/filament.php') => config_path('filament.php'),
            ], 'filament-config');

            $this->optimizes(
                optimize: 'filament:optimize', /** @phpstan-ignore-line */
                clear: 'filament:optimize-clear', /** @phpstan-ignore-line */
                key: 'filament', /** @phpstan-ignore-line */
            );
        }
    }
}
