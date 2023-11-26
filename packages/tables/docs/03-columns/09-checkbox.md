---
title: Checkbox column
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The checkbox column allows you to render a checkbox inside the table, which can be used to update that database record without needing to open a new page or a modal:

```php
use Filament\Tables\Columns\CheckboxColumn;

CheckboxColumn::make('is_admin')
```

<AutoScreenshot name="tables/columns/checkbox/simple" alt="Checkbox column" version="3.x" />

## Lifecycle hooks

Hooks may be used to execute code at various points within the checkbox's lifecycle:

```php
CheckboxColumn::make()
    ->beforeStateUpdated(function ($record, $state) {
        // Runs before the state is saved to the database.
    })
    ->afterStateUpdated(function ($record, $state) {
        // Runs after the state is saved to the database.
    })
```
