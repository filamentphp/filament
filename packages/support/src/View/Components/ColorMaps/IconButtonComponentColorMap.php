<?php

namespace Filament\Support\View\Components\ColorMaps;

use Filament\Support\Colors\Color;
use LogicException;

class IconButtonComponentColorMap
{
    /**
     * @var array<int, string>
     */
    protected array $palette;

    protected float $minContrastRatio = Color::WCAG_AA_NON_TEXT;

    protected ?string $lightSurface = null;

    protected ?string $darkSurface = null;

    /**
     * The maximum shade considered when searching for dark-mode text. Higher shades are skipped
     * because they would be too dark to read against the dark-mode body surface.
     */
    protected int $darkMaxShade = 500;

    /**
     * @param  array<int, string>  $palette
     */
    final public function __construct(array $palette)
    {
        $this->palette = $palette;
    }

    /**
     * @param  array<int, string>  $palette
     */
    public static function make(array $palette): static
    {
        return new static($palette);
    }

    public function minContrastRatio(float $ratio): static
    {
        $this->minContrastRatio = $ratio;

        return $this;
    }

    public function lightSurface(string $color): static
    {
        $this->lightSurface = $color;

        return $this;
    }

    public function darkSurface(string $color): static
    {
        $this->darkSurface = $color;

        return $this;
    }

    public function darkMaxShade(int $shade): static
    {
        $this->darkMaxShade = $shade;

        return $this;
    }

    /**
     * @return array<string, int>
     */
    public function get(): array
    {
        if ($this->lightSurface === null) {
            throw new LogicException('A `lightSurface()` must be configured before calling `get()`.');
        }

        if ($this->darkSurface === null) {
            throw new LogicException('A `darkSurface()` must be configured before calling `get()`.');
        }

        $lightShade = Color::findShade(
            palette: $this->palette,
            surface: $this->lightSurface,
            minRatio: $this->minContrastRatio,
        );

        if ($lightShade === null) {
            $text = 900;
            $hoverText = 800;
        } elseif ($lightShade > 500) {
            // Shades above 500 are likely to be quite dark, so instead of lightening the icon
            // when it is hovered, we darken it.
            $text = $lightShade;
            $hoverText = $lightShade + 100;
        } else {
            $text = $lightShade + 100;
            $hoverText = $lightShade;
        }

        $darkShade = Color::findShade(
            palette: $this->palette,
            surface: $this->darkSurface,
            minRatio: $this->minContrastRatio,
            maxShade: $this->darkMaxShade,
            shouldStartFromDarkest: true,
        );

        if ($darkShade === null) {
            $darkText = 200;
            $darkHoverText = 100;
        } else {
            $darkText = $darkShade;
            $darkHoverText = $darkShade - 100;
        }

        return [
            'text' => $text,
            'hover:text' => $hoverText,
            'dark:text' => $darkText,
            'dark:hover:text' => $darkHoverText,
        ];
    }
}
