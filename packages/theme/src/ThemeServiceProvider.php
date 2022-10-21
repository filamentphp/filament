<?php

namespace Filament\Theme;

use Filament\Facades\Filament;
use Filament\Support\Assets\Css;
use Filament\Support\PluginServiceProvider;

class ThemeServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-theme';

    protected function getAssetPackage(): ?string
    {
        return null;
    }

    protected function getThemeCss(): Css
    {
        return Css::make('theme', __DIR__ . '/../dist/index.css');
    }

    protected function getAssets(): array
    {
        return [
            $this->getThemeCss(),
        ];
    }

    public function packageBooted(): void
    {
        Filament::registerTheme($this->getThemeCss()->getHref());
    }
}
