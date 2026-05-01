<?php

namespace Filament\Support\View\Components\ColorMaps;

use Filament\Support\Colors\Color;

class ComponentColorMap
{
    /**
     * @var array<int, string>
     */
    protected array $palette;

    /**
     * @var array<string, int>
     */
    protected array $resolved = [];

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

    /**
     * Find the lightest (or darkest, if `$shouldStartFromDarkest`) shade in the palette that contrasts
     * against `$surface` by at least `$minRatio`, optionally bounded by `$minShade`/`$maxShade`,
     * and assign it to the named slot. Falls back to `$fallback` if no shade qualifies.
     */
    public function slot(
        string $name,
        string $surface,
        float $minRatio = Color::WCAG_AA_TEXT,
        ?int $maxShade = null,
        ?int $minShade = null,
        bool $shouldStartFromDarkest = false,
        int $fallback = 900,
    ): static {
        $this->resolved[$name] = Color::findShade(
            palette: $this->palette,
            surface: $surface,
            minRatio: $minRatio,
            maxShade: $maxShade,
            minShade: $minShade,
            shouldStartFromDarkest: $shouldStartFromDarkest,
        ) ?? $fallback;

        return $this;
    }

    /**
     * @return array<string, int>
     */
    public function get(): array
    {
        return $this->resolved;
    }
}
