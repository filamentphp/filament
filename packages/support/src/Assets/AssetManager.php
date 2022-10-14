<?php

namespace Filament\Support\Assets;

use Illuminate\Support\Arr;

class AssetManager
{
    protected array $alpineComponents = [];

    protected array $scripts = [];

    protected array $styles = [];

    public function register(array $assets, ?string $package = null): static
    {
        foreach ($assets as $asset) {
            $asset->package($package);

            match ($asset::class) {
                AlpineComponent::class => $this->alpineComponents[$package][] = $asset,
                Css::class => $this->styles[$package][] = $asset,
                Js::class => $this->scripts[$package][] = $asset,
            };
        }

        return $this;
    }

    public function getAlpineComponents(?array $packages = null): array
    {
        return $this->getAssets($this->alpineComponents, $packages);
    }

    public function getScripts(?array $packages = null): array
    {
        return $this->getAssets($this->scripts, $packages);
    }

    public function renderScripts(?array $packages = null): string
    {
        return view('filament-support::assets.scripts', [
            'assets' => $this->getScripts($packages),
        ])->render();
    }

    public function getStyles(?array $packages = null): array
    {
        return $this->getAssets($this->styles, $packages);
    }

    public function renderStyles(?array $packages = null): string
    {
        return view('filament-support::assets.styles', [
            'assets' => $this->getStyles($packages),
        ])->render();
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
