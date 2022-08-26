<?php

namespace Filament\Forms;

use Filament\Forms\Testing\TestsForms;
use Livewire\Testing\TestableLivewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FormsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('forms')
            ->hasCommands($this->getCommands())
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews();
    }

    protected function getCommands(): array
    {
        $commands = [
            Commands\InstallCommand::class,
            Commands\MakeFieldCommand::class,
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

    public function packageBooted(): void
    {
        TestableLivewire::mixin(new TestsForms());
    }
}
