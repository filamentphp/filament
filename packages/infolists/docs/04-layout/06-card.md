---
title: Card
---

## Overview

The card component may be used to render the infolists components inside a card:

```php
use Filament\Infolists\Components\Card;

Card::make()
    ->schema([
        // ...
    ])
```

## Using grid columns within a card

You may use the `columns()` method to easily create a [grid](grid) within the card:

```php
use Filament\Infolists\Components\Card;

Card::make()
    ->schema([
        // ...
    ])
    ->columns(2)
```
