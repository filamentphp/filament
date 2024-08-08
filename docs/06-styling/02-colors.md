---
title: Colors
---

## Overview

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

On one hand, this means that you can specify a color in Filament without having to worry about the exact shade to use, or to specify a shade for each part of the component. Filament takes care of selecting a shade that should create an accessible contrast with other elements where possible. On the other hand, it means that you can't use a specific shade of a color in Filament without [overriding the shade](#overriding-a-component-shade) manually for all instances of that component.

To customize the color that something is in Filament, you can use its name. For example, if you wanted to use the `success` color, you could pass it to a color method of a PHP component like so:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;

Action::make('proceed')
    ->color('success')
    
Toggle::make('is_active')
    ->onColor('success')
```

If you would like to use a color in a [Blade component](../ui/overview), you can pass it as an attribute:

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

However, if you want to use a custom color via a Tailwind class, such as in a custom Blade view or passing it in the `class` HTML attribute of a component, you will have to also register it in your Tailwind config file via the `extend` syntax, so that the class is available in your Tailwind CSS build:

```js
import colors from 'tailwindcss/colors'
import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        // ...
    ],
    theme: {
        extend: {
            colors: {
                secondary: colors.indigo,
            },
        },
    },
}
```

## Using a non-Tailwind color

You can use custom colors that are not included in the [Tailwind CSS color](https://tailwindcss.com/docs/customizing-colors#color-palette-reference) palette by passing an array of color shades from `50` to `950` in RGB format:

```php
use Filament\Support\Facades\FilamentColor;

FilamentColor::register([
    'danger' => [
        50 => '254, 242, 242',
        100 => '254, 226, 226',
        200 => '254, 202, 202',
        300 => '252, 165, 165',
        400 => '248, 113, 113',
        500 => '239, 68, 68',
        600 => '220, 38, 38',
        700 => '185, 28, 28',
        800 => '153, 27, 27',
        900 => '127, 29, 29',
        950 => '69, 10, 10',
    ],
]);
```

### Generating a custom color from a hex code

You can use the `Color::hex()` method to generate a custom color palette from a hex code:

```php
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

FilamentColor::register([
    'danger' => Color::hex('#ff0000'),
]);
```

### Generating a custom color from an RGB value

You can use the `Color::rgb()` method to generate a custom color palette from an RGB value:

```php
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

FilamentColor::register([
    'danger' => Color::rgb('rgb(255, 0, 0)'),
]);
```

## Overriding a component shade

The shade in which a component uses for a particular property such as its background color, text color or border color is defined within the element's CSS definition. If you inspect a particular element, you may see that its CSS contains something such as:

```css
.fi-btn.fi-color-custom {
    background-color: rgba(var(--c-600), var(--tw-bg-opacity));
}

.fi-btn.fi-color-custom:hover {
    background-color: rgba(var(--c-500), var(--tw-bg-opacity));
}
```

This is compiled from the Tailwind CSS `@apply` statements that Filament uses in its base CSS file:

```css
.fi-btn.fi-color-custom {
    @apply bg-custom-600 hover:bg-custom-500;
}
```

The `custom` part of those Tailwind classes translates to those `--c-500` and `--c-600` CSS variables that are used in the CSS definition. Since the color for an element in Filament is defined within PHP, Filament needs a way to pass the color to the CSS when the element is rendered. This is done through an inline `style` attribute that is added to the element, mapping the unknown "custom" color to a real color that has been defined using `FilamentColor::register()`:

```html
<button
    style="
        --c-500: var(--primary-500);
        --c-600: var(--primary-600);
    "
    class="fi-btn fi-color-custom"
>
    <!-- ... -->
</button>
```

Imagine you wanted to swap the background shades for this element, so that the `600` shade is used when the element is hovered, and the `500` shade is used when it is not. You can do this using [CSS hooks](css-hooks) in the same way as any other CSS property. In this case, you want to target the `.fi-btn.fi-color-custom` selector in your CSS file:

```css
.fi-btn.fi-color-custom {
    @apply bg-custom-500 hover:bg-custom-600;
}
```

This will override the default shades that Filament uses for the `custom` color in this particular component.

However, what about if you wanted to access a shade that is not defined within the `style` attribute of that element? If you attempted to do so, the CSS property would not be defined, and the element would not have the correct color. For example, maybe you want to use the `800` shade for the background color when the element is not hovered, and the `700` shade when it is. You can do this by telling Filament to inject those new shades into all instances of that element. Each element that uses custom colors has an "alias", which is an identifier that you can target in the `boot()` method of a service provider, or in a middleware, using the `FilamentColor::addShades()` method:

```php
use Filament\Support\Facades\FilamentColor;

FilamentColor::addShades('button', [700, 800]);
```

This will add the `700` and `800` shades to the `style` attribute of all elements that use the `button` alias. You can now use those shades in your CSS:

```css
.fi-btn.fi-color-custom {
    @apply bg-custom-800 hover:bg-custom-700;
}
```

### Available color aliases

#### Forms color aliases

- `forms::components.toggle.off` - Toggle field in the "off" state
- `forms::components.toggle.on` - Toggle field in the "on" state

#### Infolists color aliases

- `infolists::components.icon-entry.item` - Icon entry icon
- `infolists::components.text-entry.item.icon` - Text entry icon
- `infolists::components.text-entry.item.label` - Text entry text

#### Notifications color aliases

- `notifications::notification` - Notification container
- `notifications::notification.icon` - Notification icon

#### Schema color aliases

- `schema::components.decorations.icon-decoration.icon` - Icon decoration icon
- `schema::components.decorations.text-decoration` - Text decoration

#### Tables color aliases

- `tables::columns.icon-column.item` - Icon column icon
- `tables::columns.summaries.icon-count.icon` - Icon in a count column summary
- `tables::columns.text-column.item.icon` - Text column icon
- `tables::columns.text-column.item.label` - Text column text
- `tables::columns.toggle-column.on` - Toggle column in the "on" state
- `tables::columns.toggle-column.off` - Toggle column in the "off" state

#### UI components color aliases

- `badge` - Badge container
- `badge.delete-button` - Badge delete button
- `badge.icon` - Badge icon
- `button` - Button
- `dropdown.header.icon` - Dropdown header icon
- `dropdown.header.label` - Dropdown header text
- `dropdown.list.item` - Dropdown list item container
- `dropdown.list.item.icon` - Dropdown list item icon
- `dropdown.list.item.label` - Dropdown list item text
- `icon-button` - Icon button
- `input-wrapper.icon` - Input wrapper icon
- `link.icon` - Link icon
- `link.label` - Link text
- `modal.icon` - Modal icon
- `section.header.icon` - Section header icon

#### Widgets color aliases

- `widgets::chart-widget.background` - Chart widget background
- `widgets::chart-widget.border` - Chart widget border
- `widgets::stats-overview-widget.stat.chart` - Stats overview widget stat chart
- `widgets::stats-overview-widget.stat.description` - Stats overview widget stat description text
- `widgets::stats-overview-widget.stat.description.icon` - Stats overview widget stat description icon
