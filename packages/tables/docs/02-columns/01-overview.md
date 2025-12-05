---
title: Overview
---
import Aside from "@components/Aside.astro"
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Column classes can be found in the `Filament\Tables\Columns` namespace. They reside within the `$table->columns()` method. Filament includes a number of columns built-in:

- [Text column](text)
- [Icon column](icon)
- [Image column](image)
- [Color column](color)

Editable columns allow the user to update data in the database without leaving the table:

- [Select column](select)
- [Toggle column](toggle)
- [Text input column](text-input)
- [Checkbox column](checkbox)

You may also [create your own custom columns](custom-columns) to display data however you wish.

Columns may be created using the static `make()` method, passing its unique name. Usually, the name of a column corresponds to the name of an attribute on an Eloquent model. You may use "dot notation" to access attributes within relationships:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')

TextColumn::make('author.name')
```

## Column content (state)

Columns may feel a bit magic at first, but they’re designed to be simple to use and optimized to display data from an Eloquent record. Despite this, they’re flexible and you can display data from any source, not just an Eloquent record attribute.

The data that a column displays is called its "state". When using a [panel resource](../../resources/overview), the table is aware of the records it is displaying. This means that the state of the column is set based on the value of the attribute on the record. For example, if the column is used in the table of a `PostResource`, then the `title` attribute value of the current post will be displayed.

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
```

If you want to access the value stored in a relationship, you can use "dot notation". The name of the relationship that you would like to access data from comes first, followed by a dot, and then the name of the attribute:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('author.name')
```

You can also use "dot notation" to access values within a JSON / array column on an Eloquent model. The name of the attribute comes first, followed by a dot, and then the key of the JSON object you want to read from:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('meta.title')
```

### Setting the state of a column

You can pass your own state to a column by using the `state()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->state('Hello, world!')
```

<UtilityInjection set="tableColumns" except="$state" version="4.x">The `state()` method also accepts a function to dynamically calculate the state. You can inject various utilities into the function as parameters.</UtilityInjection>

### Setting the default state of a column

When a column is empty (its state is `null`), you can use the `default()` method to define alternative state to use instead. This method will treat the default state as if it were real, so columns like [image](image) or [color](color) will display the default image or color.

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->default('Untitled')
```

#### Adding placeholder text if a column is empty

Sometimes you may want to display placeholder text for columns with an empty state, which is styled as a lighter gray text. This differs from the [default value](#setting-the-default-state-of-an-column), as the placeholder is always text and not treated as if it were real state.

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->placeholder('Untitled')
```

<AutoScreenshot name="tables/columns/placeholder" alt="Column with a placeholder for empty state" version="4.x" />

### Displaying data from relationships

You may use "dot notation" to access columns within relationships. The name of the relationship comes first, followed by a period, followed by the name of the column to display:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('author.name')
```

#### Counting relationships

If you wish to count the number of related records in a column, you may use the `counts()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('users_count')->counts('users')
```

In this example, `users` is the name of the relationship to count from. The name of the column must be `users_count`, as this is the convention that [Laravel uses](https://laravel.com/docs/eloquent-relationships#counting-related-models) for storing the result.

If you'd like to scope the relationship before counting, you can pass an array to the method, where the key is the relationship name and the value is the function to scope the Eloquent query with:

```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

TextColumn::make('users_count')->counts([
    'users' => fn (Builder $query) => $query->where('is_active', true),
])
```

#### Determining relationship existence

If you simply wish to indicate whether related records exist in a column, you may use the `exists()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('users_exists')->exists('users')
```

In this example, `users` is the name of the relationship to check for existence. The name of the column must be `users_exists`, as this is the convention that [Laravel uses](https://laravel.com/docs/eloquent-relationships#other-aggregate-functions) for storing the result.

If you'd like to scope the relationship before checking existence, you can pass an array to the method, where the key is the relationship name and the value is the function to scope the Eloquent query with:

```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

TextColumn::make('users_exists')->exists([
    'users' => fn (Builder $query) => $query->where('is_active', true),
])
```

#### Aggregating relationships

Filament provides several methods for aggregating a relationship field, including `avg()`, `max()`, `min()` and `sum()`. For instance, if you wish to show the average of a field on all related records in a column, you may use the `avg()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('users_avg_age')->avg('users', 'age')
```

In this example, `users` is the name of the relationship, while `age` is the field that is being averaged. The name of the column must be `users_avg_age`, as this is the convention that [Laravel uses](https://laravel.com/docs/eloquent-relationships#other-aggregate-functions) for storing the result.

If you'd like to scope the relationship before aggregating, you can pass an array to the method, where the key is the relationship name and the value is the function to scope the Eloquent query with:

```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

TextColumn::make('users_avg_age')->avg([
    'users' => fn (Builder $query) => $query->where('is_active', true),
], 'age')
```

## Setting a column's label

By default, the label of the column, which is displayed in the header of the table, is generated from the name of the column. You may customize this using the `label()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('name')
    ->label('Full name')
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing a static value, the `label()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

Customizing the label in this way is useful if you wish to use a [translation string for localization](https://laravel.com/docs/localization#retrieving-translation-strings):

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('name')
    ->label(__('columns.name'))
```

## Sorting

Columns may be sortable, by clicking on the column label. To make a column sortable, you must use the `sortable()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('name')
    ->sortable()
```

<AutoScreenshot name="tables/columns/sortable" alt="Table with sortable column" version="4.x" />

Using the name of the column, Filament will apply an `orderBy()` clause to the Eloquent query. This is useful for simple cases where the column name matches the database column name. It can also handle [relationships](#displaying-data-from-relationships).

However, many columns are not as simple. The [state](#column-content-state) of the column might be customized, or using an [Eloquent accessor](https://laravel.com/docs/eloquent-mutators#accessors-and-mutators). In this case, you may need to customize the sorting behavior.

You can pass an array of real database columns in the table to sort the column with:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('full_name')
    ->sortable(['first_name', 'last_name'])
```

In this instance, the `full_name` column is not a real column in the database, but the `first_name` and `last_name` columns are. When the `full_name` column is sorted, Filament will sort the table by the `first_name` and `last_name` columns. The reason why two columns are passed is that if two records have the same `first_name`, the `last_name` will be used to sort them. If your use case doesn’t require this, you can pass only one column in the array if you wish.

You may also directly interact with the Eloquent query to customize how sorting is applied for that column:

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

<UtilityInjection set="tableColumns" version="4.x" extras="Direction;;string;;$direction;;The direction that the column is currently being sorted on, either <code>'asc'</code> or <code>'desc'</code>.||Eloquent query builder;;Illuminate\Database\Eloquent\Builder;;$query;;The query builder to modify.">The `query` parameter's function can inject various utilities as parameters.</UtilityInjection>

### Sorting by default

You may choose to sort a table by default if no other sort is applied. You can use the `defaultSort()` method for this:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->defaultSort('stock', direction: 'desc');
}
```

The second parameter is optional and defaults to `'asc'`.

If you pass the name of a table column as the first parameter, Filament will use that column's sorting behavior (custom sorting columns or query function). However, if you need to sort by a column that doesn’t exist in the table or in the database, you should pass a query function instead:

```php
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->defaultSort(function (Builder $query): Builder {
            return $query->orderBy('stock');
        });
}
```

### Persisting the sort in the user's session

To persist the sorting in the user's session, use the `persistSortInSession()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->persistSortInSession();
}
```

### Setting a default sort option label

To set a default sort option label, use the `defaultSortOptionLabel()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->defaultSortOptionLabel('Date');
}
```

## Disabling default primary key sorting

By default, Filament will automatically add a primary key sort to the table query to ensure that the order of records is consistent. The primary key will be sorted in the same direction as the other sort column. If your table doesn't have a primary key, or you wish to disable this behavior, you can use the `defaultKeySort(false)` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->defaultKeySort(false);
}
```

## Searching

Columns may be searchable by using the text input field in the top right of the table. To make a column searchable, you must use the `searchable()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('name')
    ->searchable()
```

<AutoScreenshot name="tables/columns/searchable" alt="Table with searchable column" version="4.x" />

By default, Filament will apply a `where` clause to the Eloquent query, searching for the column name. This is useful for simple cases where the column name matches the database column name. It can also handle [relationships](#displaying-data-from-relationships).

However, many columns are not as simple. The [state](#column-content-state) of the column might be customized, or using an [Eloquent accessor](https://laravel.com/docs/eloquent-mutators#accessors-and-mutators). In this case, you may need to customize the search behavior.

You can pass an array of real database columns in the table to search the column with:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('full_name')
    ->searchable(['first_name', 'last_name'])
```

In this instance, the `full_name` column is not a real column in the database, but the `first_name` and `last_name` columns are. When the `full_name` column is searched, Filament will search the table by the `first_name` and `last_name` columns.

You may also directly interact with the Eloquent query to customize how searching is applied for that column:

```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

TextColumn::make('full_name')
    ->searchable(query: function (Builder $query, string $search): Builder {
        return $query
            ->where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%");
    })
```

<UtilityInjection set="tableColumns" version="4.x" extras="Search;;string;;$search;;The current search input value.||Eloquent query builder;;Illuminate\Database\Eloquent\Builder;;$query;;The query builder to modify.">The `query` parameter's function can inject various utilities as parameters.</UtilityInjection>

### Adding extra searchable columns to the table

You may allow the table to search with extra columns that aren’t present in the table by passing an array of column names to the `searchable()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->searchable(['id']);
}
```

You may use dot notation to search within relationships:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->searchable(['id', 'author.id']);
}
```

You may also pass custom functions to search using:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->searchable([
            'id',
            'author.id',
            function (Builder $query, string $search): Builder {
                if (! is_numeric($search)) {
                    return $query;
                }

                return $query->whereYear('published_at', $search);
            },
        ]);
}
```

### Customizing the table search field placeholder

You may customize the placeholder in the search field using the `searchPlaceholder()` method on the `$table`:

```php
use Filament\Tables\Table;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->searchPlaceholder('Search (ID, Name)');
}
```

### Searching individually

You can choose to enable a per-column search input field using the `isIndividual` parameter:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('name')
    ->searchable(isIndividual: true)
```

<AutoScreenshot name="tables/columns/individually-searchable" alt="Table with individually searchable column" version="4.x" />

If you use the `isIndividual` parameter, you may still search that column using the main "global" search input field for the entire table.

To disable that functionality while still preserving the individual search functionality, you need the `isGlobal` parameter:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->searchable(isIndividual: true, isGlobal: false)
```

### Customizing the table search debounce

You may customize the debounce time in all table search fields using the `searchDebounce()` method on the `$table`. By default, it is set to `500ms`:

```php
use Filament\Tables\Table;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->searchDebounce('750ms');
}
```

### Searching when the input is blurred

Instead of automatically reloading the table contents while the user is typing their search, which is affected by the [debounce](#customizing-the-table-search-debounce) of the search field, you may change the behavior so that the table is only searched when the user blurs the input (tabs or clicks out of it), using the `searchOnBlur()` method:

```php
use Filament\Tables\Table;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->searchOnBlur();
}
```

### Persisting the search in the user's session

To persist the table or individual column search in the user's session, use the `persistSearchInSession()` or `persistColumnSearchInSession()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->persistSearchInSession()
        ->persistColumnSearchesInSession();
}
```

### Disabling search term splitting

By default, the table search will split the search term into individual words and search for each word separately. This allows for more flexible search queries. However, it can have a negative impact on performance when large datasets are involved. You can disable this behavior using the `splitSearchTerms(false)` method on the table:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->splitSearchTerms(false);
}
```

## Clickable cell content

When a cell is clicked, you may open a URL or trigger an "action".

### Opening URLs

To open a URL, you may use the `url()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->url(fn (Post $record): string => route('posts.edit', ['post' => $record]))
```

<UtilityInjection set="tableColumns" version="4.x">The `url()` method also accepts a function to dynamically calculate the value. You can inject various utilities into the function as parameters.</UtilityInjection>

<Aside variant="tip">
    You can also pick a URL for the entire row to open, not just a singular column. Please see the [Record URLs section](../overview#record-urls-clickable-rows).

    When using a record URL and a column URL, the column URL will override the record URL for those cells only.
</Aside>

You may also choose to open the URL in a new tab:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->url(fn (Post $record): string => route('posts.edit', ['post' => $record]))
    ->openUrlInNewTab()
```

### Triggering actions

To run a function when a cell is clicked, you may use the `action()` method. Each method accepts a `$record` parameter which you may use to customize the behavior of the action:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->action(function (Post $record): void {
        $this->dispatch('open-post-edit-modal', post: $record->getKey());
    })
```

#### Action modals

You may open [action modals](../../actions#modals) by passing in an `Action` object to the `action()` method:

```php
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->action(
        Action::make('select')
            ->requiresConfirmation()
            ->action(function (Post $record): void {
                $this->dispatch('select-post', post: $record->getKey());
            }),
    )
```

Action objects passed into the `action()` method must have a unique name to distinguish it from other actions within the table.

#### Preventing cells from being clicked

You may prevent a cell from being clicked by using the `disabledClick()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->disabledClick()
```

If [row URLs](../overview#record-urls-clickable-rows) are enabled, the cell will not be clickable.

## Adding a tooltip to a column

You may specify a tooltip to display when you hover over a cell:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->tooltip('Title')
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing a static value, the `tooltip()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="tables/columns/tooltips" alt="Table with column triggering a tooltip" version="4.x" />

## Adding a header tooltip to a column

You may specify a tooltip to display when you hover over the column header:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('sku')
    ->headerTooltip('Stock Keeping Unit')
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing a static value, the `headerTooltip()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>


## Aligning column content

### Horizontally aligning column content

You may align the content of an column to the start (left in left-to-right interfaces, right in right-to-left interfaces), center, or end (right in left-to-right interfaces, left in right-to-left interfaces) using the `alignStart()`, `alignCenter()` or `alignEnd()` methods:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->alignStart() // This is the default alignment.

TextColumn::make('email')
    ->alignCenter()

TextColumn::make('email')
    ->alignEnd()
```

Alternatively, you may pass an `Alignment` enum to the `alignment()` method:

```php
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->label('Email address')
    ->alignment(Alignment::End)
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing a static value, the `alignment()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="tables/columns/alignment" alt="Table with column aligned to the end" version="4.x" />

### Vertically aligning column content

You may align the content of a column to the start, center, or end using the `verticallyAlignStart()`, `verticallyAlignCenter()` or `verticallyAlignEnd()` methods:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('name')
    ->verticallyAlignStart()

TextColumn::make('name')
    ->verticallyAlignCenter() // This is the default alignment.

TextColumn::make('name')
    ->verticallyAlignEnd()
```

Alternatively, you may pass a `VerticalAlignment` enum to the `verticalAlignment()` method:

```php
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('name')
    ->verticalAlignment(VerticalAlignment::Start)
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing a static value, the `verticalAlignment()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="tables/columns/vertical-alignment" alt="Table with column vertically aligned to the start" version="4.x" />

## Allowing column headers to wrap

By default, column headers will not wrap onto multiple lines if they need more space. You may allow them to wrap using the `wrapHeader()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('name')
    ->wrapHeader()
```

Optionally, you may pass a boolean value to control if the header should wrap:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('name')
    ->wrapHeader(FeatureFlag::active())
```

<UtilityInjection set="tableColumns" version="4.x">The `wrapHeader()` method also accepts a function to dynamically calculate the value. You can inject various utilities into the function as parameters.</UtilityInjection>

## Controlling the width of columns

By default, columns will take up as much space as they need. You may allow some columns to consume more space than others by using the `grow()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('name')
    ->grow()
```

Alternatively, you can define a width for the column, which is passed to the header cell using the `style` attribute, so you can use any valid CSS value:

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('is_paid')
    ->label('Paid')
    ->boolean()
    ->width('1%')
```

<UtilityInjection set="tableColumns" version="4.x">The `width()` method also accepts a function to dynamically calculate the value. You can inject various utilities into the function as parameters.</UtilityInjection>

## Grouping columns

You group multiple columns together underneath a single heading using a `ColumnGroup` object:

```php
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('slug'),
            ColumnGroup::make('Visibility', [
                TextColumn::make('status'),
                IconColumn::make('is_featured'),
            ]),
            TextColumn::make('author.name'),
        ]);
}
```

The first argument is the label of the group, and the second is an array of column objects that belong to that group.

<AutoScreenshot name="tables/columns/grouping" alt="Table with grouped columns" version="4.x" />

You can also control the group header [alignment](#horizontally-aligning-column-content) and [wrapping](#allowing-column-headers-to-wrap) on the `ColumnGroup` object. To improve the multi-line fluency of the API, you can chain the `columns()` onto the object instead of passing it as the second argument:

```php
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\ColumnGroup;

ColumnGroup::make('Website visibility')
    ->columns([
        // ...
    ])
    ->alignCenter()
    ->wrapHeader()
```

## Hiding columns

You may hide a column by using the `hidden()` or `visible()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->hidden()

TextColumn::make('email')
    ->visible()
```

To hide a column conditionally, you may pass a boolean value to either method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('role')
    ->hidden(FeatureFlag::active())

TextColumn::make('role')
    ->visible(FeatureFlag::active())
```

### Allowing users to manage columns

#### Toggling column visibility

Users may hide or show columns themselves in the table. To make a column toggleable, use the `toggleable()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->toggleable()
```

<AutoScreenshot name="tables/columns/column-manager" alt="Table with column manager" version="4.x" />

##### Making toggleable columns hidden by default

By default, toggleable columns are visible. To make them hidden instead:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('id')
    ->toggleable(isToggledHiddenByDefault: true)
```

#### Reordering columns

You may allow columns to be reordered in the table using the `reorderableColumns()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->reorderableColumns();
}
```

<AutoScreenshot name="tables/columns/column-manager-reorderable" alt="Table with reorderable column manager" version="4.x" />

#### Live column manager

By default, column manager changes (toggling and reordering columns) are deferred and do not affect the table, until the user clicks an "Apply" button. To disable this and make the filters "live" instead, use the `deferColumnManager(false)` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->reorderableColumns()
        ->deferColumnManager(false);
}
```

#### Customizing the column manager dropdown trigger action

To customize the column manager dropdown trigger button, you may use the `columnManagerTriggerAction()` method, passing a closure that returns an action. All methods that are available to [customize action trigger buttons](../../actions/overview) can be used:

```php
use Filament\Actions\Action;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->columnManagerTriggerAction(
            fn (Action $action) => $action
                ->button()
                ->label('Column Manager'),
        );
}
```

#### Displaying the reset action in the footer

By default, the reset action appears in the header of the column manager. You may move it to the footer, next to the apply action, using the `columnManagerResetActionPosition()` method:

```php
use Filament\Tables\Enums\ColumnManagerResetActionPosition;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->columnManagerResetActionPosition(ColumnManagerResetActionPosition::Footer);
}
```

#### Disabling column persistence in the user's session

By default, Filament persists the table's columns by storing them in the user's session. To prevent persisting the columns in the user's session, use the `persistColumnsInSession(false)` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->persistColumnsInSession(false);
}
```

## Adding extra HTML attributes to a column content

You can pass extra HTML attributes to the column content via the `extraAttributes()` method, which will be merged onto its outer HTML element. The attributes should be represented by an array, where the key is the attribute name and the value is the attribute value:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('slug')
    ->extraAttributes(['class' => 'slug-column'])
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing a static value, the `extraAttributes()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

By default, calling `extraAttributes()` multiple times will overwrite the previous attributes. If you wish to merge the attributes instead, you can pass `merge: true` to the method.

### Adding extra HTML attributes to the cell

You can also pass extra HTML attributes to the table cell which surrounds the content of the column:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('slug')
    ->extraCellAttributes(['class' => 'slug-cell'])
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing a static value, the `extraCellAttributes()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

By default, calling `extraCellAttributes()` multiple times will overwrite the previous attributes. If you wish to merge the attributes instead, you can pass `merge: true` to the method.

### Adding extra attributes to the header cell

You can pass extra HTML attributes to the table header cell which surrounds the content of the column:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('slug')
    ->extraHeaderAttributes(['class' => 'slug-header-cell'])
```

<UtilityInjection set="tableColumns" version="4.x">As well as allowing a static value, the `extraHeaderAttributes()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

By default, calling `extraHeaderAttributes()` multiple times will overwrite the previous attributes. If you wish to merge the attributes instead, you can pass `merge: true` to the method.

## Column utility injection

The vast majority of methods used to configure columns accept functions as parameters instead of hardcoded values:

```php
use App\Models\User;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('email')
    ->placeholder(fn (User $record): string => "No email for {$record->name}")

TextColumn::make('role')
    ->badge(fn (User $record): bool => $record->role === 'admin')

TextColumn::make('name')
    ->extraAttributes(fn (User $record): array => ['class' => "{$record->getKey()}-name-column"])
```

This alone unlocks many customization possibilities.

The package is also able to inject many utilities to use inside these functions, as parameters. All customization methods that accept functions as arguments can inject utilities.

These injected utilities require specific parameter names to be used. Otherwise, Filament doesn't know what to inject.

### Injecting the current state of the column

If you wish to access the current [value (state)](#column-content-state) of the column, define a `$state` parameter:

```php
function ($state) {
    // ...
}
```

### Injecting the current Eloquent record

You may retrieve the Eloquent record for the current schema using a `$record` parameter:

```php
use Illuminate\Database\Eloquent\Model;

function (?Model $record) {
    // ...
}
```

### Injecting the row loop

To access the [row loop](https://laravel.com/docs/blade#the-loop-variable) object for the current table row, define a `$rowLoop` parameter:

```php
function (stdClass $rowLoop) {
    // ...
}
```

### Injecting the current Livewire component instance

If you wish to access the current Livewire component instance, define a `$livewire` parameter:

```php
use Livewire\Component;

function (Component $livewire) {
    // ...
}
```

### Injecting the current column instance

If you wish to access the current component instance, define a `$component` parameter:

```php
use Filament\Tables\Columns\Column;

function (Column $component) {
    // ...
}
```

### Injecting the current table instance

If you wish to access the current table instance, define a `$table` parameter:

```php
use Filament\Tables\Table;

function (Table $table) {
    // ...
}
```

### Injecting multiple utilities

The parameters are injected dynamically using reflection, so you’re able to combine multiple parameters in any order:

```php
use App\Models\User;
use Livewire\Component as Livewire;

function (Livewire $livewire, mixed $state, User $record) {
    // ...
}
```

### Injecting dependencies from Laravel's container

You may inject anything from Laravel's container like normal, alongside utilities:

```php
use App\Models\User;
use Illuminate\Http\Request;

function (Request $request, User $record) {
    // ...
}
```

## Global settings

If you wish to change the default behavior of all columns globally, then you can call the static `configureUsing()` method inside a service provider's `boot()` method, to which you pass a Closure to modify the columns using. For example, if you wish to make all `TextColumn` columns [`toggleable()`](#toggling-column-visibility), you can do it like so:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::configureUsing(function (TextColumn $column): void {
    $column->toggleable();
});
```

Of course, you’re still able to overwrite this on each column individually:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('name')
    ->toggleable(false)
```
