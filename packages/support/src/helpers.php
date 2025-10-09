<?php

namespace Filament\Support;

use BackedEnum;
use Filament\Support\Contracts\ScalableIcon;
use Filament\Support\Enums\IconSize;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\Support\View\Components\Contracts\HasColor;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Translation\MessageSelector;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use Throwable;

if (! function_exists('Filament\Support\format_money')) {
    /**
     * @deprecated Use `Illuminate\Support\Number::currency()` instead.
     */
    function format_money(float | int $money, string $currency, int $divideBy = 0): string
    {
        if ($divideBy) {
            $money /= $divideBy;
        }

        return Number::currency($money, $currency);
    }
}

if (! function_exists('Filament\Support\format_number')) {
    /**
     * @deprecated Use `Illuminate\Support\Number::format()` instead.
     */
    function format_number(float | int $number): string
    {
        return Number::format($number);
    }
}

if (! function_exists('Filament\Support\get_model_label')) {
    /**
     * @param  class-string<Model>  $model
     */
    function get_model_label(string $model): string
    {
        return (string) str($model)
            ->classBasename()
            ->kebab()
            ->replace('-', ' ');
    }
}

if (! function_exists('Filament\Support\locale_has_pluralization')) {
    function locale_has_pluralization(): bool
    {
        return (new MessageSelector)->getPluralIndex(app()->getLocale(), 10) > 0;
    }
}

if (! function_exists('Filament\Support\get_component_color_classes')) {
    /**
     * @param  class-string<HasColor>  $component
     * @return array<string>
     */
    function get_component_color_classes(string | HasColor $component, ?string $color): array
    {
        if (blank($color)) {
            return [];
        }

        return FilamentColor::getComponentClasses($component, $color);
    }
}

if (! function_exists('Filament\Support\prepare_inherited_attributes')) {
    function prepare_inherited_attributes(ComponentAttributeBag $attributes): ComponentAttributeBag
    {
        $originalAttributes = $attributes->getAttributes();

        $attributes->setAttributes(
            collect($originalAttributes)
                ->filter(fn ($value, string $name): bool => ! str($name)->startsWith(['x-', 'data-']))
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

        if (! $slot instanceof ComponentSlot) {
            $slot = new ComponentSlot($slot->toHtml());
        }

        return ! $slot->hasActualContent();
    }
}

if (! function_exists('Filament\Support\is_app_url')) {
    function is_app_url(string $url): bool
    {
        return str($url)->startsWith(request()->root());
    }
}

if (! function_exists('Filament\Support\generate_href_html')) {
    function generate_href_html(?string $url, bool $shouldOpenInNewTab = false, ?bool $shouldOpenInSpaMode = null, bool $hasNestedClickEventHandler = false): Htmlable
    {
        if (blank($url)) {
            return new HtmlString('');
        }

        $html = "href=\"{$url}\"";

        if ($shouldOpenInNewTab) {
            $html .= ' target="_blank"';
        } elseif ($shouldOpenInSpaMode ?? (FilamentView::hasSpaMode($url))) {
            if (FilamentView::hasSpaPrefetching()) {
                $html .= ' wire:navigate.hover';
            } elseif ($hasNestedClickEventHandler) {
                $html .= ' x-on:click="if (! ($event.altKey || $event.ctrlKey || $event.metaKey || $event.shiftKey)) { $event.preventDefault(); Alpine.navigate(' . "'{$url}'" . ') }"';
            } else {
                $html .= ' wire:navigate';
            }
        }

        return new HtmlString($html);
    }
}

if (! function_exists('Filament\Support\generate_icon_html')) {
    /**
     * @param  string | array<string> | null  $alias
     */
    function generate_icon_html(string | BackedEnum | Htmlable | null $icon, string | array | null $alias = null, ?ComponentAttributeBag $attributes = null, ?IconSize $size = null): ?Htmlable
    {
        if (filled($alias)) {
            $icon = FilamentIcon::resolve($alias) ?: $icon;
        }

        if (blank($icon)) {
            return null;
        }

        $size ??= IconSize::Medium;

        $attributes = ($attributes ?? new ComponentAttributeBag)->class([
            'fi-icon',
            "fi-size-{$size->value}",
        ]);

        if ($icon instanceof Htmlable) {
            return new HtmlString(<<<HTML
                <span {$attributes->toHtml()}>
                    {$icon->toHtml()}
                </span>
                HTML);
        }

        if (is_string($icon) && str_contains($icon, '/')) {
            return new HtmlString(<<<HTML
                <img src="{$icon}" {$attributes->toHtml()} />
                HTML);
        }

        if ($icon instanceof ScalableIcon) {
            $icon = $icon->getIconForSize($size);
        } elseif ($icon instanceof BackedEnum) {
            $icon = $icon->value;
        }

        return svg($icon, $attributes->get('class'), array_filter($attributes->except('class')->getAttributes()));
    }
}

if (! function_exists('Filament\Support\generate_loading_indicator_html')) {
    function generate_loading_indicator_html(?ComponentAttributeBag $attributes = null, ?IconSize $size = null): Htmlable
    {
        $size ??= IconSize::Medium;

        $attributes = ($attributes ?? new ComponentAttributeBag)->class([
            'fi-icon fi-loading-indicator',
            "fi-size-{$size->value}",
        ]);

        return new HtmlString(<<<HTML
            <svg
                fill="none"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
                {$attributes->toHtml()}
            >
                <path
                    clip-rule="evenodd"
                    d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                    fill-rule="evenodd"
                    fill="currentColor"
                    opacity="0.2"
                ></path>
                <path
                    d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z"
                    fill="currentColor"
                ></path>
            </svg>
            HTML);
    }
}

if (! function_exists('Filament\Support\generate_search_column_expression')) {
    /**
     * @internal This function is only to be used internally by Filament and is subject to change at any time. Please do not use this function in your own code.
     */
    function generate_search_column_expression(string $column, ?bool $isSearchForcedCaseInsensitive, Connection $databaseConnection): string | Expression
    {
        $driverName = $databaseConnection->getDriverName();

        $column = match ($driverName) {
            'pgsql' => (
                str($column)->contains('->')
                            ? (
                                // Handle `table.field` part with double quotes
                                str($column)
                                    ->before('->')
                                    ->explode('.')
                                    ->map(fn (string $part): string => (string) str($part)->wrap('"'))
                                    ->implode('.')
                            ) . collect(str($column)->after('->')->explode('->')) // Handle JSON path parts
                                ->map(function ($segment, $index) use ($column): string {
                                    // If segment already starts with `>` (from `->>` operator), preserve it
                                    $isExplicitOperatorPrefixed = str($segment)->startsWith('>');
                                    $segment = $isExplicitOperatorPrefixed ? (string) str($segment)->after('>') : $segment;

                                    // Remove single quotes from segment if present to avoid redundant quoting
                                    $isWrappedWithSingleQuotes = str($segment)->startsWith("'") && str($segment)->endsWith("'");
                                    $segment = $isWrappedWithSingleQuotes ? (string) str($segment)->trim("'") : $segment;

                                    if ($isExplicitOperatorPrefixed) {
                                        return "->>'{$segment}'";
                                    }

                                    $totalParts = substr_count($column, '->');

                                    return ($index === ($totalParts - 1))
                                        ? "->>'{$segment}'"
                                        : "->'{$segment}'";
                                })
                                ->implode('')
                            : str($column)
                                ->explode('.')
                                ->map(fn (string $part): string => (string) str($part)->wrap('"'))
                                ->implode('.')
            ) . '::text',
            default => $column,
        };

        $isSearchForcedCaseInsensitive ??= match ($driverName) {
            'pgsql' => true,
            default => str($column)->contains('json_extract('),
        };

        if ($isSearchForcedCaseInsensitive) {
            if (in_array($driverName, ['mysql', 'mariadb'], true) && str($column)->contains('->') && ! str($column)->startsWith('json_extract(')) {
                [$field, $path] = invade($databaseConnection->getQueryGrammar())->wrapJsonFieldAndPath($column); /** @phpstan-ignore-line */
                $column = "json_extract({$field}{$path})";
            }

            $column = "lower({$column})";
        }

        $collation = $databaseConnection->getConfig('search_collation');

        if (filled($collation)) {
            $column = "{$column} collate {$collation}";
        }

        if (
            str($column)->contains('(') || // This checks if the column name probably contains a raw expression like `lower()` or `json_extract()`.
            filled($collation)
        ) {
            return new Expression($column);
        }

        return $column;
    }
}

if (! function_exists('Filament\Support\generate_search_term_expression')) {
    /**
     * @internal This function is only to be used internally by Filament and is subject to change at any time. Please do not use this function in your own code.
     */
    function generate_search_term_expression(string $search, ?bool $isSearchForcedCaseInsensitive, Connection $databaseConnection): string
    {
        $isSearchForcedCaseInsensitive ??= match ($databaseConnection->getDriverName()) {
            'pgsql' => true,
            default => false,
        };

        if (! $isSearchForcedCaseInsensitive) {
            return $search;
        }

        return Str::lower($search);
    }
}

if (! function_exists('Filament\Support\original_request')) {
    function original_request(): Request
    {
        return app('originalRequest');
    }
}

if (! function_exists('Filament\Support\discover_app_classes')) {
    /**
     * @return array<class-string>
     */
    function discover_app_classes(?string $parentClass = null): array
    {
        $classLoader = require 'vendor/autoload.php';

        return collect($classLoader->getClassMap())
            ->filter(function (string $file, string $class) use ($parentClass): bool {
                if (! str($file)->startsWith(base_path('vendor' . DIRECTORY_SEPARATOR . 'composer/../../'))) {
                    return false;
                }

                if (blank($parentClass)) {
                    return true;
                }

                try {
                    return is_subclass_of($class, $parentClass);
                } catch (Throwable) {
                    return false;
                }
            })
            ->keys()
            ->all();
    }
}

if (! function_exists('Filament\Support\get_color_css_variables')) {
    /**
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null  $color
     * @param  array<int>  $shades
     */
    function get_color_css_variables(string | array | null $color, array $shades, ?string $alias = null): ?string
    {
        if ($color === null) {
            return null;
        }

        if ($alias !== null) {
            if (($overridingShades = FilamentColor::getOverridingShades($alias)) !== null) {
                $shades = $overridingShades;
            }

            if ($addedShades = FilamentColor::getAddedShades($alias)) {
                $shades = [...$shades, ...$addedShades];
            }

            if ($removedShades = FilamentColor::getRemovedShades($alias)) {
                $shades = array_diff($shades, $removedShades);
            }
        }

        $variables = [];

        if (is_string($color)) {
            foreach ($shades as $shade) {
                $variables[] = "--color-{$shade}:var(--{$color}-{$shade})";
            }
        }

        if (is_array($color)) {
            foreach ($shades as $shade) {
                $variables[] = "--color-{$shade}:{$color[$shade]}";
            }
        }

        return implode(';', $variables);
    }
}
