---
title: Toggle column
---

## Introduction

The toggle column allows you to render a toggle button inside the table, which can be used to update that database record without needing to open a new page or a modal:

```php
use Filament\Tables\Columns\ToggleColumn;

ToggleColumn::make('is_admin')
```

<AutoScreenshot name="tables/columns/toggle/simple" alt="Toggle column" />

## Lifecycle hooks

Hooks may be used to execute code at various points within the toggle's lifecycle:

```php
use Filament\Tables\Columns\ToggleColumn;

ToggleColumn::make('is_admin')
    ->beforeStateUpdated(function ($record, $state) {
        // Runs before the state is saved to the database.
    })
    ->afterStateUpdated(function ($record, $state) {
        // Runs after the state is saved to the database.
    })
```

## Rendering only the updated cells

By default, the whole table re-renders after a toggle column is updated. You may [re-render only the updated cells](overview#rendering-only-the-updated-cells-of-editable-columns) using `partiallyRenderAfterStateUpdated()` or `partiallyRenderColumnsAfterStateUpdated()`.

## Security

### Authorization

By default, toggle columns do not check model policies. Without an authorization check, users can update the column for records accessible through the table query, even if the record's `update()` policy denies access to its edit page.

Use the `authorize()` method to check the record's `update()` policy before saving. When the check fails, the column is shown as disabled and changes are rejected on the server. You may pass a different ability name, or a function that returns a boolean or an `Illuminate\Auth\Access\Response`:

```php
use Filament\Tables\Columns\ToggleColumn;

ToggleColumn::make('is_admin')
    ->authorize('updateAdminStatus')
```

You may also pass a function that returns a boolean or an `Illuminate\Auth\Access\Response`, rather than an ability name:

```php
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Support\Facades\Gate;

ToggleColumn::make('is_admin')
    ->authorize(fn ($record): bool => Gate::allows('updateAdminStatus', $record))
```

<UtilityInjection set="tableColumns">The function passed to `authorize()` can inject various utilities as parameters.</UtilityInjection>

In a panel with [strict authorization](../../panel-configuration#strict-authorization-mode) enabled, toggle columns in resource and relation manager tables check the resource's `update()` policy by default, unless you call `authorize()` yourself.

To opt out of this default policy check, use `authorize(true)`. This does not override `disabled()`. Use `authorize(null)` to restore the default authorization behavior.

You can also use `disabled()` with your authorization logic, for example `disabled(fn ($record) => $record->user_id !== auth()->id())`. Filament checks this condition on the server before saving.

Check permission for the specific attribute being changed, especially sensitive attributes such as administrator access. If the column updates a related model or pivot, authorize that operation too. Alternatively, use a resource edit page or a policy-backed `EditAction` to apply the resource's edit authorization.
