---
title: Advanced
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Pagination

### Disabling pagination

By default, tables will be paginated. To disable this, you should use the `$table->paginated(false)` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->paginated(false);
}
```

### Customizing the pagination options

You may customize the options for the paginated records per page select by passing them to the `paginated()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->paginated([10, 25, 50, 100, 'all']);
}
```

### Customizing the default pagination page option

To customize the default number of records shown use the `defaultPaginationPageOption()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->defaultPaginationPageOption(25);
}
```

### Preventing query string conflicts with the pagination page

By default, Livewire stores the pagination state in a `page` parameter of the URL query string. If you have multiple tables on the same page, this will mean that the pagination state of one table may be overwritten by the state of another table.

To fix this, you may define a `$table->queryStringIdentifier()`, to return a unique query string identifier for that table:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->queryStringIdentifier('users');
}
```

### Using simple pagination

You may use simple pagination by overriding `paginateTableQuery()` method.

First, locate your Livewire component. If you're using a resource from the Panel Builder and you want to add simple pagination to the List page, you'll want to open the `Pages/List.php` file in the resource, not the resource class itself.

```php
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

protected function paginateTableQuery(Builder $query): Paginator
{
    return $query->simplePaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
}
```

### Using cursor pagination

You may use cursor pagination by overriding `paginateTableQuery()` method.

First, locate your Livewire component. If you're using a resource from the Panel Builder and you want to add simple pagination to the List page, you'll want to open the `Pages/List.php` file in the resource, not the resource class itself.

```php
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;

protected function paginateTableQuery(Builder $query): CursorPaginator
{
    return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
}
```

## Record URLs (clickable rows)

You may allow table rows to be completely clickable by using the `$table->recordUrl()` method:

```php
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

public function table(Table $table): Table
{
    return $table
        ->recordUrl(
            fn (Model $record): string => route('posts.edit', ['record' => $record]),
        );
}
```

In this example, clicking on each post will take you to the `posts.edit` route.

If you'd like to [override the URL](columns/getting-started#opening-urls) for a specific column, or instead [run an action](columns/getting-started#running-actions) when a column is clicked, see the [columns documentation](columns/getting-started#opening-urls).

## Reordering records

To allow the user to reorder records using drag and drop in your table, you can use the `$table->reorderable()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->reorderable('sort');
}
```

If you're using mass assignment protection on your model, you will also need to add the `sort` attribute to the `$fillable` array there.

When making the table reorderable, a new button will be available on the table to toggle reordering.

<AutoScreenshot name="tables/reordering" alt="Table with reorderable rows" version="3.x" />

The `reorderable()` method accepts the name of a column to store the record order in. If you use something like [`spatie/eloquent-sortable`](https://github.com/spatie/eloquent-sortable) with an order column such as `order_column`, you may use this instead:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->reorderable('order_column');
}
```

The `reorderable()` method also accepts a boolean condition as its second parameter, allowing you to conditionally enable reordering:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->reorderable('sort', auth()->user()->isAdmin());
}
```

### Enabling pagination while reordering

Pagination will be disabled in reorder mode to allow you to move records between pages. It is generally bad UX to re-enable pagination while reordering, but if you are sure then you can use `$table->paginatedWhileReordering()`:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->paginatedWhileReordering();
}
```

### Customizing the reordering trigger action

To customize the reordering trigger button, you may use the `reorderRecordsTriggerAction()` method, passing a closure that returns an action. All methods that are available to [customize action trigger buttons](../actions/trigger-button) can be used:

```php
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->reorderRecordsTriggerAction(
            fn (Action $action, bool $isReordering) => $action
                ->button()
                ->label($isReordering ? 'Disable reordering' : 'Enable reordering'),
        );
}
```

<AutoScreenshot name="tables/reordering/custom-trigger-action" alt="Table with reorderable rows and a custom trigger action" version="3.x" />

## Customizing the table header

You can add a heading to a table using the `$table->heading()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->heading('Clients')
        ->columns([
            // ...
        ]);
```

You can also add a description below the heading using the `$table->description()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->heading('Clients')
        ->description('Manage your clients here.')
        ->columns([
            // ...
        ]);
```

You can pass a view to the `$table->header()` method to customize the entire header:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->header(view('tables.header', [
            'heading' => 'Clients',
        ]))
        ->columns([
            // ...
        ]);
```

## Polling table content

You may poll table content so that it refreshes at a set interval, using the `$table->poll()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->poll('10s');
}
```

## Deferring loading

Tables with lots of data might take a while to load, in which case you can load the table data asynchronously using the `deferLoading()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->deferLoading();
}
```

## Searching records with Laravel Scout

While Filament doesn't provide a direct integration with [Laravel Scout](https://laravel.com/docs/scout), you may override methods to integrate it.

Use a `whereIn()` clause to filter the query for Scout results:

```php
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;

protected function applySearchToTableQuery(Builder $query): Builder
{
    $this->applyColumnSearchesToTableQuery($query);
    
    if (filled($search = $this->getTableSearch())) {
        $query->whereIn('id', Post::search($search)->keys());
    }

    return $query;
}
```

Scout uses this `whereIn()` method to retrieve results internally, so there is no performance penalty for using it.

The `applyColumnSearchesToTableQuery()` method ensures that searching individual columns will still work. You can replace that method with your own implementation if you want to use Scout for those search inputs as well.

For the global search input to show, at least one column in the table needs to be `searchable()`. Alternatively, if you are using Scout to control which columns are searchable already, you can simply pass `searchable()` to the entire table instead:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->searchable();
}
```

## Query string

Livewire ships with a feature to store data in the URL's query string, to access across requests.

With Filament, this allows you to store your table's filters, sort, search and pagination state in the URL.

To store the filters, sorting, and search state of your table in the query string:

```php
use Livewire\Attributes\Url;

#[Url]
public bool $isTableReordering = false;

/**
 * @var array<string, mixed> | null
 */
#[Url]
public ?array $tableFilters = null;

#[Url]
public ?string $tableGrouping = null;

#[Url]
public ?string $tableGroupingDirection = null;

/**
 * @var ?string
 */
#[Url]
public $tableSearch = '';

#[Url]
public ?string $tableSortColumn = null;

#[Url]
public ?string $tableSortDirection = null;
```

## Styling table rows

### Striped table rows

To enable striped table rows, you can use the `striped()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->striped();
}
```

<AutoScreenshot name="tables/striped" alt="Table with striped rows" version="3.x" />

### Custom row classes

You may want to conditionally style rows based on the record data. This can be achieved by specifying a string or array of CSS classes to be applied to the row using the `$table->recordClasses()` method:

```php
use Closure;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

public function table(Table $table): Table
{
    return $table
        ->recordClasses(fn (Model $record) => match ($record->status) {
            'draft' => 'opacity-30',
            'reviewing' => 'border-s-2 border-orange-600 dark:border-orange-300',
            'published' => 'border-s-2 border-green-600 dark:border-green-300',
            default => null,
        });
}
```

These classes are not automatically compiled by Tailwind CSS. If you want to apply Tailwind CSS classes that are not already used in Blade files, you should update your `content` configuration in `tailwind.config.js` to also scan for classes inside your directory: `'./app/Filament/**/*.php'`

## Resetting the table

If you make changes to the table definition during a Livewire request, for example, when consuming a public property in the `table()` method, you may need to reset the table to ensure that the changes are applied. To do this, you can call the `resetTable()` method on the Livewire component:

```php
$this->resetTable();
```

## Global settings

To customize the default configuration that is used for all tables, you can call the static `configureUsing()` method from the `boot()` method of a service provider. The function will be run for each table that gets created:

```php
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

Table::configureUsing(function (Table $table): void {
    $table
        ->filtersLayout(FiltersLayout::AboveContentCollapsible)
        ->paginationPageOptions([10, 25, 50]);
});
```
