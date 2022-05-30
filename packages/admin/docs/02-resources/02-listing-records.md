---
title: Listing Records
---

## Columns

The `$table->columns()` method is used to define the [columns](../../tables/columns) in your table. It is an array of column objects, in the order they should appear in your table.

We have many fields available for your forms, including:

- [Text column](../../tables/columns#text-column)
- [Boolean column](../../tables/columns#boolean-column)
- [Image column](../../tables/columns#image-column)
- [Icon column](../../tables/columns#icon-column)
- [Badge column](../../tables/columns#badge-column)

To view a full list of available table [columns](../../tables/columns), see the [Table Builder documentation](../../tables/columns).

You may also build your own completely [custom table columns](../../tables/columns#building-custom-columns).

### Sorting a column by default

If a column is `sortable()`, you may choose to sort it by default using the `$table->defaultSort()` method:

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

[Filters](../../tables/filters) are predefined scopes that administrators can use to filter records in your table. The `$table->filters()` method is used to register these.

### Displaying filters above the table content

To render the filters above the table content instead of in a popover, you may use:

```php
use Filament\Tables\Filters\Layout;
use Filament\Resources\Table;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->filters(
            [
                // ...
            ],
            layout: Layout::AboveContent,
        );
}
```

## Actions

[Actions](../../tables/actions#single-actions) are buttons that are rendered at the end of table rows. They allow the user to perform a task on a record in the table. To learn how to build actions, see the [full actions documentation](../../tables/actions#single-actions).

To add actions before the default "Edit" / "View" actions, use the `$table->prependActions()` method:

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
            Tables\Actions\Action::make('activate')
                ->action(fn (Post $record) => $record->activate())
                ->requiresConfirmation()
                ->color('success'),
        ]);
}
```

To add actions after the default "Edit" / "View" actions, use the `$table->pushActions()` method:

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
            Tables\Actions\Action::make('activate')
                ->action(fn (Post $record) => $record->activate())
                ->requiresConfirmation()
                ->color('success'),
        ]);
}
```

To replace the default "Edit" / "View" actions, use the `$table->actions()` method:

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
            Tables\Actions\Action::make('activate')
                ->action(fn (Post $record) => $record->activate())
                ->requiresConfirmation()
                ->color('success'),
        ]);
}
```

## Bulk Actions

[Bulk actions](../../tables/actions#bulk-actions) are buttons that are rendered in a dropdown in the header of the table. They appear when you select records using the checkboxes at the start of each table row. They allow the user to perform a task on multiple records at once in the table. To learn how to build bulk actions, see the [full actions documentation](../../tables/actions#bulk-actions).

To add bulk actions before the "Delete selected" action, use the `$table->prependBulkActions()` method:

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

To add bulk actions after the "Delete selected" action, use the `$table->pushBulkActions()` method:

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

To replace the "Delete selected" action, use the `$table->bulkActions()` method:

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

## Creating records in modals

If your resource is simple, you may wish to create records in modals rather than on the [Create page](creating-records).

If you set up a [modal resource](getting-started#simple-modal-resources) you'll already be able to do this. But, if you have a normal resource and want to open a modal for creating records, add the `CanCreateRecords` trait to the List page class:

```php
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    use ListRecords\Concerns\CanCreateRecords;

    // ...
}
```

> If your Create page is no longer required, you can [delete it](getting-started#deleting-pages).

As per the documentation on [creating records](creating-records), you may use all the same utilities for customisation, but on the List page class instead of the Create page class:

- [Customizing data before saving](creating-records#customizing-data-before-saving)
- [Customizing the creation process](creating-records#customizing-the-creation-process)
- [Customizing the save notification](creating-records#customizing-the-save-notification)
- [Lifecycle hooks](creating-records#lifecycle-hooks), with the addition of specific `beforeCreateFill()`, `afterCreateFill()`, `beforeCreateValidate()` and `afterCreateValidate()` hooks in case you also [edit records in modals](#editing-records-in-modals)

## Editing records in modals

If your resource is simple, you may wish to edit records in modals rather than on the [Edit page](editing-records).

If you set up a [modal resource](getting-started#simple-modal-resources) you'll already be able to do this. But, if you have a normal resource and want to open a modal for editing records, add the `CanEditRecords` trait to the List page class:

```php
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    use ListRecords\Concerns\CanEditRecords;

    // ...
}
```

> If your Edit page is no longer required, you can [delete it](getting-started#deleting-pages).

As per the documentation on [editing records](editing-records), you may use all the same utilities for customisation, but on the List page class instead of the Edit page class:

- [Customizing data before filling the form](editing-records#customizing-data-before-filling-the-form)
- [Customizing data before saving](editing-records#customizing-data-before-saving)
- [Customizing the saving process](editing-records#customizing-the-update-process)
- [Customizing the save notification](editing-records#customizing-the-save-notification)
- [Lifecycle hooks](editing-records#lifecycle-hooks), with the addition of specific `beforeEditFill()`, `afterEditFill()`, `beforeEditValidate()` and `afterEditValidate()` hooks in case you also [create records in modals](#creating-records-in-modals)

## Viewing records in modals

If your resource is simple, you may wish to view records in modals rather than on the [View page](viewing-records).

Simply add the `CanViewRecords` trait to the List page class:

```php
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    use ListRecords\Concerns\CanViewRecords;

    // ...
}
```

> If your View page is no longer required, you can [delete it](getting-started#deleting-pages).

## Deleting records

By default, you can bulk-delete records in your table. You may also wish to delete single records, using an [action](#actions).

If you set up a [modal resource](getting-started#simple-modal-resources) you'll already be able to do this. But, if you have a normal resource, simply add the `CanDeleteRecords` trait to the List page class:

```php
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    use ListRecords\Concerns\CanDeleteRecords;

    // ...
}
```

## Authorization

For authorization, Filament will observe any [model policies](https://laravel.com/docs/authorization#creating-policies) that are registered in your app.

Users may access the List page if the `viewAny()` method of the model policy returns `true`.

They also have the ability to bulk-delete records if the `deleteAny()` method of the policy returns `true`. Filament uses the `deleteAny()` method because iterating through multiple records and checking the `delete()` policy is not very performant.

## Customising the Eloquent query

Although you can [customise the Eloquent query for the entire resource](getting-started#customising-the-eloquent-query), you may also make specific modifications for the List page table. To do this, override the `getTableQuery()` method on the page class:

```php
protected function getTableQuery(): Builder
{
    return parent::getTableQuery()->withoutGlobalScopes();
}
```

## Custom view

For further customization opportunities, you can override the static `$view` property on the page class to a custom view in your app:

```php
protected static string $view = 'filament.resources.users.pages.list-users';
```
