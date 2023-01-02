<?php

namespace Filament\Tables;

use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Js;
use Filament\Support\PluginServiceProvider;
use Filament\Tables\Testing\TestsActions;
use Filament\Tables\Testing\TestsBulkActions;
use Filament\Tables\Testing\TestsColumns;
use Filament\Tables\Testing\TestsFilters;
use Filament\Tables\Testing\TestsRecords;
use Filament\Tables\Testing\TestsSummaries;
use Illuminate\Filesystem\Filesystem;
use Livewire\Testing\TestableLivewire;

class TablesServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-tables';

    public function packageBooted(): void
    {
        parent::packageBooted();

        if ($this->app->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament/{$file->getFilename()}"),
                ], 'tables-stubs');
            }
        }

        TestableLivewire::mixin(new TestsActions());
        TestableLivewire::mixin(new TestsBulkActions());
        TestableLivewire::mixin(new TestsColumns());
        TestableLivewire::mixin(new TestsFilters());
        TestableLivewire::mixin(new TestsRecords());
        TestableLivewire::mixin(new TestsSummaries());
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        $commands = [
            Commands\MakeColumnCommand::class,
            Commands\MakeTableCommand::class,
        ];

        $aliases = [];

        foreach ($commands as $command) {
            $class = 'Filament\\Tables\\Commands\\Aliases\\' . class_basename($command);

            if (! class_exists($class)) {
                continue;
            }

            $aliases[] = $class;
        }

        return array_merge($commands, $aliases);
    }

    protected function getAssetPackage(): ?string
    {
        return 'filament/tables';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            Js::make('tables', __DIR__ . '/../dist/index.js'),
        ];
    }
}
