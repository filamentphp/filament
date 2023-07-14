---
title: Color column
---

The color column allows you to show the color preview from a CSS color definition, typically entered using the color picker field, in one of the supported formats (HEX, HSL, RGB, RGBA).

```php
use Filament\Tables\Columns\ColorColumn;

ColorColumn::make('color')
```

## Allowing the color to be copied to the clipboard

You may make the color copyable, such that clicking on the preview copies the CSS value to the clipboard, and optionally specify a custom confirmation message and duration in milliseconds. This feature only works when SSL is enabled for the app.

```php
use Filament\Tables\Columns\ColorColumn;

ColorColumn::make('color')
    ->copyable()
    ->copyMessage('Color code copied')
    ->copyMessageDuration(1500)
```

You can change or customize the text that gets copied.

For instance, you may have a color in hex format and you want to copy it in RGB:

```php
use Filament\Tables\Columns\ColorColumn;

ColorColumn::make('hex_color')
    ->copyable()
    ->copyableState(fn (Model $record): string => join(',', sscanf($record->hex_color, "#%02x%02x%02x")))
```
