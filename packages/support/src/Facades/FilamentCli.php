<?php

namespace Filament\Support\Facades;

use Filament\Support\CliManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array<string, array{path: string, viewNamespace: ?string}> getComponentLocations()
 * @method static array<string, array{path: string, viewNamespace: ?string}> getLivewireComponentLocations()
 * @method static void registerComponentLocation(string $directory, string $namespace, ?string $viewNamespace = null)
 * @method static void registerLivewireComponentLocation(string $directory, string $namespace, ?string $viewNamespace = null)
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
