# Filament Spatie Google Fonts Plugin

## Installation

Install the plugin with Composer:

```bash
composer require filament/spatie-laravel-google-fonts-plugin:"^3.0-stable" -W
```

Please [follow Spatie's documentation about how to set up their package](https://github.com/spatie/laravel-google-fonts) first.

## Using the font provider

In your [Panel Builder configuration](https://filamentphp.com/docs/panels/configuration), you can use the `font()` method, passing a `provider`:

```php
use Filament\FontProviders\SpatieGoogleFontProvider;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->font('Inter', provider: SpatieGoogleFontProvider::class);
}
```

Now, if Spatie's package is set up to fetch and cache the `Inter` font correctly, Filament will use that source in the panel, without using any CDNs. Internally, the `SpatieGoogleFontProvider` class uses the `@googlefonts` directive from Spatie's package.
