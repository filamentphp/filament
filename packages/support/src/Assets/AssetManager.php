<?php

namespace Filament\Support\Assets;

use Exception;
use Illuminate\Support\Arr;

class AssetManager
{
    /**
     * @var array<string, array<AlpineComponent>>
     */
    protected array $alpineComponents = [];

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
     * @var array<string, Theme>
     */
    protected array $themes = [];

    /**
     * @param  array<Asset>  $assets
     */
    public function register(array $assets, string $package): void
    {
        foreach ($assets as $asset) {
            $asset->package($package);

            match ($asset::class) {
                AlpineComponent::class => $this->alpineComponents[$package][] = $asset,
                Css::class => $this->styles[$package][] = $asset,
                Js::class => $this->scripts[$package][] = $asset,
                /** @phpstan-ignore-next-line */
                Theme::class => $this->themes[$asset->getId()] = $asset,
                default => null,
            };
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function registerScriptData(array $data, string $package): void
    {
        $this->scriptData[$package] = array_merge($this->scriptData[$package] ?? [], $data);
    }

    /**
     * @param  array<string> | null  $packages
     * @return array<Asset>
     */
    public function getAlpineComponents(?array $packages = null): array
    {
        return $this->getAssets($this->alpineComponents, $packages);
    }

    public function getAlpineComponentSrc(string $id, string $package): string
    {
        /** @var array<AlpineComponent> $components */
        $components = $this->getAlpineComponents([$package]);

        foreach ($components as $component) {
            if ($component->getId() !== $id) {
                continue;
            }

            return $component->getSrc();
        }

        throw new Exception("Alpine component with ID [{$id}] not found for package [{$package}].");
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
                ($package !== null) &&
                (! in_array($package, $packages))
            ) {
                continue;
            }

            $data = array_merge($data, $packageData);
        }

        return $data;
    }

    /**
     * @param  array<string> | null  $packages
     * @return array<Asset>
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
     * @return array<Asset>
     */
    public function getStyles(?array $packages = null): array
    {
        return $this->getAssets($this->styles, $packages);
    }

    /**
     * @param  array<string> | null  $packages
     */
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
