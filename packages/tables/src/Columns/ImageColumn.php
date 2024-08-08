<?php

namespace Filament\Tables\Columns;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\ComponentAttributeBag;
use League\Flysystem\UnableToCheckFileExistence;
use Throwable;

class ImageColumn extends Column
{
    use Concerns\CanWrap;

    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.image-column';

    protected string | Closure | null $disk = null;

    protected int | string | Closure | null $height = null;

    protected bool | Closure $isCircular = false;

    protected bool | Closure $isSquare = false;

    protected string | Closure $visibility = 'public';

    protected int | string | Closure | null $width = null;

    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraImgAttributes = [];

    protected string | Closure | null $defaultImageUrl = null;

    protected bool | Closure $isStacked = false;

    protected int | Closure | null $overlap = null;

    protected int | Closure | null $ring = null;

    protected int | Closure | null $limit = null;

    protected bool | Closure $hasLimitedRemainingText = false;

    protected bool | Closure $isLimitedRemainingTextSeparate = false;

    protected string | Closure | null $limitedRemainingTextSize = null;

    protected bool | Closure $shouldCheckFileExistence = true;

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

    public function circular(bool | Closure $condition = true): static
    {
        $this->isCircular = $condition;

        return $this;
    }

    /**
     * @deprecated Use `circular()` instead.
     */
    public function rounded(bool | Closure $condition = true): static
    {
        return $this->circular($condition);
    }

    public function square(bool | Closure $condition = true): static
    {
        $this->isSquare = $condition;

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
        return $this->evaluate($this->disk) ?? config('filament.default_filesystem_disk');
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

    public function defaultImageUrl(string | Closure | null $url): static
    {
        $this->defaultImageUrl = $url;

        return $this;
    }

    public function getImageUrl(?string $state = null): ?string
    {
        if ((filter_var($state, FILTER_VALIDATE_URL) !== false) || str($state)->startsWith('data:')) {
            return $state;
        }

        /** @var FilesystemAdapter $storage */
        $storage = $this->getDisk();

        if ($this->shouldCheckFileExistence()) {
            try {
                if (! $storage->exists($state)) {
                    return null;
                }
            } catch (UnableToCheckFileExistence $exception) {
                return null;
            }
        }

        if ($this->getVisibility() === 'private') {
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

    public function getDefaultImageUrl(): ?string
    {
        return $this->evaluate($this->defaultImageUrl);
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

    public function isCircular(): bool
    {
        return (bool) $this->evaluate($this->isCircular);
    }

    /**
     * @deprecated Use `isCircular()` instead.
     */
    public function isRounded(): bool
    {
        return $this->isCircular();
    }

    public function isSquare(): bool
    {
        return (bool) $this->evaluate($this->isSquare);
    }

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraImgAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraImgAttributes[] = $attributes;
        } else {
            $this->extraImgAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraImgAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraImgAttributes as $extraImgAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraImgAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraImgAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraImgAttributes());
    }

    public function stacked(bool | Closure $condition = true): static
    {
        $this->isStacked = $condition;

        return $this;
    }

    public function isStacked(): bool
    {
        return (bool) $this->evaluate($this->isStacked);
    }

    public function overlap(int | Closure | null $overlap): static
    {
        $this->overlap = $overlap;

        return $this;
    }

    public function getOverlap(): ?int
    {
        return $this->evaluate($this->overlap);
    }

    public function ring(int | Closure | null $ring): static
    {
        $this->ring = $ring;

        return $this;
    }

    public function getRing(): ?int
    {
        return $this->evaluate($this->ring);
    }

    public function limit(int | Closure | null $limit = 3): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->evaluate($this->limit);
    }

    public function limitedRemainingText(bool | Closure $condition = true, bool | Closure $isSeparate = false, string | Closure | null $size = null): static
    {
        $this->hasLimitedRemainingText = $condition;
        $this->limitedRemainingTextSeparate($isSeparate);
        $this->limitedRemainingTextSize($size);

        return $this;
    }

    public function limitedRemainingTextSeparate(bool | Closure $condition = true): static
    {
        $this->isLimitedRemainingTextSeparate = $condition;

        return $this;
    }

    public function hasLimitedRemainingText(): bool
    {
        return (bool) $this->evaluate($this->hasLimitedRemainingText);
    }

    public function isLimitedRemainingTextSeparate(): bool
    {
        return (bool) $this->evaluate($this->isLimitedRemainingTextSeparate);
    }

    public function limitedRemainingTextSize(string | Closure | null $size): static
    {
        $this->limitedRemainingTextSize = $size;

        return $this;
    }

    public function getLimitedRemainingTextSize(): ?string
    {
        return $this->evaluate($this->limitedRemainingTextSize);
    }

    public function checkFileExistence(bool | Closure $condition = true): static
    {
        $this->shouldCheckFileExistence = $condition;

        return $this;
    }

    public function shouldCheckFileExistence(): bool
    {
        return (bool) $this->evaluate($this->shouldCheckFileExistence);
    }
}
