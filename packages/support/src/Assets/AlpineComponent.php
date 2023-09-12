<?php

namespace Filament\Support\Assets;

class AlpineComponent extends Asset
{
    public function getPublicPath(): string
    {
        return public_path($this->getRelativePublicPath());
    }

    public function getRelativePublicPath(): string
    {
        return "vendor/{$this->getPackage()}/components/js/{$this->getId()}.js";
    }

    public function getSrc(): string
    {
        return asset($this->getRelativePublicPath()) . '?v=' . $this->getVersion();
    }
}
