<?php

namespace Filament\Forms;

use Filament\Forms\Testing\TestsForms;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\PluginServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Livewire\Testing\TestableLivewire;

class FormsServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-forms';

    public function packageBooted(): void
    {
        parent::packageBooted();

        if ($this->app->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament/{$file->getFilename()}"),
                ], 'forms-stubs');
            }
        }

        TestableLivewire::mixin(new TestsForms());
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        $commands = [
            Commands\MakeFieldCommand::class,
            Commands\MakeFormCommand::class,
            Commands\MakeLayoutComponentCommand::class,
        ];

        $aliases = [];

        foreach ($commands as $command) {
            $class = 'Filament\\Forms\\Commands\\Aliases\\' . class_basename($command);

            if (! class_exists($class)) {
                continue;
            }

            $aliases[] = $class;
        }

        return array_merge($commands, $aliases);
    }

    protected function getAssetPackage(): ?string
    {
        return 'filament/forms';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            AlpineComponent::make('color-picker', __DIR__ . '/../dist/components/color-picker.js'),
            AlpineComponent::make('date-time-picker', __DIR__ . '/../dist/components/date-time-picker.js'),
            AlpineComponent::make('file-upload', __DIR__ . '/../dist/components/file-upload.js'),
            AlpineComponent::make('key-value', __DIR__ . '/../dist/components/key-value.js'),
            AlpineComponent::make('markdown-editor', __DIR__ . '/../dist/components/markdown-editor.js'),
            AlpineComponent::make('rich-editor', __DIR__ . '/../dist/components/rich-editor.js'),
            AlpineComponent::make('select', __DIR__ . '/../dist/components/select.js'),
            AlpineComponent::make('tags-input', __DIR__ . '/../dist/components/tags-input.js'),
            AlpineComponent::make('text-input', __DIR__ . '/../dist/components/text-input.js'),
            AlpineComponent::make('textarea', __DIR__ . '/../dist/components/textarea.js'),
        ];
    }
}
