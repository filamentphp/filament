<?php

namespace Filament\Tables\Columns;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\ComponentAttributeBag;
use Throwable;

class ImageColumn extends Column
{
    protected string $view = 'tables::columns.image-column';

    protected string | Closure | null $disk = null;

    protected int | string | Closure | null $height = 40;

    protected bool | Closure $isRounded = false;

    protected string | Closure $visibility = 'public';

    protected int | string | Closure | null $width = null;

    protected array | Closure $extraImgAttributes = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->disk(config('tables.default_filesystem_disk'));
    }

    public function disk(string | Closure | null $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function height(int | string | Closure | null $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function rounded(bool | Closure $condition = true): static
    {
        $this->isRounded = $condition;

        return $this;
    }

    public function size(int | string | Closure $size): static
    {
        $this->width($size);
        $this->height($size);

        return $this;
    }

    public function visibility(string | Closure $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function width(int | string | Closure | null $width): static
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
        return $this->evaluate($this->disk) ?? config('tables.default_filesystem_disk');
    }

    public function getHeight(): ?string
    {
        $height = $this->evaluate($this->height);

        if ($height === null) {
            return null;
        }

        if (is_int($height)) {
            return "{$height}px";
        }

        return $height;
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

        if (! $storage->exists($state)) {
            return null;
        }

        if ($this->getVisibility() === 'private' || $storage->getVisibility($state) === 'private') {
            try {
                return $storage->temporaryUrl(
                    $state,
                    now()->addMinutes(5),
                );
            } catch (Throwable $exception) {
                // This driver does not support creating temporary URLs.
            }
        }

        return $storage->url($state);
    }

    public function getVisibility(): string
    {
        return $this->evaluate($this->visibility);
    }

    public function getWidth(): ?string
    {
        $width = $this->evaluate($this->width);

        if ($width === null) {
            return null;
        }

        if (is_int($width)) {
            return "{$width}px";
        }

        return $width;
    }

    public function isRounded(): bool
    {
        return $this->evaluate($this->isRounded);
    }

    public function extraImgAttributes(array | Closure $attributes): static
    {
        $this->extraImgAttributes = $attributes;

        return $this;
    }

    public function getExtraImgAttributes(): array
    {
        return $this->evaluate($this->extraImgAttributes);
    }

    public function getExtraImgAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraImgAttributes());
    }
}
