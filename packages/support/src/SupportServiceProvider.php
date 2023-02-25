<?php

namespace Filament\Support;

use Composer\InstalledVersions;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Assets\Js;
use Filament\Support\Commands\AssetsCommand;
use Filament\Support\Commands\CheckTranslationsCommand;
use Filament\Support\Commands\InstallCommand;
use Filament\Support\Commands\UpgradeCommand;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\IconManager;
use HtmlSanitizer\Sanitizer;
use HtmlSanitizer\SanitizerInterface;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SupportServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-support')
            ->hasCommands([
                AssetsCommand::class,
                CheckTranslationsCommand::class,
                InstallCommand::class,
                UpgradeCommand::class,
            ])
            ->hasConfigFile('filament')
            ->hasTranslations()
            ->hasViews(namespace: 'filament');
    }

    public function packageRegistered(): void
    {
        $this->app->scoped(
            AssetManager::class,
            function () {
                return new AssetManager();
            },
        );

        $this->app->scoped(
            IconManager::class,
            function () {
                return new IconManager();
            },
        );

        $this->app->scoped(
            SanitizerInterface::class,
            function () {
                return Sanitizer::create(require __DIR__ . '/../config/html-sanitizer.php');
            },
        );

        $this->app->resolving(AssetManager::class, function () {
            FilamentAsset::register([
                Js::make('support', __DIR__ . '/../dist/index.js'),
                Js::make('async-alpine', __DIR__ . '/../dist/async-alpine.js'),
            ], 'filament/support');
        });
    }

    public function packageBooted(): void
    {
        Blade::directive('captureSlots', function (string $expression): string {
            return "<?php \$slotContents = get_defined_vars(); \$slots = collect({$expression})->mapWithKeys(fn (string \$slot): array => [\$slot => \$slotContents[\$slot] ?? null])->all(); unset(\$slotContents) ?>";
        });

        Blade::directive('filamentScripts', function (string $expression): string {
            return "<?php echo \Filament\Support\Facades\FilamentAsset::renderScripts({$expression}) ?>";
        });

        Blade::directive('filamentStyles', function (string $expression): string {
            return "<?php echo \Filament\Support\Facades\FilamentAsset::renderStyles({$expression}) ?>";
        });

        Str::macro('sanitizeHtml', function (string $html): string {
            return app(SanitizerInterface::class)->sanitize($html);
        });

        Stringable::macro('sanitizeHtml', function (): Stringable {
            /** @phpstan-ignore-next-line */
            return new Stringable(Str::sanitizeHtml($this->value));
        });

        if (class_exists(AboutCommand::class) && class_exists(InstalledVersions::class)) {
            $packages = [
                'filament',
                'forms',
                'notifications',
                'support',
                'tables',
            ];

            AboutCommand::add('Filament', [
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
