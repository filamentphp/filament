---
title: Color picker
---

## Introduction

The color picker component allows you to pick a color in a range of formats.

By default, the component uses HEX format:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('color')
```

<AutoScreenshot name="forms/fields/color-picker/simple" alt="Color picker" />

Clicking the color swatch opens a color picker panel where users can visually select a color:

<AutoScreenshot name="forms/fields/color-picker/panel" alt="Color picker with panel open" />

## Setting the color format

While HEX format is used by default, you can choose which color format to use:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('hsl_color')
    ->hsl()

ColorPicker::make('rgb_color')
    ->rgb()

ColorPicker::make('rgba_color')
    ->rgba()
```

<AutoScreenshot name="forms/fields/color-picker/formats" alt="Color pickers with different format options" />

## Customizing the loading placeholder

The color picker displays a loading placeholder while its JavaScript is loaded and initialized. You may customize it using `loadingPlaceholder()`, passing a Blade view or an `Htmlable` instance, such as an `HtmlString` containing trusted HTML:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('color')
    ->loadingPlaceholder(fn () => view('forms.color-picker-loading-placeholder'))
```

<UtilityInjection set="formFields">As well as allowing a static value, the `loadingPlaceholder()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

Keep your placeholder HTML decorative and noninteractive. Filament hides it from assistive technologies and supplies an accessible loading status in the surrounding wrapper. If the JavaScript fails to load or initialize, the placeholder remains visible.

To customize the placeholder for all color pickers, use [`ColorPicker::configureUsing()`](overview#global-settings) in a service provider's `boot()` method:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::configureUsing(function (ColorPicker $colorPicker): void {
    $colorPicker->loadingPlaceholder(fn () => view('forms.color-picker-loading-placeholder'));
});
```

You may call `loadingPlaceholder(null)` on an individual color picker to restore the default loading placeholder.

## Color picker validation

You may use Laravel's validation rules to validate the values of the color picker:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('hex_color')
    ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/')

ColorPicker::make('hsl_color')
    ->hsl()
    ->regex('/^hsl\(\s*(\d+)\s*,\s*(\d*(?:\.\d+)?%)\s*,\s*(\d*(?:\.\d+)?%)\)$/')

ColorPicker::make('rgb_color')
    ->rgb()
    ->regex('/^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$/')

ColorPicker::make('rgba_color')
    ->rgba()
    ->regex('/^rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*(\d*(?:\.\d+)?)\)$/')
```
