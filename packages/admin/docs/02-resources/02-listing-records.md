---
title: Listing records
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

### Displaying filters above or below the table content

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

To render the filters below the table content instead of in a popover, you may use:

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
            layout: Layout::BelowContent,
        );
}
```

## Actions

[Actions](../../tables/actions#single-actions) are buttons that are rendered at the end of table rows. They allow the user to perform a task on a record in the table. To learn how to build actions, see the [full actions documentation](../../tables/actions#single-actions).

To add actions to a table, use the `$table->actions()` method:

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
            // ...
            Tables\Actions\Action::make('activate')
                ->action(fn (Post $record) => $record->activate())
                ->requiresConfirmation()
                ->color('success'),
        ]);
}
```

### Grouping actions

You may use an `ActionGroup` object to group multiple table actions together in a dropdown:

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
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]),
        ]);
}
```

## Bulk actions

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
        ->bulkActions([
            // ...
            Tables\Actions\BulkAction::make('activate')
                ->action(fn (Collection $records) => $records->each->activate())
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-check'),
        ]);
}
```

## Creating records in modals

If your resource is simple, you may wish to create records in a modal rather than on the [Create page](creating-records).

If you set up a [modal resource](getting-started#simple-modal-resources) you'll already be able to do this. But, if you have a normal resource and want to open a modal for creating records, [delete the resource's Create page](getting-started#deleting-pages).

### Customizing data before saving

Sometimes, you may wish to modify form data before it is finally saved to the database. To do this, you may use the `mutateFormDataUsing()` method, which accepts the `$data` as an array, and returns the modified version:

```php
use Filament\Tables\Actions\CreateAction;

CreateAction::make()
    ->mutateFormDataUsing(function (array $data): array {
        $data['user_id'] = auth()->id();

        return $data;
    })
```

### Customizing the creation process

You can tweak how the record is created using the `using()` method:

```php
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Model;

CreateAction::make()
    ->using(function (array $data): Model {
        return static::getModel()::create($data);
    })
```

### Customizing the save notification

When the record is successfully created, a notification is dispatched to the user, which indicates the success of their action.

To customize the text content of this notification:

```php
use Filament\Tables\Actions\CreateAction;

CreateAction::make()
    ->successNotificationMessage('User registered')
```

And to disable the notification altogether:

```php
use Filament\Tables\Actions\CreateAction;

CreateAction::make()
    ->successNotificationMessage(null)
```

### Lifecycle hooks

Hooks may be used to execute code at various points within an action's lifecycle.

```php
use Filament\Tables\Actions\CreateAction;

CreateAction::make()
    ->beforeFormFilled(function () {
        // Runs before the form fields are populated with their default values.
    })
    ->afterFormFilled(function () {
        // Runs after the form fields are populated with their default values.
    })
    ->beforeFormValidated(function () {
        // Runs before the form fields are validated when the form is submitted.
    })
    ->afterFormValidated(function () {
        // Runs after the form fields are validated when the form is submitted.
    })
    ->before(function () {
        // Runs before the form fields are saved to the database.
    })
    ->after(function () {
        // Runs after the form fields are saved to the database.
    })
```

## Editing records in modals

If your resource is simple, you may wish to edit records a modal rather than on the [Edit page](editing-records).

If you set up a [modal resource](getting-started#simple-modal-resources) you'll already be able to do this. But, if you have a normal resource and want to open a modal for editing records, [delete the resource's Edit page](getting-started#deleting-pages).

### Customizing data before filling the form

You may wish to modify the data from a record before it is filled into the form. To do this, you may use the `mutateRecordDataUsing()` method to modify the `$data` array, and return the modified version before it is filled into the form:

```php
use Filament\Tables\Actions\EditAction;

EditAction::make()
    ->mutateRecordDataUsing(function (array $data): array {
        $data['user_id'] = auth()->id();

        return $data;
    })
```

### Customizing data before saving

Sometimes, you may wish to modify form data before it is finally saved to the database. To do this, you may define a `mutateFormDataUsing()` method, which accepts the `$data` as an array, and returns it modified:

```php
use Filament\Tables\Actions\EditAction;

EditAction::make()
    ->mutateFormDataUsing(function (array $data): array {
        $data['last_edited_by_id'] = auth()->id();

        return $data;
    })
```

### Customizing the saving process

You can tweak how the record is updated using the `using()` method:

```php
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;

EditAction::make()
    ->using(function (Model $record, array $data): Model {
        $record->update($data);

        return $record;
    })
```

### Customizing the save notification

When the record is successfully updated, a notification is dispatched to the user, which indicates the success of their action.

To customize the text content of this notification:

```php
use Filament\Tables\Actions\EditAction;

EditAction::make()
    ->successNotificationMessage('User updated')
```

And to disable the notification altogether:

```php
use Filament\Tables\Actions\EditAction;

EditAction::make()
    ->successNotificationMessage(null)
```

### Lifecycle hooks

Hooks may be used to execute code at various points within an action's lifecycle.

```php
use Filament\Tables\Actions\EditAction;

EditAction::make()
    ->beforeFormFilled(function () {
        // Runs before the form fields are populated from the database.
    })
    ->afterFormFilled(function () {
        // Runs after the form fields are populated from the database.
    })
    ->beforeFormValidated(function () {
        // Runs before the form fields are validated when the form is saved.
    })
    ->afterFormValidated(function () {
        // Runs after the form fields are validated when the form is saved.
    })
    ->before(function () {
        // Runs before the form fields are saved to the database.
    })
    ->after(function () {
        // Runs after the form fields are saved to the database.
    })
```

## Viewing records in modals

If your resource is simple, you may wish to view records in modals rather than on the [View page](viewing-records). If this is the case, you can just [delete the view page](getting-started#deleting-pages).

If your resource doesn't contain a `ViewAction`, you can add one to the `$table->actions()` array:

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
            Tables\Actions\ViewAction::make(),
            // ...
        ]);
}
```

## Deleting records

By default, you can bulk-delete records in your table. You may also wish to delete single records, using a `DeleteAction`:

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
            // ...
            Tables\Actions\DeleteAction::make(),
        ]);
}
```

## Reordering records

To allow the user to reorder records using drag and drop in your table, you can use the `reorderable()` method:

```php
use Filament\Resources\Table;

public static function table(Table $table): Table
{
    return $table
        // ...
        ->reorderable('sort');
}
```

When making the table reorderable, a new button will be available on the table to toggle reordering. Pagination will be disabled in reorder mode to allow you to move records between pages.

The `reorderable()` method passes in the name of a column to store the record order in. If you use something like [`spatie/eloquent-sortable`](https://github.com/spatie/eloquent-sortable) with an order column such as `order_column`, you may pass this in to `orderable()`:

```php
use Filament\Resources\Table;

public static function table(Table $table): Table
{
    return $table
        // ...
        ->reorderable('order_column');
}
```

## Polling content

You may poll table content so that it refreshes at a set interval, using the `poll()` method:

```php
use Filament\Resources\Table;

public static function table(Table $table): Table
{
    return $table
        // ...
        ->poll('10s');
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
