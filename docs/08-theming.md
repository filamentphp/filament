---
title: Theming
---

Filament makes it incredibly simple to customise the look and feel of the panel through "themes".

To create your first theme, run the following command:

```bash
php artisan make:filament-theme name-of-theme
```

This command will create a new file in `resources/css/filament` called `name-of-theme.css`.

All of the colors used by Filament can be customised using [CSS variables](https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_custom_properties). This means you can change a color in a single place and it will be used throughout the entire panel.

Filament uses 6 different colors:

* `primary`
* `success`
* `danger`
* `gray`
* `blue`
* `white`

The default theme stylesheet should look like this:

```css
:root {
    /*
    --f-primary-100: #;
    --f-primary-200: #;
    --f-primary-300: #;
    --f-primary-400: #;
    --f-primary-500: #;
    --f-primary-600: #;
    --f-primary-700: #;
    --f-primary-800: #;
    --f-primary-900: #;
    --f-success-100: #;
    --f-success-200: #;
    --f-success-300: #;
    --f-success-400: #;
    --f-success-500: #;
    --f-success-600: #;
    --f-success-700: #;
    --f-success-800: #;
    --f-success-900: #;
    --f-danger-100: #;
    --f-danger-200: #;
    --f-danger-300: #;
    --f-danger-400: #;
    --f-danger-500: #;
    --f-danger-600: #;
    --f-danger-700: #;
    --f-danger-800: #;
    --f-danger-900: #;
    --f-gray-100: #;
    --f-gray-200: #;
    --f-gray-300: #;
    --f-gray-400: #;
    --f-gray-500: #;
    --f-gray-600: #;
    --f-gray-700: #;
    --f-gray-800: #;
    --f-gray-900: #;
    --f-blue-100: #;
    --f-blue-200: #;
    --f-blue-300: #;
    --f-blue-400: #;
    --f-blue-500: #;
    --f-blue-600: #;
    --f-blue-700: #;
    --f-blue-800: #;
    --f-blue-900: #;
    --f-white: #;
    */
}
```

> Filament uses [Tailwind CSS](https://tailwindcss.com) for styling, therefore each color has 9 different scales.

To customise a color, uncomment the appropriate line in the CSS file and replace the `#` placeholder with any valid CSS color (hex, RGB, HSL, etc):

```css
:root {
    --f-primary-600: #339E8B;
}
```

## Registering a Theme

Once you've created your theme, you should register it using the `Filament::serving` and `Filament::registerStyle` methods inside the `boot` method of a service provider:

```php
use Filament\Filament;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Filament::serving(function () {
            Filament::registerStyle('my-custom-theme', resource_path('css/filament/name-of-theme.css'));
        });
    }
}
```

> Wrapping your style, script and script data related calls in `Filament::serving` ensures that they will only be run when Filament is being used.
