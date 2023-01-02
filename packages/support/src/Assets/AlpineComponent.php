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
        $path = 'js';
        $path .= DIRECTORY_SEPARATOR;

        $package = $this->getPackage();

        if (filled($package)) {
            $path .= $package;
            $path .= DIRECTORY_SEPARATOR;
        }

        $path .= 'components';
        $path .= DIRECTORY_SEPARATOR;
        $path .= "{$this->getId()}.js";

        return $path;
    }

    public function getUrl(): string
    {
        return asset("{$this->getRelativePublicPath()}?v={$this->getVersion()}");
    }
}
