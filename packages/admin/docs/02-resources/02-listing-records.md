---
title: Listing Records
---

## Columns

The `columns()` method is used to define the [columns](../../tables/columns) in your table. It is an array of column objects, in the order they should appear in your table.

We have many fields available for your forms, including:

- [Text column](../../tables/columns#text-column)
- [Boolean column](../../tables/columns#boolean-column)
- [Image column](../../tables/columns#image-column)
- [Icon column](../../tables/columns#icon-column)
- [Badge column](../../tables/columns#badge-column)

To view a full list of available table [columns](../../tables/columns), see the [Table Builder documentation](../../tables/columns).

You may also build your own completely [custom table columns](../../tables/columns#building-custom-columns).

### Sorting a column by default

If a column is `sortable()`, you may choose to sort it by default using the `defaultSort()` method:

```php
use Filament\Resources\Table;
use Filament\Tables;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')->sortable(),
            // ...
        ])
        ->defaultSort('name');
}
```

## Filters

[Filters](../../tables/filters) are predefined scopes that administrators can use to filter records in your table. The `filters()` method is used to register these.

## Actions

You may add more [actions](../../tables/actions#single-actions) to each table row.

To add actions before the default actions, use the `prependActions()` method:

```php
use Filament\Resources\Table;
use Filament\Tables;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->prependActions([
            Tables\Actions\Action::make('delete')
                ->action(fn (Post $record) => $record->delete())
                ->requiresConfirmation()
                ->color('danger'),
        ]);
}
```

To add actions after the default actions, use the `pushActions()` method:

```php
use Filament\Resources\Table;
use Filament\Tables;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->pushActions([
            Tables\Actions\Action::make('delete')
                ->action(fn (Post $record) => $record->delete())
                ->requiresConfirmation()
                ->color('danger'),
        ]);
}
```

To replace the default actions, use the `actions()` method:

```php
use Filament\Resources\Table;
use Filament\Tables;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->actions([
            Tables\Actions\Action::make('delete')
                ->action(fn (Post $record) => $record->delete())
                ->requiresConfirmation()
                ->color('danger'),
        ]);
}
```

## Bulk actions

You may add more [bulk actions](../../tables/actions#bulk-actions) to the table.

To add bulk actions before the default actions, use the `prependBulkActions()` method:

```php
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->prependBulkActions([
            Tables\Actions\BulkAction::make('activate')
                ->action(fn (Collection $records) => $records->each->activate())
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-check'),
        ]);
}
```

To add bulk actions after the default actions, use the `pushBulkActions()` method:

```php
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->pushBulkActions([
            Tables\Actions\BulkAction::make('activate')
                ->action(fn (Collection $records) => $records->each->activate())
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-check'),
        ]);
}
```

To replace the default actions, use the `bulkActions()` method:

```php
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->bulkActions([
            Tables\Actions\BulkAction::make('activate')
                ->action(fn (Collection $records) => $records->each->activate())
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-check'),
        ]);
}
```

## Authorization

For authorization, Filament will observe any [model policies](https://laravel.com/docs/authorization#creating-policies) that are registered in your app.

Users may access the List page if the `viewAny()` method of the model policy returns `true`.

They also have the ability to bulk-delete records if the `deleteAny()` method of the policy returns `true`. Filament uses the `deleteAny()` method because iterating through multiple records and checking the `delete()` policy is not very performant.

## Custom view

For further customization opportunities, you can override the static `$view` property on the page class to a custom view in your app:

```php
protected static string $view = 'filament.resources.users.pages.list-users';
```
