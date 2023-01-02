<?php

namespace Filament\Support\Assets;

use Composer\InstalledVersions;

abstract class Asset
{
    protected string $id;

    protected ?string $path = null;

    protected ?string $package = null;

    final public function __construct(string $id, ?string $path = null)
    {
        $this->id = $id;
        $this->path = $path;
    }

    public static function make(string $id, ?string $path = null): static
    {
        return new static($id, $path);
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getVersion(): string
    {
        return InstalledVersions::getVersion($this->getPackage() ?? 'filament/support');
    }

    abstract public function getPublicPath(): string;
}
