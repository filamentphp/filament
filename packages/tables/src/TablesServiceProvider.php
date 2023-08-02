<?php

namespace Filament\Tables;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tables\Testing\TestsActions;
use Filament\Tables\Testing\TestsBulkActions;
use Filament\Tables\Testing\TestsColumns;
use Filament\Tables\Testing\TestsFilters;
use Filament\Tables\Testing\TestsRecords;
use Filament\Tables\Testing\TestsSummaries;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TablesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-tables')
            ->hasCommands($this->getCommands())
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Js::make('tables', __DIR__ . '/../dist/index.js'),
        ], 'filament/tables');

        if ($this->app->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament/{$file->getFilename()}"),
                ], 'filament-stubs');
            }
        }

        Blade::directive('table', function ($table) {
            $result = Blade::render(<<<BLADE
                @volt
                    <?php

                    use Filament\Tables\Table;

                    Livewire\Volt\uses([
                        Filament\Forms\Concerns\InteractsWithForms::class,
                        Filament\Forms\Contracts\HasForms::class,
                        Filament\Tables\Concerns\InteractsWithTable::class,
                        Filament\Tables\Contracts\HasTable::class,
                    ]);

                    \$table = fn (Table \$table): Table => {$table};

                    ?>

                    <div>
                        <div>
                            {{ \$this->table }}
                        </div>
                    </div>
                @endvolt
            BLADE);

            // dd($result);

            return $result;
        });

        Testable::mixin(new TestsActions());
        Testable::mixin(new TestsBulkActions());
        Testable::mixin(new TestsColumns());
        Testable::mixin(new TestsFilters());
        Testable::mixin(new TestsRecords());
        Testable::mixin(new TestsSummaries());
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

        return [
            ...$commands,
            ...$aliases,
        ];
    }
}
