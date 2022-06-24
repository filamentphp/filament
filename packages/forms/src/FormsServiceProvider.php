<?php

namespace Filament\Forms;

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

        return array_reduce(
            $commands,
            static function ($commands, $command) {
                $aliasClass = 'Filament\\Forms\\Commands\\Aliases\\' . class_basename($command);
                
                if (class_exists($aliasClass)) {
                    $commands[] = $aliasClass;
                }

                return $commands;
            },
            $commands
        );
    }
}
