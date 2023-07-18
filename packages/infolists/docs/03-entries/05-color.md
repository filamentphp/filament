---
title: Color entry
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The color entry allows you to show the color preview from a CSS color definition, typically entered using the color picker field, in one of the supported formats (HEX, HSL, RGB, RGBA).

```php
use Filament\Infolists\Components\ColorEntry;

ColorEntry::make('color')
```

<AutoScreenshot name="infolists/entries/color/simple" alt="Color entry" version="3.x" />

## Allowing the color to be copied to the clipboard

You may make the color copyable, such that clicking on the preview copies the CSS value to the clipboard, and optionally specify a custom confirmation message and duration in milliseconds. This feature only works when SSL is enabled for the app.

```php
use Filament\Infolists\Components\ColorEntry;

ColorEntry::make('color')
    ->copyable()
    ->copyMessage('Copied!')
    ->copyMessageDuration(1500)
```

<AutoScreenshot name="infolists/entries/color/copyable" alt="Color entry with a button to copy it" version="3.x" />
