<?php

namespace Filament\Support\Assets;

use Composer\InstalledVersions;
use Filament\Support\Facades\FilamentAsset;
use Throwable;

abstract class Asset
{
    protected string $id;

    protected ?string $path = null;

    protected bool $isLoadedOnRequest = false;

    protected ?string $package = null;

    final public function __construct(string $id, ?string $path = null)
    {
        $this->id = $id;
        $this->path = $path;
    }

    public static function make(string $id, ?string $path = null): static
    {
        return app(static::class, ['id' => $id, 'path' => $path]);
    }

    public function loadedOnRequest(bool $condition = true): static
    {
        $this->isLoadedOnRequest = $condition;

        return $this;
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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function isRemote(): bool
    {
        return str($this->getPath())->startsWith(['http://', 'https://', '//']);
    }

    public function getVersion(): string
    {
        $package = $this->getPackage();

        if (blank($package)) {
            return $this->getInstalledVersion('filament/support');
        }

        if (
            ($package === 'app') &&
            filled($appVersion = FilamentAsset::getAppVersion())
        ) {
            return $appVersion;
        }

        try {
            return $this->getInstalledVersion($package);
        } catch (Throwable $exception) {
            // A name Composer does not know (a plugin registering under its
            // own id): the file itself says when it changed.
            return $this->getFileVersion() ?? $this->getInstalledVersion('filament/support');
        }
    }

    /**
     * A dev version (`dev-main`, `5.x-dev` normalized to `5.9999999.9999999.9999999-dev`)
     * reads the same for every commit, so browsers would keep a stale copy of
     * the asset across updates — the installed commit reference busts the
     * cache instead.
     */
    protected function getInstalledVersion(string $package): string
    {
        $version = InstalledVersions::getVersion($package);

        if (filled($version) && (! $this->isDevVersion($version))) {
            return $version;
        }

        $reference = InstalledVersions::getReference($package);

        if (filled($reference)) {
            return substr($reference, 0, 12);
        }

        return $version ?? $this->getFileVersion() ?? '';
    }

    protected function isDevVersion(string $version): bool
    {
        return str_starts_with($version, 'dev-') || str_ends_with($version, '-dev');
    }

    /**
     * The source file's modification time, for assets whose package Composer
     * cannot version.
     */
    protected function getFileVersion(): ?string
    {
        $path = $this->getPath();

        if (blank($path) || $this->isRemote() || (! is_file($path))) {
            return null;
        }

        $modifiedAt = filemtime($path);

        return $modifiedAt === false ? null : (string) $modifiedAt;
    }

    public function isLoadedOnRequest(): bool
    {
        return $this->isLoadedOnRequest;
    }

    abstract public function getPublicPath(): string;
}
