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
    protected string $view = 'tables::columns.image-column';

    protected string | Closure | null $disk = null;

    protected int | string | Closure | null $height = 40;

    protected bool | Closure $isCircular = false;

    protected bool | Closure $isSquare = false;

    protected string | Closure $visibility = 'public';

    protected int | string | Closure | null $width = null;

    protected array $extraImgAttributes = [];

    protected bool | Closure $isStacked = false;

    protected string | Closure | null $separator = null;

    protected int | Closure | null $overlap = null;

    protected int | Closure | null $ring = null;
    
    protected int | Closure | null $limit = null;

    protected bool | Closure $shouldShowRemaining = false;

    protected bool | Closure $shouldShowRemainingAfterStack = false;

    protected string | Closure | null $remainingTextSize = null;

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
        return $this->getPath();
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
        return $this->evaluate($this->isCircular);
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
        return $this->evaluate($this->isSquare);
    }

    public function extraImgAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraImgAttributes[] = $attributes;
        } else {
            $this->extraImgAttributes = [$attributes];
        }

        return $this;
    }

    public function getExtraImgAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag();

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

    public function getImagesWithPath(): array
    {
        return collect($this->getImages())
            ->filter(fn ($image) => $this->getPath($image) !== null)
            ->take($this->getLimit())
            ->toArray();
    }

    public function getImages(): array
    {        
        $images = $this->getState();

        if (is_array($images)) {
            return $images;
        }

        if (! ($separator = $this->getSeparator())) {
            return [];
        }

        $images = explode($separator, $images);

        if (count($images) === 1 && blank($images[0])) {
            $images = [];
        }

        return $images;
    }

    public function separator(string | Closure | null $separator = ','): static
    {
        $this->separator = $separator;

        return $this;
    }

    public function getSeparator(): ?string
    {
        return $this->evaluate($this->separator);
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

    public function ring(string | Closure | null $ring): static
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

    public function showRemaining(bool | Closure $condition = true, bool | Closure $showRemainingAfterStack = false, string | Closure | null $remainingTextSize = null): static
    {
        $this->shouldShowRemaining = $condition;
        $this->showRemainingAfterStack($showRemainingAfterStack);
        $this->remainingTextSize($remainingTextSize);

        return $this;
    }

    public function showRemainingAfterStack(bool | Closure $condition = true): static
    {
        $this->shouldShowRemainingAfterStack = $condition;

        return $this;
    }

    public function shouldShowRemaining(): bool
    {
        return (bool) $this->evaluate($this->shouldShowRemaining);
    }

    public function shouldShowRemainingAfterStack(): bool
    {
        return (bool) $this->evaluate($this->shouldShowRemainingAfterStack);
    }

    public function remainingTextSize(string | Closure | null $remainingTextSize): static
    {
        $this->remainingTextSize = $remainingTextSize;

        return $this;
    }

    public function getRemainingTextSize(): ?string
    {
        return $this->evaluate($this->remainingTextSize);
    }

    protected function getPath(string | null $image = null): ?string
    {
        $state = $image ?? $this->getState();
        
        if (! $state) {
            return null;
        }

        if (filter_var($state, FILTER_VALIDATE_URL) !== false) {
            return $state;
        }

        /** @var FilesystemAdapter $storage */
        $storage = $this->getDisk();

        try {
            if (! $storage->exists($state)) {
                return null;
            }
        } catch (UnableToCheckFileExistence $exception) {
            return null;
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
}
