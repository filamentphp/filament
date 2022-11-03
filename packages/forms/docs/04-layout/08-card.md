---
title: Card
---

The card component may be used to render the form components inside a card:

```php
use Filament\Forms\Components\Card;

Card::make()
    ->schema([
        // ...
    ])
```

## Grid columns

You may use the `columns()` method to easily create a [grid](grid) within the card:

```php
use Filament\Forms\Components\Card;

Card::make()
    ->schema([
        // ...
    ])
    ->columns(2)
```
