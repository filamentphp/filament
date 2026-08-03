<?php

namespace Filament\Support;

use BackedEnum;
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
use Filament\Support\Contracts\LoadingIndicator;
use Filament\Support\Enums\GridDirection;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Icons\IconManager;
use Filament\Support\Livewire\Partials\DataStoreOverride;
use Filament\Support\Livewire\Partials\PartialsComponentHook;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\DefaultLoadingIndicator;
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

        $this->app->singleton(
            TimezoneManager::class,
            fn () => new TimezoneManager,
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
            HtmlSanitizerConfig::class,
            fn (): HtmlSanitizerConfig => (new HtmlSanitizerConfig)
                ->allowSafeElements()
                ->allowRelativeLinks()
                ->allowRelativeMedias()
                ->allowAttribute('class', allowedElements: '*')
                ->allowAttribute('data-color', allowedElements: '*')
                ->allowAttribute('data-cols', allowedElements: '*')
                ->allowAttribute('data-col-span', allowedElements: '*')
                ->allowAttribute('data-from-breakpoint', allowedElements: '*')
                ->allowAttribute('data-id', allowedElements: '*')
                ->allowAttribute('data-type', allowedElements: '*')
                ->allowAttribute('style', allowedElements: '*')
                ->allowAttribute('width', allowedElements: 'img')
                ->allowAttribute('height', allowedElements: 'img')
                ->withMaxInputLength(500000),
        );

        $this->app->scoped(
            HtmlSanitizerInterface::class,
            fn (): HtmlSanitizer => new HtmlSanitizer(
                $this->app->make(HtmlSanitizerConfig::class),
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
        $this->app->bind(LoadingIndicator::class, DefaultLoadingIndicator::class);

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

        Blade::directive('capture', function (string $expression): string {
            [$name, $arguments] = str_contains($expression, ',') ?
                array_map('trim', explode(',', $expression, 2)) :
                [$expression, ''];

            return "
                <?php {$name} = (function (\$args) {
                    return function ({$arguments}) use (\$args) {
                        extract(\$args, EXTR_SKIP);
                        ob_start(); ?>
            ";
        });

        Blade::directive('endcapture', function (): string {
            return "
                <?php return new \Illuminate\Support\HtmlString(ob_get_clean()); };
                    })(get_defined_vars()); ?>
            ";
        });

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

        // When updating this macro, also update the corresponding method in `Filament\Support\View\ComponentAttributeBag`.
        ComponentAttributeBag::macro('color', function (string | HasColor $component, string | array | null $color): ComponentAttributeBag {
            if (is_array($color)) {
                return $this
                    ->class(['fi-color'])
                    ->style(FilamentColor::getComponentCustomStyles($component, $color));
            }

            return $this->class(FilamentColor::getComponentClasses($component, $color));
        });

        // When updating this macro, also update the corresponding method in `Filament\Support\View\ComponentAttributeBag`.
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

        // When updating this macro, also update the corresponding method in `Filament\Support\View\ComponentAttributeBag`.
        ComponentAttributeBag::macro('gridColumn', function (array | int | string | null $span = [], array | int | string | null $start = [], array | int | string | null $order = [], bool $isHidden = false): ComponentAttributeBag {
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

        Str::macro('sanitizeUrl', function (?string $url, array $allowedSchemes = ['http', 'https']): ?string {
            if (blank($url)) {
                return null;
            }

            // Reject URLs containing raw whitespace or control characters. Legitimate URLs
            // percent-encode these; a raw byte here is either a typo or an obfuscation attempt.
            if (preg_match('/[\x00-\x20\x7F]/', $url)) {
                return null;
            }

            // Predict the scheme the browser will see after HTML-decoding the attribute value.
            // We pre-decode ASCII numeric entities ourselves because `html_entity_decode(ENT_HTML5)`
            // replaces "forbidden" C0 control character entities (e.g. `&#0;`) with U+FFFD, which
            // would mask them from the control-character strip below.
            $decoded = preg_replace_callback(
                '/&#(?:x([0-9a-f]+)|([0-9]+));?/i',
                function (array $match): string {
                    $code = ($match[1] !== '') ? hexdec($match[1]) : (int) $match[2];

                    return ($code <= 127) ? chr((int) $code) : $match[0];
                },
                $url,
            );
            $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Reject if either the HTML-decoded form or its percent-decoded form contains
            // control characters — catches `%09`-style obfuscation that would be dangerous
            // if the URL is later decoded by other code.
            if (
                preg_match('/[\x00-\x1F\x7F]/', $decoded) ||
                preg_match('/[\x00-\x1F\x7F]/', rawurldecode($decoded))
            ) {
                return null;
            }

            // Scheme check uses the HTML-decoded form only — the browser does NOT percent-decode
            // the scheme, so a `%6A` literally in the scheme position is not dangerous.
            if (
                preg_match('/^([a-z][a-z0-9+\-.]*):/i', $decoded, $matches) &&
                (! in_array(strtolower($matches[1]), array_map(strtolower(...), $allowedSchemes), strict: true))
            ) {
                return null;
            }

            return $url;
        });

        Stringable::macro('sanitizeUrl', function (array $allowedSchemes = ['http', 'https']): Stringable {
            /** @phpstan-ignore-next-line */
            return new Stringable(Str::sanitizeUrl($this->value, $allowedSchemes));
        });

        Str::macro('sanitizeCssColor', function (string | BackedEnum | null $color): ?string {
            if ($color instanceof BackedEnum) {
                $color = $color->value;
            }

            if (blank($color)) {
                return null;
            }

            $color = trim((string) $color);

            // Accept hex colors: `#rgb`, `#rgba`, `#rrggbb`, `#rrggbbaa`.
            if (preg_match('/^#(?:[0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $color)) {
                return $color;
            }

            // Accept a bare CSS keyword such as `red` or `transparent`.
            if (preg_match('/^[a-z]+$/i', $color)) {
                return $color;
            }

            // Security: allow functional color notations, but forbid the CSS metacharacters
            // `( ) ; : " '` inside the parentheses so the value cannot break out of the
            // `background-color` declaration to inject additional properties (e.g.
            // `red;position:fixed;background-image:url(//attacker)`) or extra function calls.
            if (preg_match('/^(?:rgb|rgba|hsl|hsla|hwb|lab|lch|oklab|oklch|color)\([^();:"\']*\)$/i', $color)) {
                return $color;
            }

            return null;
        });

        Stringable::macro('sanitizeCssColor', function (): Stringable {
            /** @phpstan-ignore-next-line */
            return new Stringable(Str::sanitizeCssColor($this->value));
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
