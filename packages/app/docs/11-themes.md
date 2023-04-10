---
title: Themes
---

## Changing the colors

In the [configuration](configuration), you can easily change the colors that are used. Filament ships with 6 predefined colors that are used everywhere within the framework. They are customizable as follows:

```php
use Filament\Context;
use Filament\Support\Color;

public function context(Context $context): Context
{
    return $context
        // ...
        ->primaryColor(Color::Indigo)
        ->secondaryColor(Color::Sky)
        ->grayColor(Color::Gray)
        ->dangerColor(Color::Rose)
        ->successColor(Color::Emerald)
        ->warningColor(Color::Orange);
}
```

The `Filament\Support\Color` contains color options for all [Tailwind CSS color palettes](https://tailwindcss.com/docs/customizing-colors).

Alternatively, you may pass your own palette in as an array of RGB values:

```php
$context->primaryColor([
    50 => '238, 242, 255',
    100 => '224, 231, 255',
    200 => '199, 210, 254',
    300 => '165, 180, 252',
    400 => '129, 140, 248',
    500 => '99, 102, 241',
    600 => '79, 70, 229',
    700 => '67, 56, 202',
    800 => '55, 48, 163',
    900 => '49, 46, 129',
])
```

### Generating a color palette

If you want us to attempt to generate a palette for you based on a singular hex or RGB value, you can use pass those in:

```php
use Filament\Support\Color;

$context->primaryColor('#6366f1')

$context->primaryColor('rgb(99, 102, 241)')
```

## Changing the font

By default, we use the [Be Vietnam Pro](https://fonts.google.com/specimen/Be+Vietnam+Pro) font. You can change this using the `font()` method in the [configuration](configuration) file:

```php
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->font('Inter');
}
```

All [Google Fonts](https://fonts.google.com) are available to use.

### Changing the font provider

[Bunny Fonts CDN](https://fonts.bunny.net) is used to serve the fonts. It is GDPR-compliant. If you'd like to use [Google Fonts CDN](https://fonts.google.com) instead, you can do so using the `provider` argument of the `font()` method:

```php
use Filament\FontProviders\GoogleFontProvider;

$context->font('Inter', provider: GoogleFontProvider::class)
```

Or if you'd like to serve the fonts from a local stylesheet, you can use the `LocalFontProvider`:

```php
use Filament\FontProviders\LocalFontProvider;

$context->font(
    'Inter',
    url: asset('css/fonts.css'),
    provider: LocalFontProvider::class,
)
```

## Creating a custom theme

Filament allows you to change the fonts and color scheme used in the UI, by compiling a custom stylesheet to replace the default one. This custom stylesheet is called a "theme".

Themes use [Tailwind CSS](https://tailwindcss.com), the Tailwind Forms plugin, and the Tailwind Typography plugin, [Autoprefixer](https://github.com/postcss/autoprefixer), and [Tippy.js](https://atomiks.github.io/tippyjs). You may install these through NPM:

```bash
npm install tailwindcss @tailwindcss/forms @tailwindcss/typography postcss autoprefixer tippy.js --save-dev
```

To finish installing Tailwind, you must create a new `tailwind.config.js` file in the root of your project. The easiest way to do this is by running `npx tailwindcss init`.

In `tailwind.config.js`, require Filament's "preset", which gives you a working Tailwind configuration that you can override however you wish:

```js
const preset = require('./vendor/filament/filament/tailwind.config.preset')

module.exports = {
    presets: [preset],
}
```

If you use Vite to compile assets, in your `vite.config.js` file, register the `filament.css` theme file:

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // ...
                'resources/css/filament.css',
            ],
            // ...
        }),
    ],
})
```

And add Tailwind to the `postcss.config.js` file:

```js
module.exports = {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
}
```

Or if you're using Laravel Mix instead of Vite, in your `webpack.mix.js` file, register Tailwind CSS as a PostCSS plugin:

```js
const mix = require('laravel-mix')

mix.postCss('resources/css/filament.css', 'public/css', [
    require('tailwindcss'),
])
```

In `/resources/css/filament.css`, import Filament's base theme CSS:

```css
@import '../../vendor/filament/filament/resources/css/index.css';
```

Now, you may register the theme file in Filament's [configuration](configuration):

```php
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->viteTheme('resources/css/filament.css') // Using Vite
        ->theme(mix('css/filament.css')); // Using Laravel Mix
}
```

## Non-sticky topbar

By default, the topbar sticks to the top of the page. You may make the topbar scroll out of view with the following CSS:

```css
.filament-main-topbar {
    position: relative;
}
```

## Changing the logo

By default, Filament will use your app's name as a logo.

You may create a `resources/views/vendor/filament/components/logo.blade.php` file to provide a custom logo:

```blade
<img src="{{ asset('/images/logo.svg') }}" alt="Logo" class="h-10">
```

## Disabling dark mode

To disable dark mode switching, you can use the [configuration](configuration) file:

```php
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->darkMode(false);
}
```

## Adding a favicon

To add a favicon, you can use the [configuration](configuration) file, passing the public URL of the favicon:

```php
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->favicon(asset('images/favicon.png'));
}
```
