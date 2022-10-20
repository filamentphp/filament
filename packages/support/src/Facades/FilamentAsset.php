<?php

namespace Filament\Support\Facades;

use Filament\Support\Assets\AssetManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getAlpineComponents(?array $packages = null)
 * @method static array getScripts(?array $packages = null, bool $withCore = true)
 * @method static array getStyles(?array $packages = null)
 * @method static void register(array $assets, ?string $package = null)
 * @method static string renderScripts(?array $packages = null, bool $withCore = true)
 * @method static string renderStyles(?array $packages = null)
 *
 * @see AssetManager
 */
class FilamentAsset extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AssetManager::class;
    }
}
