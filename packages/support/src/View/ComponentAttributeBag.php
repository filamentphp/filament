<?php

namespace Filament\Support\View;

use Closure;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\Contracts\HasColor;
use Illuminate\Support\Arr;
use Illuminate\View\AppendableAttributeValue;
use Illuminate\View\ComponentAttributeBag as BaseComponentAttributeBag;

/**
 * Optimized attribute bag that avoids the Collection overhead in
 * Laravel's merge() and class() implementations.
 *
 * Laravel's merge() creates a Collection, partitions it with a
 * closure, maps with another closure, then merges — all to handle
 * class/style concatenation. This replacement uses plain array
 * operations for the same semantics.
 */
class ComponentAttributeBag extends BaseComponentAttributeBag
{
    /**
     * Inherit macros registered on the parent class (`gridColumn()`, etc.)
     * since PHP's Macroable trait uses late static binding which isolates
     * each class's macro storage.
     */
    public static function hasMacro($name)
    {
        return parent::hasMacro($name) || BaseComponentAttributeBag::hasMacro($name);
    }

    public function __call($method, $parameters)
    {
        if (! parent::hasMacro($method) && BaseComponentAttributeBag::hasMacro($method)) {
            $macro = (fn () => static::$macros[$method])->bindTo(null, BaseComponentAttributeBag::class)();

            if ($macro instanceof Closure) {
                $macro = $macro->bindTo($this, static::class);
            }

            return $macro(...$parameters);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Apply color classes or custom styles. Defined as a real method
     * instead of inheriting the parent's macro to avoid macro lookup
     * overhead on every call.
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
        $attributes['class'] = $existing !== '' ? $existing . ' ' . $classes : $classes;

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
     */
    public function merge(array $attributeDefaults = [], $escape = true): static
    {
        // Escape default values
        if ($escape) {
            foreach ($attributeDefaults as $key => $value) {
                if ($this->shouldEscapeAttributeValue($escape, $value)) {
                    $attributeDefaults[$key] = e($value);
                }
            }
        }

        // Check if any defaults use AppendableAttributeValue (rare in Filament)
        $hasAppendable = false;

        foreach ($attributeDefaults as $value) {
            if ($value instanceof AppendableAttributeValue) {
                $hasAppendable = true;

                break;
            }
        }

        if ($hasAppendable) {
            return parent::merge($attributeDefaults, escape: false);
        }

        // Fast path: plain array merge with class/style concatenation
        $result = $attributeDefaults;

        foreach ($this->attributes as $key => $value) {
            if ($key === 'class' || $key === 'style') {
                $defaultValue = $result[$key] ?? '';

                if ($key === 'style' && $value !== '') {
                    $value = rtrim($value, '; ') . ';';
                }

                $parts = array_filter([$defaultValue, $value], fn ($part) => $part !== '' && $part !== null);

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
