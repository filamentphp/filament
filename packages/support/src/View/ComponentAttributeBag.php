<?php

namespace Filament\Support\View;

use Closure;
use Filament\Support\Enums\GridDirection;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\Contracts\HasColor;
use Illuminate\Support\Arr;
use Illuminate\View\AppendableAttributeValue;
use Illuminate\View\ComponentAttributeBag as BaseComponentAttributeBag;

/**
 * Optimized attribute bag that avoids the Collection overhead in
 * Laravel's `merge()`, `class()` and `style()` implementations.
 *
 * Laravel's `merge()` creates a Collection, partitions it with a
 * closure, maps with another closure, then merges — all to handle
 * class/style concatenation.
 *
 * @phpstan-consistent-constructor
 */
class ComponentAttributeBag extends BaseComponentAttributeBag
{
    /**
     * @param  string  $name
     */
    public static function hasMacro($name): bool
    {
        return parent::hasMacro($name) || BaseComponentAttributeBag::hasMacro($name);
    }

    /**
     * @param  string  $method
     * @param  array<mixed>  $parameters
     */
    public function __call($method, $parameters): mixed
    {
        if (parent::hasMacro($method) || (! BaseComponentAttributeBag::hasMacro($method))) {
            return parent::__call($method, $parameters);
        }

        $macro = (fn () => static::$macros[$method])->bindTo(null, BaseComponentAttributeBag::class)();

        if ($macro instanceof Closure) {
            $macro = $macro->bindTo($this, static::class);
        }

        return $macro(...$parameters);
    }

    /**
     * When updating this method, also update the corresponding macro in `Filament\Support\SupportServiceProvider`.
     *
     * @param  string | array<string> | null  $color
     */
    public function color(string | HasColor $component, string | array | null $color): static
    {
        if (is_array($color)) {
            return $this
                ->class(['fi-color'])
                ->style(FilamentColor::getComponentCustomStyles($component, $color));
        }

        return $this->class(FilamentColor::getComponentClasses($component, $color));
    }

    /**
     * When updating this method, also update the corresponding macro in `Filament\Support\SupportServiceProvider`.
     *
     * @param  array<string, ?int> | int | null  $columns
     */
    public function grid(array | int | null $columns = [], GridDirection $direction = GridDirection::Row): static
    {
        if (! is_array($columns)) {
            $columns = ['lg' => $columns];
        }

        $columns = array_filter($columns);

        $columns['default'] ??= 1;

        return $this
            ->class([
                'fi-grid',
                'fi-grid-direction-col' => $direction === GridDirection::Column,
                ...array_map(
                    fn (string $breakpoint): string => match ($breakpoint) {
                        'default' => ($columns[$breakpoint] > 1) ? 'fi-grid-cols' : '',
                        default => "{$breakpoint}:fi-grid-cols",
                    },
                    array_keys($columns),
                ),
            ])
            ->style(array_map(
                fn (string $breakpoint, int $columns): string => match ($direction) {
                    GridDirection::Row => '--cols-' . str_replace('!', 'n', str_replace('@', 'c', $breakpoint)) . ": repeat({$columns}, minmax(0, 1fr))",
                    GridDirection::Column => '--cols-' . str_replace('!', 'n', str_replace('@', 'c', $breakpoint)) . ": {$columns}",
                },
                array_keys($columns),
                array_values($columns),
            ));
    }

    /**
     * When updating this method, also update the corresponding macro in `Filament\Support\SupportServiceProvider`.
     *
     * @param  array<string, int | string | null> | int | string | null  $span
     * @param  array<string, int | string | null> | int | string | null  $start
     * @param  array<string, ?int> | int | string | null  $order
     */
    public function gridColumn(array | int | string | null $span = [], array | int | string | null $start = [], array | int | string | null $order = [], bool $isHidden = false): static
    {
        if (! is_array($span)) {
            $span = ['lg' => $span];
        }

        if (! is_array($start)) {
            $start = ['lg' => $start];
        }

        if (! is_array($order)) {
            $order = ['lg' => $order];
        }

        $span = array_filter($span);

        $start = array_filter($start);

        $order = array_filter($order);

        return $this
            ->class([
                'fi-grid-col',
                'fi-hidden' => $isHidden || (($span['default'] ?? null) === 'hidden'),
                ...array_map(
                    fn (string $breakpoint): string => match ($breakpoint) {
                        'default' => '',
                        default => "{$breakpoint}:fi-grid-col-span",
                    },
                    array_keys($span),
                ),
                ...array_map(
                    fn (string $breakpoint): string => match ($breakpoint) {
                        'default' => 'fi-grid-col-start',
                        default => "{$breakpoint}:fi-grid-col-start",
                    },
                    array_keys($start),
                ),
                ...array_map(
                    fn (string $breakpoint): string => match ($breakpoint) {
                        'default' => 'fi-grid-col-order',
                        default => "{$breakpoint}:fi-grid-col-order",
                    },
                    array_keys($order),
                ),
            ])
            ->style([
                ...array_map(
                    fn (string $breakpoint, int | string $span): string => '--col-span-' . str_replace('!', 'n', str_replace('@', 'c', $breakpoint)) . ': ' . match ($span) {
                        'full' => '1 / -1',
                        default => "span {$span} / span {$span}",
                    },
                    array_keys($span),
                    array_values($span),
                ),
                ...array_map(
                    fn (string $breakpoint, int $start): string => '--col-start-' . str_replace('!', 'n', str_replace('@', 'c', $breakpoint)) . ': ' . $start,
                    array_keys($start),
                    array_values($start),
                ),
                ...array_map(
                    fn (string $breakpoint, int $order): string => '--col-order-' . str_replace('!', 'n', str_replace('@', 'c', $breakpoint)) . ': ' . $order,
                    array_keys($order),
                    array_values($order),
                ),
            ]);
    }

    /**
     * @param  mixed  $classList
     */
    public function class($classList): static
    {
        $classList = Arr::wrap($classList);
        $classes = Arr::toCssClasses($classList);

        if ($classes === '') {
            return $this;
        }

        $attributes = $this->attributes;
        $existing = $attributes['class'] ?? '';
        $attributes['class'] = $existing !== '' ? ($existing . ' ' . $classes) : $classes;

        return new static($attributes);
    }

    /**
     * @param  mixed  $styleList
     */
    public function style($styleList): static
    {
        $styleList = Arr::wrap($styleList);
        $styles = Arr::toCssStyles($styleList);

        if ($styles === '') {
            return $this;
        }

        $attributes = $this->attributes;
        $existing = $attributes['style'] ?? '';

        if ($existing !== '') {
            $existing = rtrim($existing, '; ') . '; ';
        }

        $attributes['style'] = $existing . $styles;

        return new static($attributes);
    }

    /**
     * @param  array<string, mixed>  $attributeDefaults
     * @param  bool  $escape
     */
    public function merge(array $attributeDefaults = [], $escape = true): static
    {
        if ($escape) {
            foreach ($attributeDefaults as $key => $value) {
                if ($this->shouldEscapeAttributeValue($escape, $value)) {
                    $attributeDefaults[$key] = e($value);
                }
            }
        }

        $hasAppendableAttributeValues = false;

        foreach ($attributeDefaults as $key => $value) {
            if (! ($value instanceof AppendableAttributeValue)) {
                continue;
            }

            $hasAppendableAttributeValues = true;

            $innerValue = $value->value;

            if ($escape && $this->shouldEscapeAttributeValue($escape, $innerValue)) {
                $attributeDefaults[$key] = new AppendableAttributeValue(e($innerValue));
            }
        }

        if ($hasAppendableAttributeValues) {
            return parent::merge($attributeDefaults, escape: false);
        }

        $result = $attributeDefaults;

        foreach ($this->attributes as $key => $value) {
            if ($key === 'class' || $key === 'style') {
                $defaultValue = $result[$key] ?? '';

                if ($key === 'style' && $value !== '') {
                    $value = rtrim($value, '; ') . ';';
                }

                $parts = array_filter([$defaultValue, $value], fn ($part) => ($part !== '') && ($part !== null));

                if ($parts !== []) {
                    $result[$key] = implode(' ', array_unique($parts));
                }
            } else {
                // Existing attributes take precedence over defaults
                $result[$key] = $value;
            }
        }

        return new static($result);
    }
}
