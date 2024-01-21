<?php

namespace Filament\Panel\Concerns;

use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;

trait HasAssets
{
    /**
     * @var array<string, array<Asset>>
     */
    protected array $assets = [];

    /**
     * @param  array<Asset>  $assets
     */
    public function assets(array $assets, string $package = 'app'): static
    {
        $this->assets[$package] = [
            ...($this->assets[$package] ?? []),
            ...$assets,
        ];

        return $this;
    }

    public function registerAssets(): void
    {
        foreach ($this->assets as $package => $assets) {
            FilamentAsset::register($assets, $package);
        }
    }
}
