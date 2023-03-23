---
title: Getting started
---

Column classes can be found in the `Filament\Tables\Columns` namespace.

If you're using the columns in a Livewire component, you can put them in the `getTableColumns()` method:

```php
protected function getTableColumns(): array
{
    return [
        // ...
    ];
}
```

If you're using them in admin panel resources or relation managers, you must put them in the `$table->columns()` method:

```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ]);
}
```

Columns may be created using the static `make()` method, passing its name. The name of the column should correspond to a column or accessor on your model. You may use "dot syntax" to access columns within relationships.

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')

TextColumn::make('author.name')
```

## Available columns

Filament ships with two main types of columns - static and editable.

Static columns display data to the user:

- [Text column](text)
- [Icon column](icon)
- [Image column](image)
- [Badge column](badge)
- [Tags column](tags)
- [Color column](color)

Editable columns allow the user to update data in the database without leaving the table:

- [Select column](select)
- [Toggle column](toggle)
- [Text input column](text-input)
- [Checkbox column](checkbox)

You may also [create your own custom columns](custom) to display data however you wish.

## Setting a label

By default, the label of the column, which is displayed in the header of the table, is generated from the name of the column. You may customize this using the `label()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')->label('Post title')
```

Optionally, you can have the label automatically translated by using the `translateLabel()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')->translateLabel() // Equivalent to `label(__('Title'))`
```

## Sorting

Columns may be sortable, by clicking on the column label. To make a column sortable, you must use the `sortable()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')->sortable()
```

If you're using an accessor column, you may pass `sortable()` an array of database columns to sort by:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('full_name')->sortable(['first_name', 'last_name'])
```

You may customize how the sorting is applied to the Eloquent query using a callback:

```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

TextColumn::make('full_name')
    ->sortable(query: function (Builder $query, string $direction): Builder {
        return $query
            ->orderBy('last_name', $direction)
            ->orderBy('first_name', $direction);
    })
```

If a column is `sortable()`, you may choose to sort it by default using the `getDefaultTableSortColumn()` and `getDefaultTableSortDirection()` methods:

```php
protected function getDefaultTableSortColumn(): ?string
{
    return 'full_name';
}

protected function getDefaultTableSortDirection(): ?string
{
    return 'asc';
}
```

### Persist sort in session

To persist the sort in the user's session, override the `shouldPersistTableSortInSession()` method on the Livewire component:

```php
protected function shouldPersistTableSortInSession(): bool
{
    return true;
}
```

## Searching

Columns may be searchable, by using the text input in the top right of the table. To make a column searchable, you must use the `searchable()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')->searchable()
```

If you're using an accessor column, you may pass `searchable()` an array of database columns to search within:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('full_name')->searchable(['first_name', 'last_name'])
```

You may customize how the search is applied to the Eloquent query using a callback:

```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

TextColumn::make('full_name')
    ->searchable(query: function (Builder $query, string $search): Builder {
        return $query
            ->where('first_name', 'like', "%{$search}%")
            ->where('last_name', 'like', "%{$search}%");
    })
```

### Searching individually

You can choose to enable a per-column search input using the `isIndividual` parameter:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')->searchable(isIndividual: true)
```

If you use the `isIndividual` parameter, you may still search that column using the main "global" search input for the entire table.

To disable that functionality while still preserving the individual search functionality, you need the `isGlobal` parameter:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')->searchable(isIndividual: true, isGlobal: false)
```

You may optionally persist the searches in the query string:

```php
protected $queryString = [
    // ...
    'tableColumnSearchQueries',
];
```

### Persist search in session

To persist the table or individual column search in the user's session, override the `shouldPersistTableSearchInSession()` or `shouldPersistTableColumnSearchInSession()` method on the Livewire component:

```php
protected function shouldPersistTableSearchInSession(): bool
{
    return true;
}

protected function shouldPersistTableColumnSearchInSession(): bool
{
    return true;
}
```

## Cell actions and URLs

When a cell is clicked, you may run an "action", or open a URL.

### Running actions

To run an action, you may use the `action()` method, passing a callback or the name of a Livewire method to run. Each method accepts a `$record` parameter which you may use to customize the behaviour of the action:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->action(function (Post $record): void {
        $this->dispatchBrowserEvent('open-post-edit-modal', [
            'post' => $record->getKey(),
        ]);
    })
```

#### Action modals

You may open [action modals](../actions#modals) by passing in an `Action` object to the `action()` method:

```php
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->action(
        Action::make('select')
            ->requiresConfirmation()
            ->action(function (Post $record): void {
                $this->dispatchBrowserEvent('select-post', [
                    'post' => $record->getKey(),
                ]);
            }),
    )
```

Action objects passed into the `action()` method must have a unique name to distinguish it from other actions within the table.

### Opening URLs

To open a URL, you may use the `url()` method, passing a callback or static URL to open. Callbacks accept a `$record` parameter which you may use to customize the URL:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->url(fn (Post $record): string => route('posts.edit', ['post' => $record]))
```

You may also choose to open the URL in a new tab:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->url(fn (Post $record): string => route('posts.edit', ['post' => $record]))
    ->openUrlInNewTab()
```

## Setting a default value

To set a default value for fields with a `null` state, you may use the `default()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')->default('Untitled')
```

## Hiding columns

To hide a column conditionally, you may use the `hidden()` and `visible()` methods, whichever you prefer:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('role')->hidden(! auth()->user()->isAdmin())
// or
TextColumn::make('role')->visible(auth()->user()->isAdmin())
```

### Toggling column visibility

Users may hide or show columns themselves in the table. To make a column toggleable, use the `toggleable()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('id')->toggleable()
```

By default, toggleable columns are visible. To make them hidden instead:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('id')->toggleable(isToggledHiddenByDefault: true)
```

## Calculated state

Sometimes you need to calculate the state of a column, instead of directly reading it from a database column.

By passing a callback function to the `getStateUsing()` method, you can customize the returned state for that column based on the `$record`:

```php
Tables\Columns\TextColumn::make('amount_including_vat')
    ->getStateUsing(function (Model $record): float {
        return $record->amount * (1 + $record->vat_rate);
    })
```

## Tooltips

> If you want to use tooltips outside of the admin panel, make sure you have [`@ryangjchandler/alpine-tooltip` installed](https://github.com/ryangjchandler/alpine-tooltip#installation) in your app, including [`tippy.css`](https://atomiks.github.io/tippyjs/v6/getting-started/#1-package-manager). You'll also need to install [`tippy.css`](https://atomiks.github.io/tippyjs/v6/getting-started/#1-package-manager) if you're using a [custom admin theme](/docs/admin/appearance#building-themes).

You may specify a tooltip to display when you hover over a cell:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->tooltip('Title')
```

This method also accepts a closure that can access the current table record:

```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

TextColumn::make('title')
    ->tooltip(fn (Model $record): string => "By {$record->author->name}")
```

## Custom attributes

The HTML of columns can be customized, by passing an array of `extraAttributes()`:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('slug')->extraAttributes(['class' => 'bg-gray-200'])
```

These get merged onto the outer `<div>` element of each cell in that column.

## Global settings

If you wish to change the default behaviour of all columns globally, then you can call the static `configureUsing()` method inside a service provider's `boot()` method, to which you pass a Closure to modify the columns using. For example, if you wish to make all columns [`sortable()`](#sorting) and [`toggleable()`](#toggling-column-visibility), you can do it like so:

```php
use Filament\Tables\Columns\Column;

Column::configureUsing(function (Column $column): void {
    $column
        ->toggleable()
        ->sortable();
});
```

Additionally, you can call this code on specific column types as well:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::configureUsing(function (TextColumn $column): void {
    $column
        ->toggleable()
        ->sortable();
});
```

Of course, you are still able to overwrite this on each column individually:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('name')->toggleable(false)
```
