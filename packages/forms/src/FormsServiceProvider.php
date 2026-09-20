<?php

namespace Filament\Forms;

use Faker\Generator;
use Filament\Forms\Components\RichEditor\RichContentFakerProvider;
use Filament\Forms\Components\TableSelect\Livewire\TableSelectLivewireComponent;
use Filament\Forms\Testing\TestsFormComponentActions;
use Filament\Forms\Testing\TestsForms;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FormsServiceProvider extends PackageServiceProvider
{
    public function packageRegistered(): void
    {
        if (! class_exists(Generator::class)) {
            return;
        }

        $registerRichContentFakerProvider = static function (Generator $faker): void {
            foreach ($faker->getProviders() as $provider) {
                if ($provider instanceof RichContentFakerProvider) {
                    return;
                }
            }

            $faker->addProvider(new RichContentFakerProvider($faker));
        };

        $fakerBindings = [
            Generator::class,
            ...array_filter(
                array_keys($this->app->getBindings()),
                static fn (string $binding): bool => str_starts_with($binding, Generator::class . ':'),
            ),
        ];

        foreach ($fakerBindings as $fakerBinding) {
            if (! $this->app->resolved($fakerBinding)) {
                continue;
            }

            $registerRichContentFakerProvider($this->app->make($fakerBinding));
        }

        $this->app->afterResolving(Generator::class, $registerRichContentFakerProvider);
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-forms')
            ->hasCommands([
                Commands\MakeRichContentCustomBlockCommand::class,
                Commands\MakeFieldCommand::class,
                Commands\MakeFormCommand::class,
                Commands\MakeLivewireFormCommand::class,
            ])
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            AlpineComponent::make('checkbox-list', __DIR__ . '/../dist/components/checkbox-list.js'),
            AlpineComponent::make('code-editor', __DIR__ . '/../dist/components/code-editor.js'),
            AlpineComponent::make('color-picker', __DIR__ . '/../dist/components/color-picker.js'),
            AlpineComponent::make('date-time-picker', __DIR__ . '/../dist/components/date-time-picker.js'),
            AlpineComponent::make('file-upload', __DIR__ . '/../dist/components/file-upload.js'),
            AlpineComponent::make('key-value', __DIR__ . '/../dist/components/key-value.js'),
            AlpineComponent::make('markdown-editor', __DIR__ . '/../dist/components/markdown-editor.js'),
            AlpineComponent::make('rich-editor', __DIR__ . '/../dist/components/rich-editor.js'),
            AlpineComponent::make('select', __DIR__ . '/../dist/components/select.js'),
            AlpineComponent::make('slider', __DIR__ . '/../dist/components/slider.js'),
            AlpineComponent::make('tags-input', __DIR__ . '/../dist/components/tags-input.js'),
            AlpineComponent::make('textarea', __DIR__ . '/../dist/components/textarea.js'),
        ], 'filament/forms');

        if ($this->app->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament/{$file->getFilename()}"),
                ], 'filament-stubs');
            }
        }

        Livewire::component('table-select', TableSelectLivewireComponent::class);

        Testable::mixin(new TestsForms);
        Testable::mixin(new TestsFormComponentActions);
    }
}
