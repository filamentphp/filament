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
                default => null,
            };
        }

        return $this;
    }

    public function getAlpineComponents(?array $packages = null): array
    {
        return $this->getAssets($this->alpineComponents, $packages);
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
        $assets = $this->getScripts($packages, true);

        if ($withCore) {
            usort(
                $assets,
                fn (Js $a): int => $a->isCore() ? 1 : -1,
            );
        }

        return view('filament-support::assets', [
            'assets' => $assets,
        ])->render();
    }

    public function getStyles(?array $packages = null): array
    {
        return $this->getAssets($this->styles, $packages);
    }

    public function renderStyles(?array $packages = null): string
    {
        return view('filament-support::assets', [
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
