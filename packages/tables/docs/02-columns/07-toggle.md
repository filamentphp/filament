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

## Security

### Authorization

By default, toggle columns do not check model policies. Without an authorization check, users can update the column for records accessible through the table query, even if the record's `update()` policy denies access to its edit page.

Use the `authorize()` method to check the record's `update()` policy before saving. When the check fails, the column is shown as disabled and changes are rejected on the server. You may pass a different ability name, or a function that returns a boolean or an `Illuminate\Auth\Access\Response`:

```php
use Filament\Tables\Columns\ToggleColumn;

ToggleColumn::make('is_admin')
    ->authorize('updateAdminStatus')
```

<UtilityInjection set="tableColumns">As well as allowing a static ability name, the `authorize()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

In a panel with [strict authorization](../../panel-configuration#strict-authorization-mode) enabled, toggle columns in resource and relation manager tables check the resource's `update()` policy by default, unless you call `authorize()` yourself.

You can also use `disabled()` with your authorization logic, for example `disabled(fn ($record) => $record->user_id !== auth()->id())`. Filament checks this condition on the server before saving.

Check permission for the specific attribute being changed, especially sensitive attributes such as administrator access. If the column updates a related model or pivot, authorize that operation too. Alternatively, use a resource edit page or a policy-backed `EditAction` to apply the resource's edit authorization.
