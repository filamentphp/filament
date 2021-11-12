<?php

namespace Filament\Forms;

use Illuminate\Support\Arr;
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
        Arr::macro('moveElementAfter', function (array $array, $keyToMoveAfter): array {
            $keys = array_keys($array);

            $indexToMoveAfter = array_search($keyToMoveAfter, $keys);
            $keyToMoveBefore = $keys[$indexToMoveAfter + 1];

            $keys[$indexToMoveAfter + 1] = $keyToMoveAfter;
            $keys[$indexToMoveAfter] = $keyToMoveBefore;

            $newArray = [];

            foreach ($keys as $key) {
                $newArray[$key] = $array[$key];
            }

            return $newArray;
        });

        Arr::macro('moveElementBefore', function (array $array, $keyToMoveBefore): array {
            $keys = array_keys($array);

            $indexToMoveBefore = array_search($keyToMoveBefore, $keys);
            $keyToMoveAfter = $keys[$indexToMoveBefore - 1];

            $keys[$indexToMoveBefore - 1] = $keyToMoveBefore;
            $keys[$indexToMoveBefore] = $keyToMoveAfter;

            $newArray = [];

            foreach ($keys as $key) {
                $newArray[$key] = $array[$key];
            }

            return $newArray;
        });
    }
}
