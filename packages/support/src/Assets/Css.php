<?php

namespace Filament\Support\Assets;

class Css extends Asset
{
    public function getHref(): string
    {
        $href = '/css/filament/';

        $package = $this->getPackage();

        if (filled($package)) {
            $href .= "{$package}/";
        }

        $href .= "{$this->getName()}.css";

        return $href;
    }

    public function getPublicPath(): string
    {
        $path = '';

        $package = $this->getPackage();

        if (filled($package)) {
            $path .= $package;
            $path .= DIRECTORY_SEPARATOR;
        }

        $path .= "{$this->getName()}.css";

        return public_path("css/filament/{$path}");
    }
}
