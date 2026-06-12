---
title: Colors
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

Filament uses CSS variables to define its color palette. These CSS variables are mapped to Tailwind classes in the preset file that you load when installing Filament. The reason why Filament uses CSS variables is that it allows the framework to pass a color palette from PHP via a `<style>` element that gets rendered as part of the `@filamentStyles` Blade directive.

By default, Filament's Tailwind preset file ships with 6 colors:

- `primary`, which is [Tailwind's `amber` color](https://tailwindcss.com/docs/customizing-colors) by default
- `success`, which is [Tailwind's `green` color](https://tailwindcss.com/docs/customizing-colors) by default
- `warning`, which is [Tailwind's `amber` color](https://tailwindcss.com/docs/customizing-colors) by default
- `danger`, which is [Tailwind's `red` color](https://tailwindcss.com/docs/customizing-colors) by default
- `info`, which is [Tailwind's `blue` color](https://tailwindcss.com/docs/customizing-colors) by default
- `gray`, which is [Tailwind's `zinc` color](https://tailwindcss.com/docs/customizing-colors) by default

You can [learn how to change these colors and register new ones](#customizing-the-default-colors).

## How to pass a color to Filament

A registered "color" in Filament is not just one shade. In fact, it is an entire color palette made of [11 shades](https://tailwindcss.com/docs/customizing-colors): `50`, `100`, `200`, `300`, `400`, `500`, `600`, `700`, `800`, `900`, and `950`. When you use a color in Filament, the framework will decide which shade to use based on the context. For example, it might use the `600` shade for a component's background, `500` when it is hovered, and `400` for its border. If the user has dark mode enabled, it might use `700`, `800`, or `900` instead.

On one hand, this means that you can specify a color in Filament without having to worry about the exact shade to use, or to specify a shade for each part of the component. Filament takes care of selecting a shade that should create an accessible contrast with other elements where possible.

To customize the color that something is in Filament, you can use its name. For example, if you wanted to use the `success` color, you could pass it to a color method of a PHP component like so:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;

Action::make('proceed')
    ->color('success')
    
Toggle::make('is_active')
    ->onColor('success')
```

If you would like to use a color in a [Blade component](../components), you can pass it as an attribute:

```blade
<x-filament::badge color="success">
    Active
</x-filament::badge>
```

## Customizing the default colors

From a service provider's `boot()` method, or middleware, you can call the `FilamentColor::register()` method, which you can use to customize which colors Filament uses for UI elements.

There are 6 default colors that are used throughout Filament that you are able to customize:

```php
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

FilamentColor::register([
    'danger' => Color::Red,
    'gray' => Color::Zinc,
    'info' => Color::Blue,
    'primary' => Color::Amber,
    'success' => Color::Green,
    'warning' => Color::Amber,
]);
```

The `Color` class contains every [Tailwind CSS color](https://tailwindcss.com/docs/customizing-colors#color-palette-reference) to choose from.

<AutoScreenshot name="panels/configuration/colors" alt="Panel with custom primary color" version="4.x" />

You can also pass in a function to `register()` which will only get called when the app is getting rendered. This is useful if you are calling `register()` from a service provider, and want to access objects like the currently authenticated user, which are initialized later in middleware.

### Registering extra colors

You may register a new color to use in any Filament component by passing it to the `FilamentColor::register()` method, with its name as the key in the array:

```php
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

FilamentColor::register([
    'secondary' => Color::Indigo,
]);
```

You will now be able to use `secondary` as a color in any Filament component.

## Using a non-Tailwind color

You can use custom colors that are not included in the [Tailwind CSS color](https://tailwindcss.com/docs/customizing-colors#color-palette-reference) palette by passing an array of color shades from `50` to `950` in OKLCH format:

```php
use Filament\Support\Facades\FilamentColor;

FilamentColor::register([
    'danger' => [
        50 => 'oklch(0.969 0.015 12.422)',
        100 => 'oklch(0.941 0.03 12.58)',
        200 => 'oklch(0.892 0.058 10.001)',
        300 => 'oklch(0.81 0.117 11.638)',
        400 => 'oklch(0.712 0.194 13.428)',
        500 => 'oklch(0.645 0.246 16.439)',
        600 => 'oklch(0.586 0.253 17.585)',
        700 => 'oklch(0.514 0.222 16.935)',
        800 => 'oklch(0.455 0.188 13.697)',
        900 => 'oklch(0.41 0.159 10.272)',
        950 => 'oklch(0.271 0.105 12.094)',
    ],
]);
```

### Generating a custom color palette

If you want us to attempt to generate a palette for you based on a singular hex or RGB value, you can pass that in:

```php
use Filament\Support\Facades\FilamentColor;

FilamentColor::register([
    'danger' => '#ff0000',
]);

FilamentColor::register([
    'danger' => 'rgb(255, 0, 0)',
]);
```

## How Filament selects accessible shades

When you assign a color to a Filament component (for example `->color('primary')`), Filament receives the entire 11-shade palette and decides at runtime which shade to use for the background, text, hover state, dark-mode variants, and so on. The selection is driven by [WCAG 2.1 contrast ratios](https://www.w3.org/WAI/WCAG21/Understanding/contrast-minimum.html): for each slot, Filament walks the palette and picks the lightest (or darkest, depending on context) shade that meets the minimum contrast against the surface the component sits on.

This design means:

- You only need to register one palette per color name. Per-component shade selection is automatic.
- The same color can render differently across components — a `success` button uses one bg/text combination, a `success` badge another — because each component applies contrast rules suited to its visual role.
- If you swap a palette out (for example, switching `primary` from amber to a darker hue), every component that uses it re-derives its shades to stay accessible.

The contrast thresholds Filament uses come from WCAG 2.1:

- **Normal text (`Color::WCAG_AA_TEXT`, 4.5:1)** — applied to text-bearing components such as buttons, badges, links, dropdown items, and text columns. From [success criterion 1.4.3 Contrast (Minimum)](https://www.w3.org/WAI/WCAG21/Understanding/contrast-minimum.html).
- **User interface components and graphical objects (`Color::WCAG_AA_NON_TEXT`, 3:1)** — applied to icon-only components such as icon buttons, toggles, icon columns, and icon entries. From [success criterion 1.4.11 Non-text Contrast](https://www.w3.org/WAI/WCAG21/Understanding/non-text-contrast.html).

The `Filament\Support\Colors\Color` class exposes these as constants — `WCAG_AA_TEXT`, `WCAG_AA_LARGE_TEXT`, `WCAG_AA_NON_TEXT`, `WCAG_AAA_TEXT`, `WCAG_AAA_LARGE_TEXT` — so you can reference them by name when customizing.

### Why some buttons render dark text instead of white

For solid buttons, Filament builds a "best text shade per background shade" lookup up-front, then picks the actual button background by checking which candidate background shades end up paired with light text.

For vibrant colors like red, blue, or indigo, shade `600` is dark enough that white text passes the 4.5:1 contrast threshold. The resolver picks `bg: 600`, `hover:bg: 500`, with white text — the path most colors take.

For pale colors like yellow, amber, or lime, even shade `600` is bright enough that *dark* text passes contrast better than white. The resolver detects this and falls back to a paler background — `bg: 400` — paired with dark text. The result is a yellow button with dark text on a light-yellow background, instead of an unreadable white-on-yellow combination.

This behavior is intentional. Forcing every color into the same `bg: 600, text: white` pattern would produce inaccessible combinations for warm or pale palettes. The two-path design keeps `success` and `danger` looking like solid coloured buttons while `warning` (typically amber) reads correctly with its lighter background.

## Customizing shade selection

If you need to override how a particular component picks its shades — for example, to enforce WCAG AAA contrast across the app, or to bias buttons toward darker shades — you can extend the relevant view component and rebind it through Laravel's container.

Filament exposes three color-map classes for this purpose, all under the `Filament\Support\View\Components\ColorMaps` namespace. Each follows the same fluent shape — `make($palette)` to start, chained configuration setters, then `get()` to return an `array<string, int>` mapping slot names (such as `bg`, `text`, `dark:hover:bg`) to shade numbers.

The three classes are:

- `ComponentColorMap` — for components that pick one shade per slot, like badges, links, text columns, icons, dropdowns, and toggles.
- `ButtonComponentColorMap` — for solid buttons. Picks a background shade and pairs it with a matching text shade.
- `IconButtonComponentColorMap` — for icon-only buttons. Picks a single icon shade and derives a hover variant from it.

### Components you can override

Every Filament component that picks shades from a palette implements `getColorMap()`. To customize one, extend the class, override `getColorMap()`, and bind your subclass through Laravel's container.

| Component class | Used by | Documentation |
| --- | --- | --- |
| `Filament\Support\View\Components\BadgeComponent` | Badges | [Badge](../components/badge) |
| `Filament\Support\View\Components\ButtonComponent` | Buttons (solid and outlined) | [Button](../components/button) |
| `Filament\Support\View\Components\IconButtonComponent` | Icon-only buttons | [Icon button](../components/icon-button) |
| `Filament\Support\View\Components\LinkComponent` | Links | [Link](../components/link) |
| `Filament\Support\View\Components\ToggleComponent` | Form toggle | [Toggle](../forms/toggle) |
| `Filament\Support\View\Components\DropdownComponent\HeaderComponent` | Dropdown headers | [Dropdown](../components/dropdown) |
| `Filament\Support\View\Components\DropdownComponent\ItemComponent` | Dropdown items | [Dropdown](../components/dropdown) |
| `Filament\Schemas\View\Components\TextComponent` | Schema text primes | [Prime components](../schemas/primes) |
| `Filament\Infolists\View\Components\TextEntryComponent\ItemComponent` | Infolist text entries | [Text entry](../infolists/text-entry) |
| `Filament\Infolists\View\Components\IconEntryComponent\IconComponent` | Infolist icon entries | [Icon entry](../infolists/icon-entry) |
| `Filament\Tables\View\Components\Columns\TextColumnComponent\ItemComponent` | Table text columns | [Text column](../tables/columns/text) |
| `Filament\Tables\View\Components\Columns\IconColumnComponent\IconComponent` | Table icon columns | [Icon column](../tables/columns/icon) |
| `Filament\Tables\View\Components\Columns\Summarizers\CountComponent\IconComponent` | Table count summarizers | [Summaries](../tables/summaries) |
| `Filament\Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent\DescriptionComponent` | Stats overview widget descriptions | [Stats overview](../widgets/stats-overview) |

<Aside variant="tip">
The fastest way to write a custom `getColorMap()` is to copy the original implementation from the class you're overriding and tweak the configuration values. The source files live alongside the documented classes in `packages/<package>/src/View/Components/`. Each one is a few lines of fluent calls — the easiest way to learn the surface is to read the default and change it.
</Aside>

### `ComponentColorMap`

Builds a slot map one entry at a time. Call `slot()` once per output slot, then `get()`.

```php
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\ColorMaps\ComponentColorMap;

$gray = FilamentColor::getColor('gray');

ComponentColorMap::make($color)
    ->slot('text', surface: $gray[50], minRatio: Color::WCAG_AA_TEXT, fallback: 900)
    ->slot('dark:text', surface: $gray[700], maxShade: 500, shouldStartFromDarkest: true, fallback: 200)
    ->get();
```

`slot()` parameters:

- `$name` — Output map key (`text`, `bg`, `hover:text`, `dark:hover:bg`, etc.).
- `$surface` — The color the chosen shade must contrast against (typically `$gray[50]`, `$gray[700]`, or `'oklch(1 0 0)'`).
- `$minRatio` — Minimum WCAG contrast ratio. Defaults to `Color::WCAG_AA_TEXT` (`4.5`); use `WCAG_AA_NON_TEXT` (`3.0`) for icons or `WCAG_AAA_TEXT` (`7.0`) for AAA.
- `$maxShade` — Optional upper bound on the shade number considered.
- `$minShade` — Optional lower bound.
- `$shouldStartFromDarkest` — Walk darkest→lightest instead of the default lightest→darkest. Use for dark-mode lookups.
- `$fallback` — Shade returned when no candidate qualifies. Typically `900` for light mode, `200` for dark mode.

### `ButtonComponentColorMap`

Returns all eight slots a solid button needs (`bg`, `hover:bg`, `dark:bg`, `dark:hover:bg`, `text`, `hover:text`, `dark:text`, `dark:hover:text`) from one `get()` call. You configure which background shades to use; the matching text shade for each is found automatically. At least one `lightBackground()` and one `darkBackground()` are required.

```php
use Filament\Support\Colors\Color;
use Filament\Support\View\Components\ColorMaps\ButtonComponentColorMap;

ButtonComponentColorMap::make($color)
    ->minContrastRatio(Color::WCAG_AA_TEXT)
    ->lightBackground(bg: 600, hover: 500)
    ->lightBackground(bg: 400, hover: 300, alternateHover: 500)
    ->darkBackground(bg: 600, hover: 500, alternateHover: 700)
    ->get();
```

`minContrastRatio()` sets the minimum WCAG ratio between bg and text. Defaults to `Color::WCAG_AA_TEXT` (`4.5`); set to `WCAG_AAA_TEXT` (`7.0`) for AAA.

`lightBackground()` and `darkBackground()` share the same shape `(bg, hover, alternateHover?)` and the same selection algorithm — they differ only in which mode they configure. Each call appends a candidate to the list for that mode, evaluated in order using two passes:

1. **Preferred** — walk the list and stop at the first candidate whose `bg` produces light text. For that candidate, use `hover` if it also produces light text; otherwise use `alternateHover` if *it* produces light text; otherwise skip.
2. **Fallback** — if nothing qualified in pass 1, take the *last* candidate. Hover is `hover` when its text-lightness is consistent with `bg`'s, otherwise `alternateHover`. The consistency check avoids a text-color flicker on hover.

`alternateHover` is treated identically on any candidate — first, last, or middle. It's only consulted when the main `hover` isn't appropriate.

To express a color-aware cascade — "use 800 if the palette can carry it, otherwise 700, otherwise 600, falling back to a paler bg with dark text for yellows" — chain candidates and end with a pale-friendly fallback:

```php
ButtonComponentColorMap::make($color)
    ->lightBackground(bg: 800, hover: 700)
    ->lightBackground(bg: 700, hover: 600)
    ->lightBackground(bg: 600, hover: 500)
    ->lightBackground(bg: 400, hover: 300, alternateHover: 500) // pale fallback
    ->darkBackground(bg: 600, hover: 500, alternateHover: 700)
    ->get();
```

<Aside variant="warning">
The last candidate doubles as the fallback when no candidate produces light text on its `bg`. If you configure only vibrant-friendly candidates and your palette is pale, that last candidate is used anyway — likely producing a dark-text-on-dark-bg button. Always end with a pale-friendly candidate (typically `bg` around `400`) to handle yellows, ambers, and limes.
</Aside>

### `IconButtonComponentColorMap`

Returns the four icon slots (`text`, `hover:text`, `dark:text`, `dark:hover:text`) for icon-only buttons. Hover variants are derived from the resting shade by a fixed `100`-shade offset that intensifies the icon. At least one `lightSurface()` and one `darkSurface()` are required.

```php
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\ColorMaps\IconButtonComponentColorMap;

$gray = FilamentColor::getColor('gray');

IconButtonComponentColorMap::make($color)
    ->minContrastRatio(Color::WCAG_AA_NON_TEXT)
    ->lightSurface($gray[50])
    ->darkSurface($gray[700])
    ->darkMaxShade(500)
    ->get();
```

- `minContrastRatio()` — Minimum contrast ratio between icon and surface. Defaults to `Color::WCAG_AA_NON_TEXT` (`3.0`).
- `lightSurface()` / `darkSurface()` — The body surface color the icon must contrast against in each mode. Typically `$gray[50]` and `$gray[700]`.
- `darkMaxShade()` — Upper bound on the shade considered for dark-mode icon color. Defaults to `500`; lower for lighter icons.

### Worked example: AAA contrast for buttons

Here is a complete subclass that enforces WCAG AAA contrast on solid buttons and biases the background choice toward darker shades:

```php
namespace App\View\Components;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\ButtonComponent as BaseButtonComponent;
use Filament\Support\View\Components\ColorMaps\ButtonComponentColorMap;
use Filament\Support\View\Components\ColorMaps\ComponentColorMap;

class ButtonComponent extends BaseButtonComponent
{
    public function getColorMap(array $color): array
    {
        $gray = FilamentColor::getColor('gray');

        if ($this->isOutlined) {
            return ComponentColorMap::make($color)
                ->slot('text', surface: $gray[50], minRatio: Color::WCAG_AAA_TEXT, fallback: 900)
                ->slot('dark:text', surface: $gray[700], minRatio: Color::WCAG_AAA_TEXT, maxShade: 500, shouldStartFromDarkest: true, fallback: 200)
                ->get();
        }

        return ButtonComponentColorMap::make($color)
            ->minContrastRatio(Color::WCAG_AAA_TEXT)
            ->lightBackground(bg: 700, hover: 600)
            ->lightBackground(bg: 400, hover: 300, alternateHover: 500)
            ->darkBackground(bg: 600, hover: 500, alternateHover: 700)
            ->get();
    }
}
```

Bind your subclass in a service provider's `register()` method:

```php
use App\View\Components\ButtonComponent;
use Filament\Support\View\Components\ButtonComponent as BaseButtonComponent;

public function register(): void
{
    $this->app->bind(BaseButtonComponent::class, ButtonComponent::class);
}
```

The same pattern works for any of the components listed above — extend, override `getColorMap()`, and bind.

<Aside variant="tip">
The defaults work for the overwhelming majority of palettes. Only override these classes if you have specific accessibility requirements (such as AAA compliance) or a design system that mandates different shade preferences.
</Aside>
