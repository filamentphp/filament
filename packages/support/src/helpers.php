<?php

namespace Filament\Support;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Illuminate\Translation\MessageSelector;
use Illuminate\View\ComponentAttributeBag;
use NumberFormatter;

if (! function_exists('Filament\Support\format_money')) {
    function format_money(float | int $money, string $currency, int $divideBy = 0): string
    {
        $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);

        if ($divideBy) {
            $money = bcdiv((string) $money, (string) $divideBy);
        }

        return $formatter->formatCurrency($money, $currency);
    }
}

if (! function_exists('Filament\Support\format_number')) {
    function format_number(float | int $number): string
    {
        $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::DECIMAL);

        return $formatter->format($number);
    }
}

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

if (! function_exists('Filament\Support\get_color_css_variables')) {
    /**
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}  $color
     * @param  array<int>  $shades
     */
    function get_color_css_variables(string | array | null $color, array $shades): ?string
    {
        if ($color === null) {
            return null;
        }

        $variables = [];

        if (is_string($color)) {
            foreach ($shades as $shade) {
                $variables[] = "--c-{$shade}:var(--{$color}-{$shade})";
            }
        }

        if (is_array($color)) {
            foreach ($shades as $shade) {
                $variables[] = "--c-{$shade}:{$color[$shade]}";
            }
        }

        return implode(';', $variables);
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

if (! function_exists('Filament\Support\is_slot_empty')) {
    function is_slot_empty(?Htmlable $slot): bool
    {
        if ($slot === null) {
            return true;
        }

        return trim(
            str_replace(
                [
                    '<!-- __BLOCK__ -->',
                    '<!-- __ENDBLOCK__ -->',
                ],
                '',
                $slot->toHtml()
            ),
        ) === '';
    }
}
