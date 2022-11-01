---
title: Color picker
---

The color picker component allows you to pick a color in a range of formats.

By default, the component uses HEX format:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('color')
```

![](https://user-images.githubusercontent.com/41773797/163201755-8926ce35-0d72-42b0-bd31-8967ba40f089.png)

Alternatively, you can use a different format:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('hsl_color')->hsl()
ColorPicker::make('rgb_color')->rgb()
ColorPicker::make('rgba_color')->rgba()
```
