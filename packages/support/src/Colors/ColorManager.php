<?php

namespace Filament\Support\Colors;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;
use LogicException;

class ColorManager
{
    use EvaluatesClosures;

    const DEFAULT_COLORS = [
        'danger' => Color::Red,
        'gray' => Color::Zinc,
        'info' => Color::Blue,
        'primary' => Color::Amber,
        'success' => Color::Green,
        'warning' => Color::Amber,
    ];

    /**
     * @var array<array<string, array<int, string> | string> | Closure>
     */
    protected array $colors = [];

    /**
     * @var array<string, array<int, string>>
     */
    protected array $cachedColors;

    /**
     * @var array<class-string<HasColor>, array<string, array<string>>>
     */
    protected array $componentClasses = [];

    /**
     * @var array<class-string<HasColor>, array<string, array<string>>>
     */
    protected array $componentCustomStyles = [];

    /**
     * @var array<string,array<int>>
     */
    protected array $overridingShades = [];

    /**
     * @var array<string,array<int>>
     */
    protected array $addedShades = [];

    /**
     * @var array<string,array<int>>
     */
    protected array $removedShades = [];

    /**
     * @param  array<string, array<int, string> | string> | Closure  $colors
     */
    public function register(array | Closure $colors): static
    {
        $this->colors[] = $colors;

        return $this;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function getColors(): array
    {
        if (isset($this->cachedColors)) {
            return $this->cachedColors;
        }

        array_unshift($this->colors, static::DEFAULT_COLORS);

        foreach ($this->colors as $colors) {
            $colors = $this->evaluate($colors);

            foreach ($colors as $name => $color) {
                if (is_string($color)) {
                    $color = Color::generatePalette($color);
                } else {
                    $color = array_map(
                        fn (string | int $color): string | int => is_string($color) ? Color::convertToOklch($color) : $color,
                        $color,
                    );
                }

                $this->cachedColors[$name] = $color;
            }
        }

        return $this->cachedColors;
    }

    /**
     * @return ?array<int, string>
     */
    public function getColor(string $color): ?array
    {
        return $this->getColors()[$color] ?? null;
    }

    /**
     * @param  class-string<HasColor> | HasColor  $component
     * @return array<string>
     */
    public function getComponentClasses(string | HasColor $component, ?string $color): array
    {
        if (blank($color)) {
            return [];
        }

        $component = is_string($component) ? app($component) : $component;

        if (($color === 'gray') && ($component instanceof HasDefaultGrayColor)) {
            return [];
        }

        $componentKey = serialize($component);

        if ($this->componentClasses[$componentKey][$color] ?? []) {
            return $this->componentClasses[$componentKey][$color];
        }

        $classes = ['fi-color', "fi-color-{$color}"];

        $resolvedColor = $this->getColor($color);

        if (! $resolvedColor) {
            return $this->componentClasses[$componentKey][$color] = $classes;
        }

        $map = $component->getColorMap($resolvedColor);

        return $this->componentClasses[$componentKey][$color] = [
            ...$classes,
            ...array_map(
                fn (string $shade, string $key): string => match ($key) {
                    'bg' => "fi-bg-color-{$shade}",
                    'dark:bg' => "dark:fi-bg-color-{$shade}",
                    'dark:hover:bg' => "dark:hover:fi-bg-color-{$shade}",
                    'dark:hover:text' => "dark:hover:fi-text-color-{$shade}",
                    'dark:text' => "dark:fi-text-color-{$shade}",
                    'hover:bg' => "hover:fi-bg-color-{$shade}",
                    'hover:text' => "hover:fi-text-color-{$shade}",
                    'text' => "fi-text-color-{$shade}",
                    default => throw new LogicException("Invalid color mapping key [{$key}]."),
                },
                array_values($map),
                array_keys($map),
            ),
        ];
    }

    /**
     * @param  class-string<HasColor> | HasColor  $component
     * @param  array<string>  $color
     * @return array<string>
     */
    public function getComponentCustomStyles(string | HasColor $component, array $color): array
    {
        $component = is_string($component) ? app($component) : $component;
        $componentKey = serialize($component);
        $colorKey = serialize($color);

        if ($this->componentCustomStyles[$componentKey][$colorKey] ?? []) {
            return $this->componentCustomStyles[$componentKey][$colorKey];
        }

        $map = $component->getColorMap($color);

        return $this->componentCustomStyles[$componentKey][$colorKey] = [
            ...array_map(
                fn (string $color, string $shade): string => "--color-{$shade}: {$color}",
                array_values($color),
                array_keys($color),
            ),
            ...array_map(
                fn (string $shade, string $key): string => '--' . str_replace(':', '-', $key) . ': ' . ($shade ? "var(--color-{$shade})" : 'oklch(1 0 0)'),
                array_values($map),
                array_keys($map),
            ),
        ];
    }

    /**
     * @param  array<int>  $shades
     */
    public function overrideShades(string $alias, array $shades): void
    {
        $this->overridingShades[$alias] = $shades;
    }

    /**
     * @return array<int> | null
     */
    public function getOverridingShades(string $alias): ?array
    {
        return $this->overridingShades[$alias] ?? null;
    }

    /**
     * @param  array<int>  $shades
     */
    public function addShades(string $alias, array $shades): void
    {
        $this->addedShades[$alias] = $shades;
    }

    /**
     * @return array<int> | null
     */
    public function getAddedShades(string $alias): ?array
    {
        return $this->addedShades[$alias] ?? null;
    }

    /**
     * @param  array<int>  $shades
     */
    public function removeShades(string $alias, array $shades): void
    {
        $this->removedShades[$alias] = $shades;
    }

    /**
     * @return array<int> | null
     */
    public function getRemovedShades(string $alias): ?array
    {
        return $this->removedShades[$alias] ?? null;
    }
}
