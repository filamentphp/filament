<?php

namespace Filament\Support\Facades;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Font;
use Filament\Support\Assets\Js;
use Filament\Support\Assets\Theme;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void appVersion(?string $version)
 * @method static array<AlpineComponent> getAlpineComponents(?array<string> $packages = null)
 * @method static string getAlpineComponentSrc(string $id, string $package = 'app')
 * @method static ?string getAppVersion()
 * @method static array<Font> getFonts(?array<string> $packages = null)
 * @method static array<string, mixed> getScriptData(?array<string> $packages = null)
 * @method static string getScriptSrc(string $id, string $package = 'app')
 * @method static array<Js> getScripts(?array<string> $packages = null, bool $withCore = true)
 * @method static string getStyleHref(string $id, string $package = 'app')
 * @method static array<Css> getStyles(?array<string> $packages = null)
 * @method static Theme | null getTheme(string $id)
 * @method static array<string, Theme> getThemes()
 * @method static string renderScripts(?array<string> $packages = null, bool $withCore = true)
 * @method static string renderStyles(?array<string> $packages = null)
 *
 * @see AssetManager
 */
class FilamentAsset extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AssetManager::class;
    }

    /**
     * @param  array<Asset>  $assets
     */
    public static function register(array $assets, string $package = 'app'): void
    {
        static::resolved(function (AssetManager $assetManager) use ($assets, $package): void {
            $assetManager->register($assets, $package);
        });
    }

    /**
     * @param  array<string, mixed>  $variables
     */
    public static function registerCssVariables(array $variables, ?string $package = null): void
    {
        static::resolved(function (AssetManager $assetManager) use ($variables, $package): void {
            $assetManager->registerCssVariables($variables, $package);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function registerScriptData(array $data, ?string $package = null): void
    {
        static::resolved(function (AssetManager $assetManager) use ($data, $package): void {
            $assetManager->registerScriptData($data, $package);
        });
    }
}
