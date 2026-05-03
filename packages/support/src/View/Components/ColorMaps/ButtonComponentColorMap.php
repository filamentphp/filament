<?php

namespace Filament\Support\View\Components\ColorMaps;

use Filament\Support\Colors\Color;
use LogicException;

class ButtonComponentColorMap
{
    /**
     * @var array<int, string>
     */
    protected array $palette;

    protected float $minContrastRatio = Color::WCAG_AA_TEXT;

    /**
     * @var list<array{bg: int, hover: int, alternateHover: int|null}>
     */
    protected array $lightBackgrounds = [];

    /**
     * @var list<array{bg: int, hover: int, alternateHover: int|null}>
     */
    protected array $darkBackgrounds = [];

    /**
     * @param  array<int, string>  $palette
     */
    final public function __construct(array $palette)
    {
        ksort($palette);

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

    /**
     * Add a candidate `(bg, hover, alternateHover?)` triplet for light mode. Each call appends
     * a candidate; the resolver evaluates them in the order added.
     *
     * `$alternateHover` is consulted whenever the main `$hover` isn't a good match — either
     * because it doesn't produce light text alongside a vibrant `$bg`, or because its
     * lightness disagrees with `$bg`'s in the fallback case.
     *
     * The last candidate added serves as the guaranteed fallback for palettes where no
     * candidate produces light text on its `$bg` — pale colors like yellow.
     */
    public function lightBackground(int $bg, int $hover, ?int $alternateHover = null): static
    {
        $this->lightBackgrounds[] = ['bg' => $bg, 'hover' => $hover, 'alternateHover' => $alternateHover];

        return $this;
    }

    /**
     * Add a candidate `(bg, hover, alternateHover?)` triplet for dark mode. Each call appends
     * a candidate; the resolver evaluates them in the order added using the same two-pass
     * algorithm as `lightBackground()`.
     */
    public function darkBackground(int $bg, int $hover, ?int $alternateHover = null): static
    {
        $this->darkBackgrounds[] = ['bg' => $bg, 'hover' => $hover, 'alternateHover' => $alternateHover];

        return $this;
    }

    /**
     * @return array<string, int>
     */
    public function get(): array
    {
        if (empty($this->lightBackgrounds)) {
            throw new LogicException('At least one `lightBackground()` candidate must be configured before calling `get()`.');
        }

        if (empty($this->darkBackgrounds)) {
            throw new LogicException('At least one `darkBackground()` candidate must be configured before calling `get()`.');
        }

        $textForBg = $this->resolveTextForEachBackground();

        $textLightness = $this->probeTextLightness($textForBg);

        [$bg, $hoverBg] = $this->pickBackgroundShades($textLightness, $this->lightBackgrounds);
        [$darkBg, $darkHoverBg] = $this->pickBackgroundShades($textLightness, $this->darkBackgrounds);

        return [
            'bg' => $bg,
            'hover:bg' => $hoverBg,
            'dark:bg' => $darkBg,
            'dark:hover:bg' => $darkHoverBg,
            'text' => $textForBg[$bg] ?? 0,
            'hover:text' => $textForBg[$hoverBg] ?? 0,
            'dark:text' => $textForBg[$darkBg] ?? 0,
            'dark:hover:text' => $textForBg[$darkHoverBg] ?? 0,
        ];
    }

    /**
     * For each background shade in the palette, find the best text shade. Walks dark→light
     * looking for a dark text first (>= 800), then tries shade 50 as a light fallback. Returns
     * 0 (white) when nothing else qualifies.
     *
     * @return array<int, int>
     */
    protected function resolveTextForEachBackground(): array
    {
        $textForBg = [];

        $possibleDarkTextShades = $this->palette;
        unset($possibleDarkTextShades[array_key_first($possibleDarkTextShades)]);

        $isFallbackAccessibleOnDarkerShades = true;

        foreach (array_keys($this->palette) as $bgShade) {
            foreach ($possibleDarkTextShades as $darkTextShade => $darkTextValue) {
                if (
                    ($darkTextShade >= 800) &&
                    (Color::calculateContrastRatio($this->palette[$bgShade], $darkTextValue) >= $this->minContrastRatio)
                ) {
                    $textForBg[$bgShade] = $darkTextShade;

                    continue 2;
                }

                unset($possibleDarkTextShades[$darkTextShade]);
            }

            if (
                $isFallbackAccessibleOnDarkerShades &&
                array_key_exists(50, $this->palette)
            ) {
                if (Color::calculateContrastRatio($this->palette[$bgShade], $this->palette[50]) >= $this->minContrastRatio) {
                    $textForBg[$bgShade] = 50;

                    continue;
                }

                $isFallbackAccessibleOnDarkerShades = false;
            }

            $textForBg[$bgShade] = 0;
        }

        return $textForBg;
    }

    /**
     * @param  array<int, int>  $textForBg
     * @return array<int, bool>
     */
    protected function probeTextLightness(array $textForBg): array
    {
        $shades = [];

        foreach ([...$this->lightBackgrounds, ...$this->darkBackgrounds] as $candidate) {
            $shades[] = $candidate['bg'];
            $shades[] = $candidate['hover'];

            if ($candidate['alternateHover'] !== null) {
                $shades[] = $candidate['alternateHover'];
            }
        }

        $lightness = [];

        foreach (array_unique($shades) as $shade) {
            $textShade = $textForBg[$shade] ?? 0;

            $lightness[$shade] = ($textShade === 0) || Color::isLight($this->palette[$textShade] ?? 'oklch(1 0 0)');
        }

        return $lightness;
    }

    /**
     * Two-pass selection over an ordered list of background candidates. Pass 1 prefers
     * candidates whose `bg` produces light text and tries `hover` then `alternateHover`.
     * Pass 2 takes the last candidate as guaranteed fallback with smart-hover selection
     * (consistency-based pick between `hover` and `alternateHover`).
     *
     * @param  array<int, bool>  $textLightness
     * @param  list<array{bg: int, hover: int, alternateHover: int|null}>  $candidates
     * @return array{int, int}
     */
    protected function pickBackgroundShades(array $textLightness, array $candidates): array
    {
        foreach ($candidates as $candidate) {
            if (! ($textLightness[$candidate['bg']] ?? false)) {
                continue;
            }

            if ($textLightness[$candidate['hover']] ?? false) {
                return [$candidate['bg'], $candidate['hover']];
            }

            if (($candidate['alternateHover'] !== null) && ($textLightness[$candidate['alternateHover']] ?? false)) {
                return [$candidate['bg'], $candidate['alternateHover']];
            }
        }

        $fallback = end($candidates);

        $hover = (($textLightness[$fallback['bg']] ?? false) === ($textLightness[$fallback['hover']] ?? false))
            ? $fallback['hover']
            : ($fallback['alternateHover'] ?? $fallback['hover']);

        return [$fallback['bg'], $hover];
    }
}
