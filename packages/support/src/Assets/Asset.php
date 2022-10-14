<?php

namespace Filament\Support\Assets;

abstract class Asset
{
    protected string $name;

    protected string $path;

    protected ?string $package = null;

    final public function __construct(string $name, string $path)
    {
        $this->name = $name;
        $this->path = $path;
    }

    public static function make(string $name, string $path): static
    {
        return new static($name, $path);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function package(?string $package): static
    {
        $this->package = $package;

        return $this;
    }

    public function getPackage(): ?string
    {
        return $this->package;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function isRemote(): bool
    {
        return str($this->getPath())->startsWith(['http://', 'https://', '//']);
    }

    abstract public function getPublicPath(): string;
}
