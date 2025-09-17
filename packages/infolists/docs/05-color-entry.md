---
title: Color entry
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

The color entry allows you to show the color preview from a CSS color definition, typically entered using the [color picker field](../forms/color-picker), in one of the supported formats (HEX, HSL, RGB, RGBA).

```php
use Filament\Infolists\Components\ColorEntry;

ColorEntry::make('color')
```

<AutoScreenshot name="infolists/entries/color/simple" alt="Color entry" version="4.x" />

## Allowing the color to be copied to the clipboard

You may make the color copyable, such that clicking on the preview copies the CSS value to the clipboard, and optionally specify a custom confirmation message and duration in milliseconds. This feature only works when SSL is enabled for the app.

```php
use Filament\Infolists\Components\ColorEntry;

ColorEntry::make('color')
    ->copyable()
    ->copyMessage('Copied!')
    ->copyMessageDuration(1500)
```

<AutoScreenshot name="infolists/entries/color/copyable" alt="Color entry with a button to copy it" version="4.x" />

Optionally, you may pass a boolean value to control if the color should be copyable or not:

```php
use Filament\Infolists\Components\ColorEntry;

ColorEntry::make('color')
    ->copyable(FeatureFlag::active())
```

<UtilityInjection set="infolistEntries" version="4.x">As well as allowing static values, the `copyable()`, `copyMessage()`, and `copyMessageDuration()` methods also accept functions to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>
