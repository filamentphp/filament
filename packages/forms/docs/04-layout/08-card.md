---
title: Card
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The card component may be used to render the form components inside a card:

```php
use Filament\Forms\Components\Card;

Card::make()
    ->schema([
        // ...
    ])
```

<AutoScreenshot name="forms/layout/card/simple" alt="Card" version="3.x" />

## Using grid columns within a card

You may use the `columns()` method to easily create a [grid](grid) within the card:

```php
use Filament\Forms\Components\Card;

Card::make()
    ->schema([
        // ...
    ])
    ->columns(2)
```
