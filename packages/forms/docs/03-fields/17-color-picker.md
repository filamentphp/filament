---
title: Color picker
---

## Overview

The color picker component allows you to pick a color in a range of formats.

By default, the component uses HEX format:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('color')
```

![](https://user-images.githubusercontent.com/41773797/163201755-8926ce35-0d72-42b0-bd31-8967ba40f089.png)

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
