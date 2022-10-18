<?php

namespace Filament\Support;

use Illuminate\Support\Str;
use Illuminate\Translation\MessageSelector;
use Illuminate\View\ComponentAttributeBag;

if (! function_exists('Filament\Support\get_model_label')) {
    function get_model_label(string $model): string
    {
        return (string) str(class_basename($model))
            ->kebab()
            ->replace('-', ' ');
    }
}

if (! function_exists('Filament\Support\locale_has_pluralization')) {
    function locale_has_pluralization(): bool
    {
        return (new MessageSelector())->getPluralIndex(app()->getLocale(), 10) > 0;
    }
}

if (! function_exists('Filament\Support\prepare_inherited_attributes')) {
    function prepare_inherited_attributes(ComponentAttributeBag $attributes): ComponentAttributeBag
    {
        $originalAttributes = $attributes->getAttributes();

        $attributes->setAttributes(
            collect($originalAttributes)
                ->filter(fn ($value, string $name): bool => ! str($name)->startsWith('x-'))
                ->mapWithKeys(fn ($value, string $name): array => [Str::camel($name) => $value])
                ->merge($originalAttributes)
                ->all(),
        );

        return $attributes;
    }
}
