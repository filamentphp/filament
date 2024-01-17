<?php

namespace Filament\Panel\Concerns;

use Filament\Enums\ThemeMode;
use Filament\Support\Assets\Js;
use Filament\Support\Assets\Theme;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Foundation\Vite;

trait HasTheme
{
    protected string | Htmlable | Theme | null $theme = null;

    /**
     * @var string | array<string>
     */
    protected string | array | null $viteTheme = null;

    protected ?string $viteThemeBuildDirectory = null;

    protected ThemeMode $defaultThemeMode = ThemeMode::System;

    protected string | array $themeExtension = 'css';

    /**
     * @param  string | array<string>  $theme
     */
    public function viteTheme(string | array $theme, ?string $buildDirectory = null): static
    {
        $this->viteTheme = $theme;
        $this->viteThemeBuildDirectory = $buildDirectory;
        $this->themeExtension = collect((array) $theme)
            ->map(fn ($item) => pathinfo($item, PATHINFO_EXTENSION))
            ->filter()
            ->first() ?: $this->themeExtension;

        return $this;
    }

    public function theme(string | Htmlable | Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getTheme(): Theme | Js
    {
        if (filled($this->viteTheme)) {
            $this->theme = app(Vite::class)($this->viteTheme, $this->viteThemeBuildDirectory);
        }

        if (blank($this->theme)) {
            return $this->getDefaultTheme();
        }

        if ($this->theme instanceof Theme) {
            return $this->theme;
        }

        if ($this->theme instanceof Htmlable) {
            if ($this->themeExtension === 'js') {
                return Js::make('app')->html($this->theme);
            }

            return Theme::make('app')->html($this->theme);
        }

        $theme = FilamentAsset::getTheme($this->theme);

        return $theme ?? Theme::make('app', path: $this->theme);
    }

    public function getDefaultTheme(): Theme
    {
        return FilamentAsset::getTheme('app');
    }

    public function defaultThemeMode(ThemeMode $mode): static
    {
        $this->defaultThemeMode = $mode;

        return $this;
    }

    public function getDefaultThemeMode(): ThemeMode
    {
        return $this->defaultThemeMode;
    }
}
