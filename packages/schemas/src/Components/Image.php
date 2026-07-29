<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasTooltip;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;

class Image extends Component implements HasEmbeddedView
{
    use HasAlignment;
    use HasTooltip;

    protected ?string $publishedViewOverrideCheckPath = 'filament-schemas::components.image';

    protected string | Closure $url;

    protected string | Closure $alt;

    protected int | string | Closure | null $imageHeight = null;

    protected int | string | Closure | null $imageWidth = null;

    final public function __construct(string | Closure $url, string | Closure $alt)
    {
        $this->url($url);
        $this->alt($alt);
    }

    public static function make(string | Closure $url, string | Closure $alt): static
    {
        $static = app(static::class, ['url' => $url, 'alt' => $alt]);
        $static->configure();

        return $static;
    }

    public function url(string | Closure $url): static
    {
        // Security: If this URL is derived from user input, validate it
        // to prevent XSS via `javascript:` protocol URLs rendered
        // in `src` attributes.

        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->evaluate($this->url);
    }

    public function alt(string | Closure $alt): static
    {
        $this->alt = $alt;

        return $this;
    }

    public function getAlt(): string
    {
        return $this->evaluate($this->alt);
    }

    public function imageHeight(int | string | Closure | null $height): static
    {
        $this->imageHeight = $height;

        return $this;
    }

    public function imageSize(int | string | Closure $size): static
    {
        $this->imageWidth($size);
        $this->imageHeight($size);

        return $this;
    }

    public function imageWidth(int | string | Closure | null $width): static
    {
        $this->imageWidth = $width;

        return $this;
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

    public function toEmbeddedHtml(): string
    {
        $alignment = $this->getAlignment();
        $height = $this->getImageHeight() ?? '8rem';
        $width = $this->getImageWidth();
        $tooltip = $this->getTooltip();

        if (! $alignment instanceof Alignment) {
            $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
        }

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-sc-image',
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : $alignment,
            ])
            ->style([
                ('height: ' . e($height)) => $height,
                ('width: ' . e($width)) => $width,
            ]);

        ob_start(); ?>

        <img
            alt="<?= e($this->getAlt()) ?>"
            src="<?= e($this->getUrl()) ?>"
            <?php if (filled($tooltip)) { ?>
                x-tooltip="{ content: <?= Js::from($tooltip) ?>, theme: $store.theme, allowHTML: <?= Js::from($tooltip instanceof Htmlable) ?> }"
            <?php } ?>
            <?= $attributes->toHtml() ?>
        />

        <?php return ob_get_clean();
    }
}
