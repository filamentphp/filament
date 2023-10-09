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
         $path = config('filament.assets_path', '');

         return (string) str("{$path}/js/{$this->getPackage()}/components/{$this->getId()}.js")->start('/');
    }

    public function getSrc(): string
    {
        return asset($this->getRelativePublicPath()) . '?v=' . $this->getVersion();
    }
}
