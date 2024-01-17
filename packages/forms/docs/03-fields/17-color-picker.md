---
title: Color picker
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The color picker component allows you to pick a color in a range of formats.

By default, the component uses HEX format:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('color')
```

<AutoScreenshot name="forms/fields/color-picker/simple" alt="Color picker" version="3.x" />

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

## Live updates

When using the color picker with `live()` you may run into issues with the color picker updating the value too often and making unnecessary server requests. To avoid this, you can use the `updateOnDragend()` method instead to only update the value when the user has finished dragging the color picker:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('color')
    ->updateOnDragend()
```
