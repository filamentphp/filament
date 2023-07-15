---
title: Listing records
---

## Columns

The `$table->columns()` method is used to define the [columns](../../tables/columns) in your table. It is an array of column objects, in the order they should appear in your table.

We have many columns available for your tables, including:

- [Text column](../../tables/columns/text)
- [Image column](../../tables/columns/image)
- [Icon column](../../tables/columns/icon)
- [Badge column](../../tables/columns/badge)

To view a full list of available table [columns](../../tables/columns), see the [Table Builder documentation](../../tables/columns).

You may also build your own completely [custom table columns](../../tables/columns/custom).

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

To render the filters above the table content in a collapsible panel, you may use:

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
            layout: Layout::AboveContentCollapsible,
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

To add bulk actions, use the `$table->bulkActions()` method:

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

### Record select checkbox position

By default, the record select checkboxes are rendered at the start of the row. You may move them to the end of the row:

```php
use Filament\Resources\Table;
use Filament\Tables\Actions\RecordCheckboxPosition;

public static function table(Table $table): Table
{
    return $table
        ->recordCheckboxPosition(RecordCheckboxPosition::AfterCells)
        ->columns([
            // ...
        ])
        ->bulkActions([
            // ...
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

If you're using mass assignment protection on your model, you will also need to add the `sort` attribute to the `$fillable` array there.

When making the table reorderable, a new button will be available on the table to toggle reordering. Pagination will be disabled in reorder mode to allow you to move records between pages.

The `reorderable()` method passes in the name of a column to store the record order in. If you use something like [`spatie/eloquent-sortable`](https://github.com/spatie/eloquent-sortable) with an order column such as `order_column`, you may pass this in to `reorderable()`:

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

The `reorder()` method is used to control [reordering a record](#reordering-records).

## Customizing the Eloquent query

Although you can [customize the Eloquent query for the entire resource](getting-started#customizing-the-eloquent-query), you may also make specific modifications for the List page table. To do this, override the `getTableQuery()` method on the page class:

```php
protected function getTableQuery(): Builder
{
    return parent::getTableQuery()->withoutGlobalScopes();
}
```

## Customizing the query string

Table search, filters, sorts and other stateful properties are stored in the URL as query strings. Since Filament uses Livewire internally, this behaviour can be modified by overriding the `$queryString` property on the List page of the resource. For instance, you can employ [query string aliases](https://laravel-livewire.com/docs/2.x/query-string#query-string-aliases) to rename some of the properties using `as`:

```php
protected $queryString = [
    'isTableReordering' => ['except' => false],
    'tableFilters' => ['as' => 'filters'], // `tableFilters` is now replaced with `filters` in the query string
    'tableSortColumn' => ['except' => ''],
    'tableSortDirection' => ['except' => ''],
    'tableSearchQuery' => ['except' => '', 'as' => 'search'], // `tableSearchQuery` is now replaced with `search` in the query string
];
```

## Custom view

For further customization opportunities, you can override the static `$view` property on the page class to a custom view in your app:

```php
protected static string $view = 'filament.resources.users.pages.list-users';
```
