---
title: Fieldset
---

## Overview

You may want to group entries into a Fieldset. Each fieldset has a label, a border, and a two-column grid by default:

```php
use Filament\Infolists\Components\Fieldset;

Fieldset::make('Label')
    ->schema([
        // ...
    ])
```

## Using grid columns within a fieldset

You may use the `columns()` method to customize the [grid](grid) within the fieldset:

```php
use Filament\Infolists\Components\Fieldset;

Fieldset::make('Label')
    ->schema([
        // ...
    ])
    ->columns(3)
```
