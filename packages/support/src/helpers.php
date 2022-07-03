<?php

namespace Filament\Support;

use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

if (! function_exists('Filament\Support\get_model_label')) {
    function get_model_label(string $model): string
    {
        return (string) Str::of(class_basename($model))
            ->kebab()
            ->replace('-', ' ');
    }
}

if (! function_exists('Filament\Support\prepare_inherited_attributes')) {
    function prepare_inherited_attributes(ComponentAttributeBag $attributes): ComponentAttributeBag
    {
        $attributes->setAttributes(
            collect($attributes->getAttributes())
                ->mapWithKeys(fn ($value, string $name): array => [Str::camel($name) => $value])
                ->merge($attributes->getAttributes())
                ->toArray(),
        );

        return $attributes;
    }
}
