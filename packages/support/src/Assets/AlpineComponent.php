<?php

namespace Filament\Support\Assets;

class AlpineComponent extends Asset
{
    public function getPublicPath(): string
    {
        $path = '';

        $package = $this->getPackage();

        if (filled($package)) {
            $path .= $package;
            $path .= DIRECTORY_SEPARATOR;
        }

        $path .= 'components';
        $path .= DIRECTORY_SEPARATOR;
        $path .= "{$this->getId()}.js";

        return public_path("js/filament/{$path}");
    }
}
