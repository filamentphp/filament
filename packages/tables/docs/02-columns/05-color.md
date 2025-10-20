---
title: Color column
---
import Aside from "@components/Aside.astro"
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

The color column allows you to show the color preview from a CSS color definition, typically entered using the [color picker field](../../forms/color-picker), in one of the supported formats (HEX, HSL, RGB, RGBA).

```php
use Filament\Tables\Columns\ColorColumn;

ColorColumn::make('color')
```

<AutoScreenshot name="tables/columns/color/simple" alt="Color column" version="4.x" />

## Allowing the color to be copied to the clipboard

You may make the color copyable, such that clicking on the preview copies the CSS value to the clipboard, and optionally specify a custom confirmation message and duration in milliseconds. This feature only works when SSL is enabled for the app.

```php
use Filament\Tables\Columns\ColorColumn;

ColorColumn::make('color')
    ->copyable()
    ->copyMessage('Copied!')
    ->copyMessageDuration(1500)
```

<AutoScreenshot name="tables/columns/color/copyable" alt="Color column with a button to copy it" version="4.x" />

Optionally, you may pass a boolean value to control if the text should be copyable or not:

```php
use Filament\Tables\Columns\ColorColumn;

ColorColumn::make('color')
    ->copyable(FeatureFlag::active())
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing static values, the `copyable()`, `copyMessage()`, and `copyMessageDuration()` methods also accept functions to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

## Wrapping multiple color blocks

Color blocks can be set to wrap if they can't fit on one line, by setting `wrap()`:

```php
use Filament\Tables\Columns\ColorColumn;

ColorColumn::make('color')
    ->wrap()
```

<Aside variant="tip">
    The "width" for wrapping is affected by the column label, so you may need to use a shorter or hidden label to wrap more tightly.
</Aside>
