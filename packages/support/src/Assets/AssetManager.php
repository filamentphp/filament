<?php

namespace Filament\Support\Assets;

use Filament\Support\Colors\ColorManager;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\Arr;
use LogicException;

class AssetManager
{
    /**
     * @var array<string, array<AlpineComponent>>
     */
    protected array $alpineComponents = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    protected array $cssVariables = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    protected array $scriptData = [];

    /**
     * @var array<string, array<Js>>
     */
    protected array $scripts = [];

    /**
     * @var array<string, array<Css>>
     */
    protected array $styles = [];

    /**
     * @var array<string, array<Font>>
     */
    protected array $fonts = [];

    /**
     * @var array<string, Theme>
     */
    protected array $themes = [];

    protected ?string $appVersion = null;

    public function appVersion(?string $version): void
    {
        $this->appVersion = $version;
    }

    public function getAppVersion(): ?string
    {
        return $this->appVersion;
    }

    /**
     * @param  array<Asset>  $assets
     */
    public function register(array $assets, string $package = 'app'): void
    {
        foreach ($assets as $asset) {
            $asset->package($package);

            match (true) {
                $asset instanceof Theme => $this->themes[$asset->getId()] = $asset,
                $asset instanceof AlpineComponent => $this->alpineComponents[$package][] = $asset,
                $asset instanceof Css => $this->styles[$package][] = $asset,
                $asset instanceof Font => $this->fonts[$package][] = $asset,
                $asset instanceof Js => $this->scripts[$package][] = $asset,
                default => null,
            };
        }
    }

    /**
     * @param  array<string, mixed>  $variables
     */
    public function registerCssVariables(array $variables, ?string $package = null): void
    {
        $this->cssVariables[$package] = [
            ...($this->cssVariables[$package] ?? []),
            ...$variables,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function registerScriptData(array $data, ?string $package = null): void
    {
        $this->scriptData[$package] = [
            ...($this->scriptData[$package] ?? []),
            ...$data,
        ];
    }

    /**
     * @param  array<string> | null  $packages
     * @return array<AlpineComponent>
     */
    public function getAlpineComponents(?array $packages = null): array
    {
        /** @var array<AlpineComponent> $assets */
        $assets = $this->getAssets($this->alpineComponents, $packages);

        return $assets;
    }

    public function getAlpineComponentSrc(string $id, string $package = 'app'): string
    {
        /** @var array<AlpineComponent> $components */
        $components = $this->getAlpineComponents([$package]);

        foreach ($components as $component) {
            if ($component->getId() !== $id) {
                continue;
            }

            return $component->getSrc();
        }

        throw new LogicException("Alpine component with ID [{$id}] not found for package [{$package}].");
    }

    /**
     * @param  array<string> | null  $packages
     * @return array<string, mixed>
     */
    public function getScriptData(?array $packages = null): array
    {
        $data = [];

        foreach ($this->scriptData as $package => $packageData) {
            if (
                ($packages !== null) &&
                filled($package) &&
                (! in_array($package, $packages))
            ) {
                continue;
            }

            $data = [
                ...$data,
                ...$packageData,
            ];
        }

        return $data;
    }

    public function getScriptSrc(string $id, string $package = 'app'): string
    {
        /** @var array<Js> $scripts */
        $scripts = $this->getScripts([$package]);

        foreach ($scripts as $script) {
            if ($script->getId() !== $id) {
                continue;
            }

            return $script->getSrc();
        }

        throw new LogicException("Script with ID [{$id}] not found for package [{$package}].");
    }

    /**
     * @param  array<string> | null  $packages
     * @return array<Js>
     */
    public function getScripts(?array $packages = null, bool $withCore = true): array
    {
        /** @var array<Js> $assets */
        $assets = $this->getAssets($this->scripts, $packages);

        if (! $withCore) {
            $assets = array_filter(
                $assets,
                fn (Js $asset): bool => ! $asset->isCore(),
            );
        }

        return $assets;
    }

    /**
     * @param  array<string> | null  $packages
     */
    public function renderScripts(?array $packages = null, bool $withCore = false): string
    {
        /** @var array<Js> $assets */
        $assets = $this->getScripts($packages, $withCore);

        if ($withCore) {
            usort(
                $assets,
                fn (Js $asset): int => $asset->isCore() ? 1 : -1,
            );
        }

        return view('filament::assets', [
            'assets' => $assets,
            'data' => $this->getScriptData($packages),
        ])->render();
    }

    /**
     * @param  array<string> | null  $packages
     * @return array<Font>
     */
    public function getFonts(?array $packages = null): array
    {
        /** @var array<Font> $assets */
        $assets = $this->getAssets($this->fonts, $packages);

        return $assets;
    }

    /**
     * @param  array<string> | null  $packages
     * @return array<Css>
     */
    public function getStyles(?array $packages = null): array
    {
        /** @var array<Css> $assets */
        $assets = $this->getAssets($this->styles, $packages);

        return $assets;
    }

    public function getStyleHref(string $id, string $package = 'app'): string
    {
        /** @var array<Css> $styles */
        $styles = $this->getStyles([$package]);

        foreach ($styles as $style) {
            if ($style->getId() !== $id) {
                continue;
            }

            return $style->getHref();
        }

        throw new LogicException("Stylesheet with ID [{$id}] not found for package [{$package}].");
    }

    /**
     * @param  array<string> | null  $packages
     * @return array<string, mixed>
     */
    public function getCssVariables(?array $packages = null): array
    {
        $variables = [];

        foreach ($this->cssVariables as $package => $packageVariables) {
            if (
                ($packages !== null) &&
                filled($package) &&
                (! in_array($package, $packages))
            ) {
                continue;
            }

            $variables = [
                ...$variables,
                ...$packageVariables,
            ];
        }

        return $variables;
    }

    /**
     * @param  array<string> | null  $packages
     */
    public function renderStyles(?array $packages = null): string
    {
        $cssVariables = $this->getCssVariables($packages);
        $customColors = [];

        $defaultColorNames = array_keys(ColorManager::DEFAULT_COLORS);

        foreach (FilamentColor::getColors() as $name => $palette) {
            foreach (array_keys($palette) as $shade) {
                $cssVariables["{$name}-{$shade}"] = $this->resolveColorShadeFromPalette($palette, $shade);
            }

            if (! in_array($name, $defaultColorNames)) {
                $customColors[$name] = array_keys($palette);
            }
        }

        return view('filament::assets', [
            'assets' => [
                ...$this->getStyles($packages),
                ...array_map(
                    fn (Font $font): Css => $font->getStyle(),
                    $this->getFonts($packages),
                ),
            ],
            'cssVariables' => $cssVariables,
            'customColors' => $customColors,
        ])->render();
    }

    /**
     * @param  array<int | string, string | int>  $palette
     */
    protected function resolveColorShadeFromPalette(array $palette, string | int $shade): string
    {
        $color = $palette[$shade];

        while (! str_starts_with($color, 'oklch(')) {
            if ($color === 0) {
                return 'oklch(1 0 0)';
            }

            $color = $palette[$color];
        }

        return $color;
    }

    public function getTheme(?string $id): ?Theme
    {
        return $this->getThemes()[$id] ?? null;
    }

    /**
     * @return array<string, Theme>
     */
    public function getThemes(): array
    {
        return $this->themes;
    }

    /**
     * @param  array<string, array<Asset>>  $assets
     * @param  array<string> | null  $packages
     * @return array<Asset>
     */
    protected function getAssets(array $assets, ?array $packages = null): array
    {
        if ($packages !== null) {
            $assets = Arr::only($assets, $packages);
        }

        return Arr::flatten($assets);
    }
}
