<?php

namespace Filament\Actions\Exports;

use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Spatie\Color\Hex;
use Spatie\Color\Rgb;

class CellStyle
{
    public bool $bold = false;

    public bool $italic = false;

    public bool $underline = false;

    public bool $strikethrough = false;

    public int $size;

    public string $family;

    public Hex|Rgb $color;

    public Hex|Rgb $backgroundColor;

    public VerticalAlignment $verticalAlignment;
    
    public Alignment $alignment;

    public static function make(): static
    {
        return app(static::class);
    }

    public function bold(bool $bold = true): static
    {
        $this->bold = $bold;

        return $this;
    }

    public function italic(bool $italic = true): static
    {
        $this->italic = $italic;

        return $this;
    }

    public function underline(bool $underline = true): static
    {
        $this->underline = $underline;

        return $this;
    }

    public function strikethrough(bool $strikethrough = true): static
    {
        $this->strikethrough = $strikethrough;

        return $this;
    }

    public function size(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function family(string $fontFamily): static
    {
        $this->family = ucfirst(strtolower($fontFamily));

        return $this;
    }

    public function color(string $color): static
    {
        if (str($color)->startsWith('#')) {
            $this->color = Hex::fromString($color);
        } elseif (str($color)->startsWith('rgb')) {
            $this->color = Rgb::fromString($color);
        }

        return $this;
    }

    public function backgroundColor(string $backgroundColor): static
    {
        if (str($backgroundColor)->startsWith('#')) {
            $this->backgroundColor = Hex::fromString($backgroundColor);
        } elseif (str($backgroundColor)->startsWith('rgb')) {
            $this->backgroundColor = Rgb::fromString($backgroundColor);
        }

        return $this;
    }

    public function alignment(Alignment|string $alignment): static
    {
        if (is_string($alignment)) {
            $alignment = Alignment::tryFrom($alignment);
        }

        $this->alignment = $alignment;

        return $this;
    }

    public function verticalAlignment(VerticalAlignment|string $verticalAlignment): static
    {
        if (is_string($verticalAlignment)) {
            $verticalAlignment = VerticalAlignment::tryFrom($verticalAlignment);
        }

        $this->verticalAlignment = $verticalAlignment;

        return $this;
    }
}
