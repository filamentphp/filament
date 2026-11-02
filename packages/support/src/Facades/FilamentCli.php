<?php

namespace Filament\Support\Facades;

use Filament\Support\CliManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array<string, array{path: string, viewNamespace: ?string}> getComponentLocations()
 * @method static array<string, array{path: string, viewNamespace: ?string}> getLivewireComponentLocations()
 * @method static void registerComponentLocation(string $path, string $namespace, ?string $viewNamespace)
 * @method static void registerLivewireComponentLocation(string $path, string $namespace, ?string $viewNamespace)
 *
 * @see CliManager
 */
class FilamentCli extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CliManager::class;
    }
}
