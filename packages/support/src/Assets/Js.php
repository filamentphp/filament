<?php

namespace Filament\Support\Assets;

class Js extends Asset
{
    protected bool $isAsync = false;

    protected bool $isDeferred = true;

    public function async(bool $condition = true): static
    {
        $this->isAsync = $condition;

        return $this;
    }

    public function defer(bool $condition = true): static
    {
        $this->isDeferred = $condition;

        return $this;
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    public function isDeferred(): bool
    {
        return $this->isDeferred;
    }

    public function getSrc(): string
    {
        $href = '/js/filament/';

        $package = $this->getPackage();

        if (filled($package)) {
            $href .= "{$package}/";
        }

        $href .= "{$this->getName()}.js";

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

        $path .= "{$this->getName()}.js";

        return public_path("js/filament/{$path}");
    }
}
