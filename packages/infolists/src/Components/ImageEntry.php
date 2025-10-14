<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanWrap;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\TextSize;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use League\Flysystem\UnableToCheckFileExistence;
use Throwable;

use function Filament\Support\generate_href_html;

class ImageEntry extends Entry implements HasEmbeddedView
{
    use CanWrap;

    protected string | Closure | null $diskName = null;

    protected int | string | Closure | null $imageHeight = null;

    protected int | string | Closure | null $imageWidth = null;

    protected bool | Closure $isCircular = false;

    protected bool | Closure $isSquare = false;

    protected string | Closure | null $visibility = null;

    protected int | string | Closure | null $width = null;

    /**
     * @var array<mixed> | Closure
     */
    protected array | Closure $extraImgAttributes = [];

    protected string | Closure | null $defaultImageUrl = null;

    protected bool | Closure $isStacked = false;

    protected int | Closure | null $overlap = null;

    protected int | Closure | null $ring = null;

    protected int | Closure | null $limit = null;

    protected bool | Closure $hasLimitedRemainingText = false;

    protected bool | Closure $isLimitedRemainingTextSeparate = false;

    protected TextSize | string | Closure | null $limitedRemainingTextSize = null;

    protected bool | Closure $shouldCheckFileExistence = true;

    public function disk(string | Closure | null $disk): static
    {
        $this->diskName = $disk;

        return $this;
    }

    public function imageHeight(int | string | Closure | null $height): static
    {
        $this->imageHeight = $height;

        return $this;
    }

    /**
     * @deprecated Use `imageHeight()` instead.
     */
    public function height(int | string | Closure | null $height): static
    {
        $this->imageHeight($height);

        return $this;
    }

    public function imageSize(int | string | Closure $size): static
    {
        $this->imageWidth($size);
        $this->imageHeight($size);

        return $this;
    }

    /**
     * @deprecated Use `imageSize()` instead.
     */
    public function size(int | string | Closure $size): static
    {
        $this->imageSize($size);

        return $this;
    }

    public function circular(bool | Closure $condition = true): static
    {
        $this->isCircular = $condition;

        return $this;
    }

    public function square(bool | Closure $condition = true): static
    {
        $this->isSquare = $condition;

        return $this;
    }

    public function visibility(string | Closure | null $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function imageWidth(int | string | Closure | null $width): static
    {
        $this->imageWidth = $width;

        return $this;
    }

    /**
     * @deprecated Use `imageWidth()` instead.
     */
    public function width(int | string | Closure | null $width): static
    {
        $this->imageWidth($width);

        return $this;
    }

    public function getDisk(): Filesystem
    {
        return Storage::disk($this->getDiskName());
    }

    public function getDiskName(): string
    {
        $name = $this->evaluate($this->diskName);

        if (filled($name)) {
            return $name;
        }

        $defaultName = config('filament.default_filesystem_disk');

        if (
            ($defaultName === 'public')
            && ($this->getCustomVisibility() === 'private')
        ) {
            return 'local';
        }

        return $defaultName;
    }

    public function getImageHeight(): ?string
    {
        $height = $this->evaluate($this->imageHeight);

        if ($height === null) {
            return null;
        }

        if (is_int($height)) {
            return "{$height}px";
        }

        return $height;
    }

    /**
     * @deprecated Use `getImageHeight()` instead.
     */
    public function getHeight(): ?string
    {
        return $this->getImageHeight();
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
                    now()->addMinutes(30)->endOfHour(),
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
        $visibility = $this->getCustomVisibility();

        if (filled($visibility)) {
            return $visibility;
        }

        return ($this->getDiskName() === 'public') ? 'public' : 'private';
    }

    public function getCustomVisibility(): ?string
    {
        return $this->evaluate($this->visibility);
    }

    public function getImageWidth(): ?string
    {
        $width = $this->evaluate($this->imageWidth);

        if ($width === null) {
            return null;
        }

        if (is_int($width)) {
            return "{$width}px";
        }

        return $width;
    }

    /**
     * @deprecated Use `getImageWidth()` instead.
     */
    public function getWidth(): ?string
    {
        return $this->getImageWidth();
    }

    public function isCircular(): bool
    {
        return (bool) $this->evaluate($this->isCircular);
    }

    public function isSquare(): bool
    {
        return (bool) $this->evaluate($this->isSquare);
    }

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraImgAttributes(array | Closure $attributes): static
    {
        $this->extraImgAttributes = $attributes;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraImgAttributes(): array
    {
        return $this->evaluate($this->extraImgAttributes);
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

    public function limitedRemainingText(bool | Closure $condition = true, bool | Closure $isSeparate = false, TextSize | string | Closure | null $size = null): static
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

    public function limitedRemainingTextSize(TextSize | string | Closure | null $size): static
    {
        $this->limitedRemainingTextSize = $size;

        return $this;
    }

    public function getLimitedRemainingTextSize(): TextSize | string | null
    {
        $size = $this->evaluate($this->limitedRemainingTextSize);

        if (blank($size)) {
            return null;
        }

        if (is_string($size)) {
            $size = TextSize::tryFrom($size) ?? $size;
        }

        return $size;
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

    public function toEmbeddedHtml(): string
    {
        $state = $this->getState();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-in-image',
            ]);

        $defaultImageUrl = $this->getDefaultImageUrl();

        if (blank($state) && filled($defaultImageUrl)) {
            $state = [null];
        }

        if (blank($state)) {
            $attributes = $attributes
                ->merge([
                    'x-tooltip' => filled($tooltip = $this->getEmptyTooltip())
                        ? '{
                            content: ' . Js::from($tooltip) . ',
                            theme: $store.theme,
                            allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ',

                        }'
                        : null,
                ], escape: false);

            $placeholder = $this->getPlaceholder();

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($placeholder)) { ?>
                    <p class="fi-in-placeholder">
                        <?= e($placeholder) ?>
                    </p>
                <?php } ?>
            </div>

            <?php return $this->wrapEmbeddedHtml(ob_get_clean());
        }

        $state = Arr::wrap($state);
        $stateCount = count($state);

        $limit = $this->getLimit() ?? $stateCount;

        $stateOverLimitCount = ($limit && ($stateCount > $limit))
            ? ($stateCount - $limit)
            : 0;

        if ($stateOverLimitCount) {
            $state = array_slice($state, 0, $limit);
        }

        $alignment = $this->getAlignment();
        $isCircular = $this->isCircular();
        $isSquare = $this->isSquare();
        $isStacked = $this->isStacked();
        $hasLimitedRemainingText = $stateOverLimitCount && $this->hasLimitedRemainingText();
        $limitedRemainingTextSize = $this->getLimitedRemainingTextSize();
        $height = $this->getImageHeight() ?? ($isStacked ? '2.5rem' : '8rem');
        $width = $this->getImageWidth() ?? (($isCircular || $isSquare) ? $height : null);

        $attributes = $attributes
            ->class([
                'fi-circular' => $isCircular,
                'fi-wrapped' => $this->canWrap(),
                'fi-stacked' => $isStacked,
                ($isStacked && is_int($ring = $this->getRing())) ? "fi-in-image-ring fi-in-image-ring-{$ring}" : '',
                ($isStacked && ($overlap = ($this->getOverlap() ?? 2))) ? "fi-in-image-overlap-{$overlap}" : '',
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
            ]);

        $shouldOpenUrlInNewTab = $this->shouldOpenUrlInNewTab();

        $formatState = function (mixed $stateItem) use ($defaultImageUrl, $width, $height, $shouldOpenUrlInNewTab): string {
            $item = '<img ' . $this->getExtraImgAttributeBag()
                ->merge([
                    'src' => filled($stateItem) ? ($this->getImageUrl($stateItem) ?? $defaultImageUrl) : $defaultImageUrl,
                    'x-tooltip' => filled($tooltip = $this->getTooltip($stateItem))
                        ? '{
                                content: ' . Js::from($tooltip) . ',
                                theme: $store.theme,
                                allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ',
                            }'
                        : null,
                ], escape: false)
                ->style([
                    "height: {$height}" => $height,
                    "width: {$width}" => $width,
                ])
                ->toHtml()
                . ' />';

            if (filled($url = $this->getUrl($stateItem))) {
                $item = '<a ' . generate_href_html($url, $shouldOpenUrlInNewTab)->toHtml() . '>' . $item . '</a>';
            }

            return $item;
        };

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <?php foreach ($state as $stateItem) { ?>
                <?= $formatState($stateItem) ?>
            <?php } ?>

            <?php if ($hasLimitedRemainingText) { ?>
                <div <?= (new ComponentAttributeBag)
                ->class([
                    'fi-in-image-limited-remaining-text',
                    (($limitedRemainingTextSize instanceof TextSize) ? "fi-size-{$limitedRemainingTextSize->value}" : $limitedRemainingTextSize) => $limitedRemainingTextSize,
                ])
                ->style([
                    "height: {$height}" => $height,
                    "width: {$width}" => $width,
                ])
                ->toHtml() ?>>
                    +<?= $stateOverLimitCount ?>
                </div>
            <?php } ?>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }

    public function canWrapByDefault(): bool
    {
        return true;
    }
}
