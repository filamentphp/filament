---
title: Fieldset
---

You may want to group fields into a Fieldset. Each fieldset has a label, a border, and a two-column grid by default:

```php
use Filament\Forms\Components\Fieldset;

Fieldset::make('Label')
    ->schema([
        // ...
    ])
```

## Grid columns

You may use the `columns()` method to customize the [grid](grid) within the fieldset:

```php
use Filament\Forms\Components\Fieldset;

Fieldset::make('Label')
    ->schema([
        // ...
    ])
    ->columns(3)
```
