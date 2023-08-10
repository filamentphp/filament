---
title: Deleting records
---

## Handling soft deletes

## Creating a resource with soft delete

By default, you will not be able to interact with deleted records in the app. If you'd like to add functionality to restore, force delete and filter trashed records in your resource, use the `--soft-deletes` flag when generating the resource:

```bash
php artisan make:filament-resource Customer --soft-deletes
```

## Adding soft deletes to an existing resource

Alternatively, you may add soft deleting functionality to an existing resource.

Firstly, you must update the resource:

```php
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->filters([
            Tables\Filters\TrashedFilter::make(),
            // ...
        ])
        ->actions([
            // You may add these actions to your table if you're using a simple
            // resource, or you just want to be able to delete records without
            // leaving the table.
            Tables\Actions\DeleteAction::make(),
            Tables\Actions\ForceDeleteAction::make(),
            Tables\Actions\RestoreAction::make(),
            // ...
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                // ...
            ]),
        ]);
}

public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
}
```

Now, update the Edit page class if you have one:

```php
use Filament\Actions;

protected function getHeaderActions(): array
{
    return [
        Actions\DeleteAction::make(),
        Actions\ForceDeleteAction::make(),
        Actions\RestoreAction::make(),
        // ...
    ];
}
```

## Deleting records on the List page

By default, you can bulk-delete records in your table. You may also wish to delete single records, using a `DeleteAction`:

```php
use Filament\Tables;
use Filament\Tables\Table;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->actions([
            // ...
            Tables\Actions\DeleteAction::make(),
        ]);
}
```

## Authorization

For authorization, Filament will observe any [model policies](https://laravel.com/docs/authorization#creating-policies) that are registered in your app.

Users may delete records if the `delete()` method of the model policy returns `true`.

They also have the ability to bulk-delete records if the `deleteAny()` method of the policy returns `true`. Filament uses the `deleteAny()` method because iterating through multiple records and checking the `delete()` policy is not very performant.

### Authorizing soft deletes

The `forceDelete()` policy method is used to prevent a single soft-deleted record from being force-deleted. `forceDeleteAny()` is used to prevent records from being bulk force-deleted. Filament uses the `forceDeleteAny()` method because iterating through multiple records and checking the `forceDelete()` policy is not very performant.

The `restore()` policy method is used to prevent a single soft-deleted record from being restored. `restoreAny()` is used to prevent records from being bulk restored. Filament uses the `restoreAny()` method because iterating through multiple records and checking the `restore()` policy is not very performant.
