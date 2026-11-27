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

## Offering preset colors

You may list colors below the picker, such as your brand's palette, using the `presetColors()` method. Clicking a preset color selects it:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('color')
    ->presetColors([
        '#ef4444' => 'Red',
        '#f59e0b' => 'Amber',
        '#3b82f6' => 'Blue',
        '#111827',
    ])
```

<UtilityInjection set="formFields">As well as allowing a static value, the `presetColors()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/color-picker/preset-colors" alt="Color picker with preset colors" />

Each key is a color and its value is the label that screen readers announce and that is shown when hovering over it. A color without a key is used as its own label.

Preset colors are saved exactly as they are written, so write them in the [format of the color picker](#setting-the-color-format). The preset color that matches the current state is highlighted.

## Picking a color from the screen

You may add a button to pick a color from anywhere on the screen using the `eyeDropper()` method:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('color')
    ->eyeDropper()
```

<UtilityInjection set="formFields">As well as allowing a static value, the `eyeDropper()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

The picked color is converted to the [format of the color picker](#setting-the-color-format). The button uses the browser's [EyeDropper API](https://developer.mozilla.org/en-US/docs/Web/API/EyeDropper_API), and is hidden in browsers that do not support it.

## Allowing the color to be copied

You may add a button to copy the color to the clipboard using the `copyable()` method, as with the [text input](text-input#allowing-the-text-to-be-copied-to-the-clipboard):

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('color')
    ->copyable(copyMessage: 'Copied!', copyMessageDuration: 1500)
```

<UtilityInjection set="formFields">As well as allowing static values, the `copyable()` method also accepts functions to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

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
