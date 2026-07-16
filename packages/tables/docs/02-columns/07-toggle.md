---
title: Toggle column
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

The toggle column allows you to render a toggle button inside the table, which can be used to update that database record without needing to open a new page or a modal:

```php
use Filament\Tables\Columns\ToggleColumn;

ToggleColumn::make('is_admin')
```

<AutoScreenshot name="tables/columns/toggle/simple" alt="Toggle column" version="5.x" />

## Lifecycle hooks

Hooks may be used to execute code at various points within the toggle's lifecycle:

```php
ToggleColumn::make()
    ->beforeStateUpdated(function ($record, $state) {
        // Runs before the state is saved to the database.
    })
    ->afterStateUpdated(function ($record, $state) {
        // Runs after the state is saved to the database.
    })
```

## Security

### Authorization

The toggle column does not automatically check Laravel Model Policies before saving changes. When a user updates a value via the toggle column, Filament checks whether the column is `disabled()` but does not run any `update` policy gate check. This means that if a user can see a record in the table and the column is not disabled, they can update that column's value regardless of any `update` policy you have defined. If you need to restrict who can edit this column, you should use the `disabled()` method to conditionally prevent editing based on your own authorization logic, for example `disabled(fn ($record) => $record->user_id !== auth()->id())`. Alternatively, consider using a full edit page or modal action where Filament's resource authorization is enforced.
