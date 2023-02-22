---
title: Advanced
---

## Pagination

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

First, locate your Livewire component. If you're using a resource from the app framework and you want to add simple pagination to the List page, you'll want to open the `Pages/List.php` file in the resource, not the resource class itself.

```php
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

protected function paginateTableQuery(Builder $query): Paginator
{
    return $query->simplePaginate($this->getTableRecordsPerPage() == 'all' ? $query->count() : $this->getTableRecordsPerPage());
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
    if (filled($search = $this->getTableSearch())) {
        $query->whereIn('id', Post::search($search)->keys());
    }

    return $query;
}
```

Scout uses this `whereIn()` method to retrieve results internally, so there is no performance penalty for using it.

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

If you'd like to [override the URL](columns/getting-started#opening-urls) for a specific column, or instead [run a Livewire action](columns#running-actions) when a column is clicked, see the [columns documentation](columns#opening-urls).

## Record classes

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
            'reviewing' => 'border-l-2 border-orange-600 dark:border-orange-300',
            'published' => 'border-l-2 border-green-600 dark:border-green-300',
            default => null,
        });
}
```

These classes are not automatically compiled by Tailwind CSS. If you want to apply Tailwind CSS classes that are not already used in Blade files, you should update your `content` configuration in `tailwind.config.js` to also scan for classes in your desired PHP files:

```js
module.exports = {
    content: ['./app/Filament/**/*.php'],
}
```

Alternatively, you may add the classes to your [safelist](https://tailwindcss.com/docs/content-configuration#safelisting-classes):

```js
module.exports = {
    safelist: [
        'border-green-600',
        'border-l-2',
        'border-orange-600',
        'dark:border-orange-300',
        'opacity-30',
    ],
}
```

## Empty state

By default, an "empty state" card will be rendered when the table is empty. To customize this:

```php
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->emptyStateHeading('No posts yet')
        ->emptyStateDescription('You may create a post using the button below.')
        ->emptyStateIcon('heroicon-o-bookmark')
        ->emptyStateActions([
            Action::make('create')
                ->label('Create post')
                ->url(route('posts.create'))
                ->icon('heroicon-m-plus')
                ->button(),
        ]);
}
```

## Query string

Livewire ships with a feature to store data in the URL's query string, to access across requests.

With Filament, this allows you to store your table's filters, sort, search and pagination state in the URL.

To store the filters, sorting, and search state of your table in the query string:

```php
protected $queryString = [
    'isTableReordering' => ['except' => false],
    'tableFilters',
    'tableSortColumn' => ['except' => ''],
    'tableSortDirection' => ['except' => ''],
    'tableSearch' => ['except' => ''],
    'tableColumnSearches',
];
```

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

The `reorderable()` method accepts the name of a column to store the record order in. If you use something like [`spatie/eloquent-sortable`](https://github.com/spatie/eloquent-sortable) with an order column such as `order_column`, you may use this instead:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->reorderable('order_column');
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

## Polling content

You may poll table content so that it refreshes at a set interval, using the `$form->poll()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->poll('10s');
}
```
