<?php

namespace Filament\Support;

use Composer\InstalledVersions;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\ColorManager;
use Filament\Support\Commands\AssetsCommand;
use Filament\Support\Commands\CheckTranslationsCommand;
use Filament\Support\Commands\InstallCommand;
use Filament\Support\Commands\UpgradeCommand;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\IconManager;
use Filament\Support\View\ViewManager;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
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
                InstallCommand::class,
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
            fn () => new AssetManager(),
        );

        $this->app->scoped(
            ColorManager::class,
            fn () => new ColorManager(),
        );

        $this->app->scoped(
            IconManager::class,
            fn () => new IconManager(),
        );

        $this->app->scoped(
            ViewManager::class,
            fn () => new ViewManager(),
        );

        $this->app->scoped(
            HtmlSanitizerInterface::class,
            fn (): HtmlSanitizer => new HtmlSanitizer(
                (new HtmlSanitizerConfig())
                    ->allowSafeElements()
                    ->allowRelativeLinks()
                    ->allowRelativeMedias()
                    ->allowAttribute('class', allowedElements: '*')
                    ->allowAttribute('style', allowedElements: '*')
                    ->withMaxInputLength(500000),
            ),
        );
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Js::make('async-alpine', __DIR__ . '/../dist/async-alpine.js'),
            Css::make('support', __DIR__ . '/../dist/index.css'),
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

        Str::macro('sanitizeHtml', function (string $html): string {
            return app(HtmlSanitizerInterface::class)->sanitize($html);
        });

        Stringable::macro('sanitizeHtml', function (): Stringable {
            /** @phpstan-ignore-next-line */
            return new Stringable(Str::sanitizeHtml($this->value));
        });

        if (class_exists(InstalledVersions::class)) {
            $packages = [
                'filament',
                'forms',
                'notifications',
                'support',
                'tables',
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
            ]);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->package->basePath('/../config/filament.php') => config_path('filament.php'),
            ], 'filament-config');
        }
    }
}
