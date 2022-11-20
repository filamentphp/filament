<?php

namespace Filament\Support\Assets;

use Illuminate\Support\Arr;

class AssetManager
{
    protected array $alpineComponents = [];

    protected array $scriptData = [];

    protected array $scripts = [];

    protected array $styles = [];

    protected array $themes = [];

    public function register(array $assets, ?string $package = null): void
    {
        foreach ($assets as $asset) {
            $asset->package($package);

            match ($asset::class) {
                AlpineComponent::class => $this->alpineComponents[$package][] = $asset,
                Css::class => $this->styles[$package][] = $asset,
                Js::class => $this->scripts[$package][] = $asset,
                Theme::class => $this->themes[$asset->getId()] = $asset,
                default => null,
            };
        }
    }

    public function registerScriptData(array $data, ?string $package = null): void
    {
        $this->scriptData[$package] = array_merge($this->scriptData[$package] ?? [], $data);
    }

    public function getAlpineComponents(?array $packages = null): array
    {
        return $this->getAssets($this->alpineComponents, $packages);
    }

    public function getScriptData(?array $packages = null): array
    {
        $data = [];

        foreach ($this->scriptData as $package => $packageData) {
            if (
                ($packages !== null) &&
                ($package !== null) &&
                (! in_array($package, $packages))
            ) {
                continue;
            }

            $data = array_merge($data, $packageData);
        }

        return $data;
    }

    public function getScripts(?array $packages = null, bool $withCore = true): array
    {
        $assets = $this->getAssets($this->scripts, $packages);

        if (! $withCore) {
            $assets = array_filter(
                $assets,
                fn (Js $asset): bool => ! $asset->isCore(),
            );
        }

        return $assets;
    }

    public function renderScripts(?array $packages = null, bool $withCore = false): string
    {
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

    public function getStyles(?array $packages = null): array
    {
        return $this->getAssets($this->styles, $packages);
    }

    public function renderStyles(?array $packages = null): string
    {
        return view('filament::assets', [
            'assets' => $this->getStyles($packages),
        ])->render();
    }

    public function getTheme(?string $id): ?Theme
    {
        return $this->themes[$id] ?? null;
    }

    public function getThemes(): array
    {
        return $this->themes;
    }

    protected function getAssets(array $assets, ?array $packages = null): array
    {
        if ($packages === null) {
            return Arr::flatten($assets);
        }

        $packages[] = null;

        return Arr::only($assets, $packages);
    }
}
