<?php

namespace Filament\Tables\Columns;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3v3\AwsS3Adapter;

class ImageColumn extends Column
{
    protected string $view = 'tables::columns.image-column';

    protected string $disk;

    protected int | string | null $height = 40;

    protected bool $isRounded = false;

    protected int | string | null $width = null;

    protected function setUp(): void
    {
        $this->disk(config('tables.default_filesystem_disk'));
    }

    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function height(int | string | null $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function rounded(bool $condition = true): static
    {
        $this->isRounded = $condition;

        return $this;
    }

    public function size(int | string $size): static
    {
        $this->width($size);
        $this->height($size);

        return $this;
    }

    public function width(int | string | null $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getDisk(): Filesystem
    {
        return Storage::disk($this->getDiskName());
    }

    public function getDiskName(): string
    {
        return $this->disk ?? config('tables.default_filesystem_disk');
    }

    public function getHeight(): ?string
    {
        if ($this->height === null) {
            return null;
        }

        if (is_integer($this->height)) {
            return "{$this->height}px";
        }

        return $this->height;
    }

    public function getImagePath(): ?string
    {
        $state = $this->getState();

        if (! $state) {
            return null;
        }

        if (filter_var($state, FILTER_VALIDATE_URL) !== false) {
            return $state;
        }

        /** @var FilesystemAdapter $storage */
        $storage = $this->getDisk();

        /** @var \League\Flysystem\Filesystem $storageDriver */
        $storageDriver = $storage->getDriver();

        if (
            $storageDriver->getAdapter() instanceof AwsS3Adapter &&
            $storage->getVisibility($state) === 'private'
        ) {
            return $storage->temporaryUrl(
                $state,
                now()->addMinutes(5),
            );
        }

        return $storage->url($state);
    }

    public function getWidth(): ?string
    {
        if ($this->width === null) {
            return null;
        }

        if (is_integer($this->width)) {
            return "{$this->width}px";
        }

        return $this->width;
    }

    public function isRounded()
    {
        return $this->isRounded;
    }
}
