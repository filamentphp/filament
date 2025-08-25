---
title: Overview
---
import Aside from "@components/Aside.astro"
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

Tables are a common UI pattern for displaying lists of records in web applications. Filament provides a PHP-based API for defining tables with many features, while also being incredibly customizable.

### Defining table columns

The basis of any table is rows and columns. Filament uses Eloquent to get the data for rows in the table, and you are responsible for defining the columns that are used in that row.

Filament includes many column types prebuilt for you, and you can [view a full list here](columns/overview). You can even [create your own custom column types](columns/custom-columns) to display data in whatever way you need.

Columns are stored in an array, as objects within the `$table->columns()` method:

```php
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('slug'),
            IconColumn::make('is_featured')
                ->boolean(),
        ]);
}
```

<AutoScreenshot name="tables/overview/columns" alt="Table with columns" version="4.x" />

In this example, there are 3 columns in the table. The first two display [text](columns/text) - the title and slug of each row in the table. The third column displays an [icon](columns/icon), either a green check or a red cross depending on if the row is featured or not.

#### Making columns sortable and searchable

You can easily modify columns by chaining methods onto them. For example, you can make a column [searchable](columns/overview#searching) using the `searchable()` method. Now, there will be a search field in the table, and you will be able to filter rows by the value of that column:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->searchable()
```

<AutoScreenshot name="tables/overview/searchable-columns" alt="Table with searchable column" version="4.x" />

You can make multiple columns searchable, and Filament will be able to search for matches within any of them, all at once.

You can also make a column [sortable](columns/overview#sorting) using the `sortable()` method. This will add a sort button to the column header, and clicking it will sort the table by that column:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->sortable()
```

<AutoScreenshot name="tables/overview/sortable-columns" alt="Table with sortable column" version="4.x" />

#### Accessing related data from columns

You can also display data in a column that belongs to a relationship. For example, if you have a `Post` model that belongs to a `User` model (the author of the post), you can display the user's name in the table:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('author.name')
```

<AutoScreenshot name="tables/overview/relationship-columns" alt="Table with relationship column" version="4.x" />

In this case, Filament will search for an `author` relationship on the `Post` model, and then display the `name` attribute of that relationship. We call this "dot notation", and you can use it to display any attribute of any relationship, even nested relationships. Filament uses this dot notation to eager-load the results of that relationship for you.

For more information about column relationships, visit the [Relationships section](columns/overview#displaying-data-from-relationships).

#### Adding new columns alongside existing columns

While the `columns()` method redefines all columns for a table, you may sometimes want to add columns to an existing configuration without overriding it completely. This is particularly useful when you have global column configurations that should appear across multiple tables.

Filament provides the `pushColumns()` method for this purpose. Unlike `columns()`, which replaces the entire column configuration, `pushColumns()` appends new columns to any existing ones.

This is especially powerful when combined with [global table settings](#global-settings) in the `boot()` method of a service provider, such as `AppServiceProvider`:

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

Table::configureUsing(function (Table $table) {
    $table
        ->pushColumns([
            TextColumn::make('created_at')
                ->label('Created')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('updated_at')
                ->label('Updated')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ]);
});
```

### Defining table filters

As well as making columns `searchable()`, which allows the user to filter the table by searching the content of columns, you can also allow the users to filter rows in the table in other ways. [Filters](filters) can be defined in the `$table->filters()` method:

```php
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->filters([
            Filter::make('is_featured')
                ->query(fn (Builder $query) => $query->where('is_featured', true)),
            SelectFilter::make('status')
                ->options([
                    'draft' => 'Draft',
                    'reviewing' => 'Reviewing',
                    'published' => 'Published',
                ]),
        ]);
}
```

<AutoScreenshot name="tables/overview/filters" alt="Table with filters" version="4.x" />

In this example, we have defined 2 table filters. On the table, there is now a "filter" icon button in the top corner. Clicking it will open a dropdown with the 2 filters we have defined.

The first filter is rendered as a checkbox. When it's checked, only featured rows in the table will be displayed. When it's unchecked, all rows will be displayed.

The second filter is rendered as a select dropdown. When a user selects an option, only rows with that status will be displayed. When no option is selected, all rows will be displayed.

You can use any [schema component](../schemas) to build the UI for a filter. For example, you could create [a custom date range filter](filters/custom).

### Defining table actions

Filament's tables can use [actions](../actions/overview). They are buttons that can be added to the [end of any table row](actions#record-actions), or even in the [header](actions#header-actions) of a table. For instance, you may want an action to "create" a new record in the header, and then "edit" and "delete" actions on each row. [Bulk actions](actions#bulk-actions) can be used to execute code when records in the table are selected.

```php
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->recordActions([
            Action::make('feature')
                ->action(function (Post $record) {
                    $record->is_featured = true;
                    $record->save();
                })
                ->hidden(fn (Post $record): bool => $record->is_featured),
            Action::make('unfeature')
                ->action(function (Post $record) {
                    $record->is_featured = false;
                    $record->save();
                })
                ->visible(fn (Post $record): bool => $record->is_featured),
        ])
        ->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
}
```

<AutoScreenshot name="tables/overview/actions" alt="Table with actions" version="4.x" />

In this example, we define 2 actions for table rows. The first action is a "feature" action. When clicked, it will set the `is_featured` attribute on the record to `true` - which is written within the `action()` method. Using the `hidden()` method, the action will be hidden if the record is already featured. The second action is an "unfeature" action. When clicked, it will set the `is_featured` attribute on the record to `false`. Using the `visible()` method, the action will be hidden if the record is not featured.

We also define a bulk action. When bulk actions are defined, each row in the table will have a checkbox. This bulk action is [built-in to Filament](../actions/delete#bulk-delete), and it will delete all selected records. However, you can [write your own custom bulk actions](actions#bulk-actions) easily too.

<AutoScreenshot name="tables/overview/actions-modal" alt="Table with action modal open" version="4.x" />

Actions can also open modals to request confirmation from the user, as well as render forms inside to collect extra data. It's a good idea to read the [Actions documentation](../actions) to learn more about their extensive capabilities throughout Filament.

## Pagination

By default, Filament tables will be paginated. The user can choose between 5, 10, 25, and 50 records per page. If there are more records than the selected number, the user can navigate between pages using the pagination buttons.

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

<Aside variant="warning">
    Be aware when using very high numbers and `all` as large number of records can cause performance issues.
</Aside>

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

<Aside variant="info">
    Make sure that the default pagination page option is included in the [pagination options](#customizing-the-pagination-options).
</Aside>

### Displaying links to the first and the last pagination page

To add "extreme" links to the first and the last page using the `extremePaginationLinks()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->extremePaginationLinks();
}
```

### Using simple pagination

You may use simple pagination by using the `paginationMode(PaginationMode::Simple)` method:

```php
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->paginationMode(PaginationMode::Simple);
}
```

### Using cursor pagination

You may use cursor pagination by using the `paginationMode(PaginationMode::Cursor)` method:

```php
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->paginationMode(PaginationMode::Cursor);
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

When using a [resource](../resources) table, the URL for each row is usually already set up for you, but this method can be called to override the default URL for each row.

<Aside variant="tip">
    You can also [override the URL](columns/overview#opening-urls) for a specific column, or [trigger an action](columns/overview#triggering-actions) when a column is clicked.
</Aside>

You may also open the URL in a new tab:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->openRecordUrlInNewTab();
}
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

<AutoScreenshot name="tables/reordering" alt="Table with reorderable rows" version="4.x" />

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

You can pass a `direction` parameter as `desc` to reorder the records in descending order instead of ascending:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->reorderable('sort', direction: 'desc');
}
```

### Enabling pagination while reordering

Pagination will be disabled in reorder mode to allow you to move records between pages. It is generally a bad experience to have pagination while reordering, but if would like to override this use `$table->paginatedWhileReordering()`:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->paginatedWhileReordering();
}
```

### Customizing the reordering trigger action

To customize the reordering trigger button, you may use the `reorderRecordsTriggerAction()` method, passing a closure that returns an action. All methods that are available to [customize action trigger buttons](../actions/overview) can be used:

```php
use Filament\Actions\Action;
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

<AutoScreenshot name="tables/reordering/custom-trigger-action" alt="Table with reorderable rows and a custom trigger action" version="4.x" />

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

You can pass a view to the `$table->header()` method to customize the entire header HTML:

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

While Filament doesn't provide a direct integration with [Laravel Scout](https://laravel.com/docs/scout), you may use the `searchUsing()` method with a `whereKey()` clause to filter the query for Scout results:

```php
use App\Models\Post;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

public function table(Table $table): Table
{
    return $table
        ->searchUsing(fn (Builder $query, string $search) => $query->whereKey(Post::search($search)->keys()));
```

Under normal circumstances Scout uses the `whereKey()` (`whereIn()`) method to retrieve results internally, so there is no performance penalty for using it.

For the global search input to show, at least one column in the table needs to be `searchable()`. Alternatively, if you are using Scout to control which columns are searchable already, you can simply pass `searchable()` to the entire table instead:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->searchable();
}
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

<AutoScreenshot name="tables/striped" alt="Table with striped rows" version="4.x" />

### Custom row classes

You may want to conditionally style rows based on the record data. This can be achieved by specifying a string or array of CSS classes to be applied to the row using the `$table->recordClasses()` method:

```php
use App\Models\Post;
use Closure;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

public function table(Table $table): Table
{
    return $table
        ->recordClasses(fn (Post $record) => match ($record->status) {
            'draft' => 'draft-post-table-row',
            'reviewing' => 'reviewing-post-table-row',
            'published' => 'published-post-table-row',
            default => null,
        });
}
```

## Global settings

To customize the default configuration used for all tables, you can call the static `configureUsing()` method from the `boot()` method of a service provider. The function will be run for each table that gets created:

```php
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

Table::configureUsing(function (Table $table): void {
    $table
        ->reorderableColumns()
        ->filtersLayout(FiltersLayout::AboveContentCollapsible)
        ->paginationPageOptions([10, 25, 50]);
});
```
