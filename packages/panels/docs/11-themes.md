---
title: Themes
---

## Changing the colors

In the [configuration](configuration), you can easily change the colors that are used. Filament ships with 6 predefined colors that are used everywhere within the framework. They are customizable as follows:

```php
use Filament\Panel;
use Filament\Support\Colors\Color;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->colors([
            'danger' => Color::Rose,
            'gray' => Color::Gray,
            'info' => Color::Blue,
            'primary' => Color::Indigo,
            'success' => Color::Emerald,
            'warning' => Color::Orange,
        ]);
}
```

The `Filament\Support\Colors\Color` class contains color options for all [Tailwind CSS color palettes](https://tailwindcss.com/docs/customizing-colors).

Alternatively, you may pass your own palette in as an array of RGB values:

```php
$panel
    ->colors([
        'primary' => [
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
            950 => '30, 27, 75',
        ],
    ])
```

### Generating a color palette

If you want us to attempt to generate a palette for you based on a singular hex or RGB value, you can pass that in:

```php
$panel
    ->colors([
        'primary' => '#6366f1',
    ])

$panel
    ->colors([
        'primary' => 'rgb(99, 102, 241)',
    ])
```

## Changing the font

By default, we use the [Inter](https://fonts.google.com/specimen/Inter) font. You can change this using the `font()` method in the [configuration](configuration) file:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->font('Poppins');
}
```

All [Google Fonts](https://fonts.google.com) are available to use.

### Changing the font provider

[Bunny Fonts CDN](https://fonts.bunny.net) is used to serve the fonts. It is GDPR-compliant. If you'd like to use [Google Fonts CDN](https://fonts.google.com) instead, you can do so using the `provider` argument of the `font()` method:

```php
use Filament\FontProviders\GoogleFontProvider;

$panel->font('Inter', provider: GoogleFontProvider::class)
```

Or if you'd like to serve the fonts from a local stylesheet, you can use the `LocalFontProvider`:

```php
use Filament\FontProviders\LocalFontProvider;

$panel->font(
    'Inter',
    url: asset('css/fonts.css'),
    provider: LocalFontProvider::class,
)
```

## Creating a custom theme

Filament allows you to change the CSS used to render the UI by compiling a custom stylesheet to replace the default one. This custom stylesheet is called a "theme".

Themes use [Tailwind CSS](https://tailwindcss.com), the Tailwind Forms plugin, the Tailwind Typography plugin, and [Autoprefixer](https://github.com/postcss/autoprefixer).

To create a custom theme for a panel, you can use the `php artisan make:filament-theme` command:

```bash
php artisan make:filament-theme
```

If you have multiple panels, you can specify the panel you want to create a theme for:

```bash
php artisan make:filament-theme admin
```

The command will create a CSS file and Tailwind Configuration file in the `/resources/css/filament` directory. You can then customize the theme by editing these files. It will also give you instructions on how to compile the theme and register it in Filament. **Please follow the instructions in the command to complete the setup process:**

```
⇂ First, add a new item to the `input` array of `vite.config.js`: `resources/css/filament/admin/theme.css`  
⇂ Next, register the theme in the admin panel provider using `->viteTheme('resources/css/filament/admin/theme.css')`  
⇂ Finally, run `npm run build` to compile the theme
```

Please reference the command to see the exact file names that you need to register, they may not be `admim/theme.css`.

## Non-sticky topbar

By default, the topbar sticks to the top of the page. You may make the topbar scroll out of view with the following CSS:

```css
.fi-topbar {
    position: relative;
}
```

## Changing the logo

By default, Filament will use your app's name as a logo.

You may create a `resources/views/vendor/filament-panels/components/logo.blade.php` file to provide a custom logo:

```blade
<img src="{{ asset('/images/logo.svg') }}" alt="Logo" class="h-10">
```

## Disabling dark mode

To disable dark mode switching, you can use the [configuration](configuration) file:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->darkMode(false);
}
```

## Adding a favicon

To add a favicon, you can use the [configuration](configuration) file, passing the public URL of the favicon:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->favicon(asset('images/favicon.png'));
}
```
