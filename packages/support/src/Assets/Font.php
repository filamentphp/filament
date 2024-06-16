<?php

namespace Filament\Support\Assets;

class Font extends Asset
{
    public function getStyle(): Css
    {
        return Css::make($this->getId(), path: $this->getPath() . DIRECTORY_SEPARATOR . 'index.css')
            ->relativePublicPath($this->getRelativePublicPath() . DIRECTORY_SEPARATOR . 'index.css');
    }

    public function getRelativePublicPath(): string
    {
        $path = config('filament.assets_path', '');

        return ltrim("{$path}/fonts/{$this->getPackage()}/{$this->getId()}", '/');
    }

    public function getPublicPath(): string
    {
        return public_path($this->getRelativePublicPath());
    }
}
