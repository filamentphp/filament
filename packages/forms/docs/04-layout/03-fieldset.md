---
title: Fieldset
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

You may want to group fields into a Fieldset. Each fieldset has a label, a border, and a two-column grid by default:

```php
use Filament\Forms\Components\Fieldset;

Fieldset::make('Label')
    ->schema([
        // ...
    ])
```

<AutoScreenshot name="forms/layout/fieldset/simple" alt="Fieldset" version="3.x" />

## Using grid columns within a fieldset

You may use the `columns()` method to customize the [grid](grid) within the fieldset:

```php
use Filament\Forms\Components\Fieldset;

Fieldset::make('Label')
    ->schema([
        // ...
    ])
    ->columns(3)
```
