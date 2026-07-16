<?php

namespace Filament\Schemas;

use Filament\Schemas\Testing\TestsSchemas;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

use function Livewire\on;

class SchemasServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-schemas')
            ->hasCommands([
                Commands\MakeComponentCommand::class,
                Commands\MakeLivewireSchemaCommand::class,
                Commands\MakeSchemaCommand::class,
            ])
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            AlpineComponent::make('actions', __DIR__ . '/../dist/components/actions.js'),
            AlpineComponent::make('tabs', __DIR__ . '/../dist/components/tabs.js'),
            AlpineComponent::make('wizard', __DIR__ . '/../dist/components/wizard.js'),
            Js::make('schemas', __DIR__ . '/../dist/index.js'),
        ], 'filament/schemas');

        if ($this->app->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament/{$file->getFilename()}"),
                ], 'filament-stubs');
            }
        }

        Testable::mixin(new TestsSchemas);

        on('call', function (object $component, string $method, array $params): void {
            if (! in_array($method, ['_startUpload', '_finishUpload', '_uploadErrored', '_removeUpload'], strict: true)) {
                return;
            }

            if (! (
                method_exists($component, 'shouldRestrictFileUploadsToSchemaComponents') &&
                method_exists($component, 'isFileUploadForSchemaComponent') &&
                $component->shouldRestrictFileUploadsToSchemaComponents()
            )) {
                return;
            }

            abort_unless($component->isFileUploadForSchemaComponent($params[0] ?? ''), 403);
        });
    }
}
