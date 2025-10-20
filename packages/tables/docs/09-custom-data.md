---
title: Custom data
---
import Aside from "@components/Aside.astro"

## Introduction

[Filament's table builder](overview/#introduction) was originally designed to render data directly from a SQL database using [Eloquent models](https://laravel.com/docs/eloquent) in a Laravel application. Each row in a Filament table corresponds to a row in the database, represented by an Eloquent model instance.

However, this setup isn't always possible or practical. You might need to display data that isn't stored in a database—or data that is stored, but not accessible via Eloquent.

In such cases, you can use custom data instead. Pass a function to the `records()` method of the table builder that returns an array of data. This function is called when the table renders, and the value it returns is used to populate the table.

```php
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->records(fn (): array => [
            1 => [
                'title' => 'First item',
                'slug' => 'first-item',
                'is_featured' => true,
            ],
            2 => [
                'title' => 'Second item',
                'slug' => 'second-item',
                'is_featured' => false,
            ],
            3 => [
                'title' => 'Third item',
                'slug' => 'third-item',
                'is_featured' => true,
            ],
        ])
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('slug'),
            IconColumn::make('is_featured')
                ->boolean(),
        ]);
}
```

<Aside variant="warning">
    The array keys `(e.g., 1, 2, 3)` represent the record IDs. Use unique and consistent keys to ensure proper diffing and state tracking. This helps prevent issues with record integrity during Livewire interactions and updates.
</Aside>

## Columns

[Columns](columns) in the table work similarly to how they do when using [Eloquent models](https://laravel.com/docs/eloquent), but with one key difference: instead of referring to a model attribute or relationship, the column name represents a key in the array returned by the `records()` function.

When working with the current record inside a column function, set the `$record` type to `array` instead of `Model`. For example, to define a column using the [`state()`](columns#setting-the-state-of-a-column) function, you could do the following:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('is_featured')
    ->state(function (array $record): string {
        return $record['is_featured'] ? 'Featured' : 'Not featured';
    })
```

### Sorting

Filament's built-in [sorting](columns#sorting) function uses SQL to sort data. When working with custom data, you'll need to handle sorting yourself.

To access the currently sorted column and direction, you can inject `$sortColumn` and `$sortDirection` into the `records()` function. These variables are `null` if no sorting is applied.

In the example below, a [collection](https://laravel.com/docs/collections#method-sortby) is used to sort the data by key. The collection is returned instead of an array, and Filament handles it the same way. However, using a collection is not required to use this feature.

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

public function table(Table $table): Table
{
    return $table
        ->records(
            fn (?string $sortColumn, ?string $sortDirection): Collection => collect([
                1 => ['title' => 'First item'],
                2 => ['title' => 'Second item'],
                3 => ['title' => 'Third item'],
            ])->when(
                filled($sortColumn),
                fn (Collection $data): Collection => $data->sortBy(
                    $sortColumn,
                    SORT_REGULAR,
                    $sortDirection === 'desc',
                ),
            )
        )
        ->columns([
            TextColumn::make('title')
                ->sortable(),
        ]);
}
```

<Aside variant="info">
    It might seem like Filament should sort the data for you, but in many cases, it's better to let your data source—like a custom query or API call—handle the sorting instead.
</Aside>

### Searching

Filament's built-in [searching](columns#searching) function uses SQL to search data. When working with custom data, you'll need to handle searching yourself.

To access the current search query, you can inject `$search` into the `records()` function. This variable is `null` if no search query is currently being used.

In the example below, a [collection](https://laravel.com/docs/collections#method-filter) is used to filter the data by the search query. The collection is returned instead of an array, and Filament handles it the same way. However, using a collection is not required to use this feature.

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

public function table(Table $table): Table
{
    return $table
        ->records(
            fn (?string $search): Collection => collect([
                1 => ['title' => 'First item'],
                2 => ['title' => 'Second item'],
                3 => ['title' => 'Third item'],
            ])->when(
                filled($search),
                fn (Collection $data): Collection => $data->filter(
                    fn (array $record): bool => str_contains(
                        Str::lower($record['title']),
                        Str::lower($search),
                    ),
                ),
            )
        )
        ->columns([
            TextColumn::make('title'),
        ])
        ->searchable();
}
```

In this example, specific columns like `title` do not need to be `searchable()` because the search logic is handled inside the `records()` function. However, if you want to enable the search field without enabling search for a specific column, you can use the `searchable()` method on the entire table.

<Aside variant="info">
    It might seem like Filament should search the data for you, but in many cases, it's better to let your data source—like a custom query or API call—handle the searching instead.
</Aside>

#### Searching individual columns

The [individual column searches](#searching-individually) feature provides a way to render a search field separately for each column, allowing more precise filtering. When using custom data, you need to implement this feature yourself.

Instead of injecting `$search` into the `records()` function, you can inject an array of `$columnSearches`, which contains the search queries for each column.

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

public function table(Table $table): Table
{
    return $table
        ->records(
            fn (array $columnSearches): Collection => collect([
                1 => ['title' => 'First item'],
                2 => ['title' => 'Second item'],
                3 => ['title' => 'Third item'],
            ])->when(
                filled($columnSearches['title'] ?? null),
                fn (Collection $data) => $data->filter(
                    fn (array $record): bool => str_contains(
                        Str::lower($record['title']),
                        Str::lower($columnSearches['title'])
                    ),
                ),
            )
        )
        ->columns([
            TextColumn::make('title')
                ->searchable(isIndividual: true),
        ]);
}
```

<Aside variant="info">
    It might seem like Filament should search the data for you, but in many cases, it's better to let your data source—like a custom query or API call—handle the searching instead.
</Aside>

## Filters

Filament also provides a way to filter data using [filters](filters). When working with custom data, you'll need to handle filtering yourself. 

Filament gives you access to an array of filter data by injecting `$filters` into the `records()` function. The array contains the names of the filters as keys and the values of the filter forms themselves.

In the example below, a [collection](https://laravel.com/docs/collections#method-where) is used to filter the data. The collection is returned instead of an array, and Filament handles it the same way. However, using a collection is not required to use this feature.

```php
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

public function table(Table $table): Table
{
    return $table
        ->records(fn (array $filters): Collection => collect([
            1 => [
                'title' => 'What is Filament?',
                'slug' => 'what-is-filament',
                'author' => 'Dan Harrin',
                'is_featured' => true,
                'creation_date' => '2021-01-01',
            ],
            2 => [
                'title' => 'Top 5 best features of Filament',
                'slug' => 'top-5-features',
                'author' => 'Ryan Chandler',
                'is_featured' => false,
                'creation_date' => '2021-03-01',
            ],
            3 => [
                'title' => 'Tips for building a great Filament plugin',
                'slug' => 'plugin-tips',
                'author' => 'Zep Fietje',
                'is_featured' => true,
                'creation_date' => '2023-06-01',
            ],
        ])
            ->when(
                $filters['is_featured']['isActive'] ?? false,
                fn (Collection $data): Collection => $data->where(
                    'is_featured', true
                ),
            )
            ->when(
                filled($author = $filters['author']['value'] ?? null),
                fn (Collection $data): Collection => $data->where(
                    'author', $author
                ),
            )
            ->when(
                filled($date = $filters['creation_date']['date'] ?? null),
                fn (Collection $data): Collection => $data->where(
                    'creation_date', $date
                ),
            )
        )
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('slug'),
            IconColumn::make('is_featured')
                ->boolean(),
            TextColumn::make('author'),
        ])
        ->filters([
            Filter::make('is_featured'),
            SelectFilter::make('author')
                ->options([
                    'Dan Harrin' => 'Dan Harrin',
                    'Ryan Chandler' => 'Ryan Chandler',
                    'Zep Fietje' => 'Zep Fietje',
                ]),
            Filter::make('creation_date')
                ->schema([
                    DatePicker::make('date'),
                ]),
        ]);
}
```

Filter values aren't directly accessible via `$filters['filterName']`. Instead, each filter contains one or more form fields, and those field names are used as keys within the filter's data array. For example:

- [Checkbox](filters/overview#introduction) or [Toggle filters](filters/overview#using-a-toggle-button-instead-of-a-checkbox) without a custom schema (e.g., featured) use `isActive` as the `key`: `$filters['featured']['isActive']`

- [Select filters](filters/select#introduction) (e.g., author) use `value`: `$filters['author']['value']`

- [Custom schema filters](filters/custom#custom-filter-schemas) (e.g., creation_date) use the actual form field names. If the field is named `date`, access it like this: `$filters['creation_date']['date']`

<Aside variant="info">
    It might seem like Filament should filter the data for you, but in many cases, it's better to let your data source—like a custom query or API call—handle the filtering instead.
</Aside>

## Pagination

Filament's built-in [pagination](overview#pagination) feature uses SQL to paginate the data. When working with custom data, you'll need to handle pagination yourself. 

The `$page` and `$recordsPerPage` arguments are injected into the `records()` function, and you can use them to paginate the data. A `LengthAwarePaginator` should be returned from the `records()` function, and Filament will handle the pagination links and other pagination features for you:

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

public function table(Table $table): Table
{
    return $table
        ->records(function (int $page, int $recordsPerPage): LengthAwarePaginator {
            $records = collect([
                1 => ['title' => 'What is Filament?'],
                2 => ['title' => 'Top 5 best features of Filament'],
                3 => ['title' => 'Tips for building a great Filament plugin'],
            ])->forPage($page, $recordsPerPage);

            return new LengthAwarePaginator(
                $records,
                total: 30, // Total number of records across all pages
                perPage: $recordsPerPage,
                currentPage: $page,
            );
        })
        ->columns([
            TextColumn::make('title'),
        ]);
}
```

In this example, the `forPage()` method is used to paginate the data. This probably isn't the most efficient way to paginate data from a query or API, but it is a simple way to demonstrate how to paginate data from a custom array.

<Aside variant="info">
    It might seem like Filament should paginate the data for you, but in many cases, it's better to let your data source—like a custom query or API call—handle the pagination instead.
</Aside>

## Actions

[Actions](actions) in the table work similarly to how they do when using [Eloquent models](https://laravel.com/docs/eloquent). The only difference is that the `$record` parameter in the action's callback function will be an `array` instead of a `Model`.

```php
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

public function table(Table $table): Table
{
    return $table
        ->records(fn (): Collection => collect([
            1 => [
                'title' => 'What is Filament?',
                'slug' => 'what-is-filament',
            ],
            2 => [
                'title' => 'Top 5 best features of Filament',
                'slug' => 'top-5-features',
            ],
            3 => [
                'title' => 'Tips for building a great Filament plugin',
                'slug' => 'plugin-tips',
            ],
        ]))
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('slug'),
        ])
        ->recordActions([
            Action::make('view')
                ->color('gray')
                ->icon(Heroicon::Eye)
                ->url(fn (array $record): string => route('posts.view', $record['slug'])),
        ]);
}
```

### Bulk actions

For actions that interact with a single record, the record is always present on the current table page, so the `records()` method can be used to fetch the data. However for bulk actions, records can be selected across pagination pages. If you would like to use a bulk action that selects records across pages, you need to give Filament a way to fetch records across pages, otherwise it will only return the records from the current page. The `resolveSelectedRecordsUsing()` method should accept a function which has a `$keys` parameter, and returns an array of record data:

```php
use Filament\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

public function table(Table $table): Table
{
    return $table
        ->records(function (): array {
            // ...
        })
        ->resolveSelectedRecordsUsing(function (array $keys): array {
            return Arr::only([
                1 => [
                    'title' => 'First item',
                    'slug' => 'first-item',
                    'is_featured' => true,
                ],
                2 => [
                    'title' => 'Second item',
                    'slug' => 'second-item',
                    'is_featured' => false,
                ],
                3 => [
                    'title' => 'Third item',
                    'slug' => 'third-item',
                    'is_featured' => true,
                ],
            ], $keys);
        })
        ->columns([
            // ...
        ])
        ->recordActions([
            BulkAction::make('feature')
                ->requiresConfirmation()
                ->action(function (Collection $records): void {
                    // Do something with the collection of `$records` data
                }),
        ]);
}
```

However, if your user uses the "Select All" button to select all records across pagination pages, Filament will internally switch to tracking *deselected* records instead of selected records. This is an efficient mechanism in significantly large datasets. You can inject two additional parameters into the `resolveSelectedRecordsUsing()` method to handle this case: `$isTrackingDeselectedKeys` and `$deselectedKeys`.

`$isTrackingDeselectedKeys` is a boolean that indicates whether the user is tracking deselected keys. If it's `true`, `$deselectedKeys` will contain the keys of the records that are currently deselected. You can use this information to filter out the deselected records from the array of records returned by the `resolveSelectedRecordsUsing()` method:

```php
use Filament\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

public function table(Table $table): Table
{
    return $table
        ->records(function (): array {
            // ...
        })
        ->resolveSelectedRecordsUsing(function (
            array $keys,
            bool $isTrackingDeselectedKeys,
            array $deselectedKeys
        ): array {
            $records = [
                1 => [
                    'title' => 'First item',
                    'slug' => 'first-item',
                    'is_featured' => true,
                ],
                2 => [
                    'title' => 'Second item',
                    'slug' => 'second-item',
                    'is_featured' => false,
                ],
                3 => [
                    'title' => 'Third item',
                    'slug' => 'third-item',
                    'is_featured' => true,
                ],
            ];
            
            if ($isTrackingDeselectedKeys) {
                return Arr::except(
                    $records,
                    $deselectedKeys,
                );
            }
            
            return Arr::only(
                $records,
                $keys,
            );
        })
        ->columns([
            // ...
        ])
        ->recordActions([
            BulkAction::make('feature')
                ->requiresConfirmation()
                ->action(function (Collection $records): void {
                    // Do something with the collection of `$records` data
                }),
        ]);
}
```

## Using an external API as a table data source

[Filament's table builder](overview/#introduction) allows you to populate tables with data fetched from any external source—not just [Eloquent models](https://laravel.com/docs/eloquent). This is particularly useful when you want to display data from a REST API or a third-party service.

### Fetching data from an external API

The example below demonstrates how to consume data from [DummyJSON](https://dummyjson.com), a free fake REST API for placeholder JSON, and display it in a [Filament table](overview/#introduction):

```php
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

public function table(Table $table): Table
{
    return $table
        ->records(fn (): array => Http::baseUrl('https://dummyjson.com')
            ->get('products')
            ->collect()
            ->get('products', [])
        )
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('category'),
            TextColumn::make('price')
                ->money(),
        ]);
}
```

`get('products')` makes a `GET` request to [`https://dummyjson.com/products`](https://dummyjson.com/products). The `collect()` method converts the JSON response into a [Laravel collection](https://laravel.com/docs/collections#main-content). Finally, `get('products', [])` retrieves the array of products from the response. If the key is missing, it safely returns an empty array.

<Aside variant="warning">
    This is a basic example for demonstration purposes only. It's the developer's responsibility to implement proper authentication, authorization, validation, error handling, rate limiting, and other best practices when working with APIs.
</Aside>

<Aside variant="info">
    DummyJSON returns 30 items by default. You can use the [limit and skip](#external-api-pagination) query parameters to paginate through all items or use [`limit=0`](https://dummyjson.com/docs/products#products-limit_skip) to get all items.
</Aside>

#### Setting the state of a column using API data

[Columns](#columns) map to the array keys returned by the `records()` function.

When working with the current record inside a column function, set the `$record` type to `array` instead of `Model`. For example, to define a column using the [`state()`](columns/overview#setting-the-state-of-a-column) function, you could do the following:

```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

TextColumn::make('category_brand')
    ->label('Category - Brand')
    ->state(function (array $record): string {
        $category = Str::headline($record['category']);
        $brand = Str::title($record['brand'] ?? 'Unknown');

        return "{$category} - {$brand}";
    })
```

<Aside variant="tip">
    You can use the [`formatStateUsing()`](columns/text#formatting) method to format the state of a text column without changing the state itself.
</Aside>

### External API sorting

You can enable [sorting](columns#sorting) in [columns](columns) even when using an external API as the data source. The example below demonstrates how to pass sorting parameters (`sort_column` and `sort_direction`) to the [DummyJSON](https://dummyjson.com/docs/products#products-sort) API and how they are handled by the API.

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

public function table(Table $table): Table
{
    return $table
        ->records(function (?string $sortColumn, ?string $sortDirection): array {
            $response = Http::baseUrl('https://dummyjson.com/')
                ->get('products', [
                    'sortBy' => $sortColumn,
                    'order' => $sortDirection,
                ]);

            return $response
                ->collect()
                ->get('products', []);
        })
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('category')
                ->sortable(),
            TextColumn::make('price')
                ->money(),
        ]);
}
```
`get('products')` makes a `GET` request to [`https://dummyjson.com/products`](https://dummyjson.com/products). The request includes two parameters: `sortBy`, which specifies the column to sort by (e.g., category), and `order`, which specifies the direction of the sort (e.g., asc or desc). The `collect()` method converts the JSON response into a [Laravel collection](https://laravel.com/docs/collections#main-content). Finally, `get('products', [])` retrieves the array of products from the response. If the key is missing, it safely returns an empty array.

<Aside variant="warning">
    This is a basic example for demonstration purposes only. It's the developer's responsibility to implement proper authentication, authorization, validation, error handling, rate limiting, and other best practices when working with APIs.
</Aside>

<Aside variant="info">
    DummyJSON returns 30 items by default. You can use the [limit and skip](#external-api-pagination) query parameters to paginate through all items or use [`limit=0`](https://dummyjson.com/docs/products#products-limit_skip) to get all items.
</Aside>

### External API searching

You can enable [searching](columns#searching) in [columns](columns) even when using an external API as the data source. The example below demonstrates how to pass the `search` parameter to the [DummyJSON](https://dummyjson.com/docs/products#products-search) API and how it is handled by the API.

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

public function table(Table $table): Table
{
    return $table
        ->records(function (?string $search): array {
            $response = Http::baseUrl('https://dummyjson.com/')
                ->get('products/search', [
                    'q' => $search,
                ]);

            return $response
                ->collect()
                ->get('products', []);
        })
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('category'),
            TextColumn::make('price')
                ->money(),
        ])
        ->searchable();
}
```

`get('products/search')` makes a `GET` request to [`https://dummyjson.com/products/search`](https://dummyjson.com/products/search). The request includes the `q` parameter, which is used to filter the results based on the `search` query. The `collect()` method converts the JSON response into a [Laravel collection](https://laravel.com/docs/collections#main-content). Finally, `get('products', [])` retrieves the array of products from the response. If the key is missing, it safely returns an empty array.

<Aside variant="warning">
    This is a basic example for demonstration purposes only. It's the developer's responsibility to implement proper authentication, authorization, validation, error handling, rate limiting, and other best practices when working with APIs.
</Aside>

<Aside variant="info">
    DummyJSON returns 30 items by default. You can use the [limit and skip](#external-api-pagination) query parameters to paginate through all items or use [`limit=0`](https://dummyjson.com/docs/products#products-limit_skip) to get all items.
</Aside>

### External API filtering

You can enable [filtering](filters) in your table even when using an external API as the data source. The example below demonstrates how to pass the `filter` parameter to the [DummyJSON](https://dummyjson.com/docs/products#products-search) API and how it is handled by the API.

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

public function table(Table $table): Table
{
    return $table
        ->records(function (array $filters): array {
            $category = $filters['category']['value'] ?? null;

            $endpoint = filled($category)
                ? "products/category/{$category}"
                : 'products';

            $response = Http::baseUrl('https://dummyjson.com/')
                ->get($endpoint);

            return $response
                ->collect()
                ->get('products', []);
        })
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('category'),
            TextColumn::make('price')
                ->money(),
        ])
        ->filters([
            SelectFilter::make('category')
                ->label('Category')
                ->options(fn (): Collection => Http::baseUrl('https://dummyjson.com/')
                    ->get('products/categories')
                    ->collect()
                    ->pluck('name', 'slug')
                ),
        ]);
}
```

If a category filter is selected, the request is made to `/products/category/{category}`; otherwise, it defaults to `/products`. The `get()` method sends a `GET` request to the appropriate endpoint. The `collect()` method converts the JSON response into a [Laravel collection](https://laravel.com/docs/collections#main-content). Finally, `get('products', [])` retrieves the array of products from the response. If the key is missing, it safely returns an empty array.

<Aside variant="warning">
    This is a basic example for demonstration purposes only. It's the developer's responsibility to implement proper authentication, authorization, validation, error handling, rate limiting, and other best practices when working with APIs.
</Aside>

<Aside variant="info">
    DummyJSON returns 30 items by default. You can use the [limit and skip](#external-api-pagination) query parameters to paginate through all items or use [`limit=0`](https://dummyjson.com/docs/products#products-limit_skip) to get all items.
</Aside>

### External API pagination

You can enable [pagination](overview#pagination) when using an external API as the table data source. Filament will pass the current page and the number of records per page to your `records()` function. The example below demonstrates how to construct a `LengthAwarePaginator` manually and fetch paginated data from the [DummyJSON](https://dummyjson.com/docs/products#products-limit_skip) API, which uses `limit` and `skip` parameters for pagination:

```php
public function table(Table $table): Table
{
    return $table
        ->records(function (int $page, int $recordsPerPage): LengthAwarePaginator {
            $skip = ($page - 1) * $recordsPerPage;

            $response = Http::baseUrl('https://dummyjson.com')
                ->get('products', [
                    'limit' => $recordsPerPage,
                    'skip' => $skip,
                ])
                ->collect();

            return new LengthAwarePaginator(
                items: $response['products'],
                total: $response['total'],
                perPage: $recordsPerPage,
                currentPage: $page
            );
        })
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('category'),
            TextColumn::make('price')
                ->money(),
        ]);
}
```

`$page` and `$recordsPerPage` are automatically injected by Filament based on the current pagination state.
The calculated `skip` value tells the API how many records to skip before returning results for the current page.
The response contains `products` (the paginated items) and `total` (the total number of available items).
These values are passed to a `LengthAwarePaginator`, which Filament uses to render pagination controls correctly.

<Aside variant="warning">
    This is a basic example for demonstration purposes only. It's the developer's responsibility to implement proper authentication, authorization, validation, error handling, rate limiting, and other best practices when working with APIs.
</Aside>

### External API actions

When using [actions](../actions/overview) in a table with an external API, the process is almost identical to working with [Eloquent models](https://laravel.com/docs/eloquent). The main difference is that the `$record` parameter in the action's callback function will be an `array` instead of a `Model` instance.

Filament provides a variety of [built-in actions](../actions/overview#available-actions) that you can use in your application. However, you are not limited to these. You can create [custom actions](../actions/overview#introduction) tailored to your application's needs.

The examples below demonstrate how to create and use actions with an external API using [DummyJSON](https://dummyjson.com) as a simulated API source.

#### External API create action example

The create action in this example provides a [modal form](../actions/modals#rendering-a-form-in-a-modal) that allows users to create a new product using an external API. When the form is submitted, a `POST` request is sent to the API to create the new product.

```php
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

public function table(Table $table): Table
{
    $baseUrl = 'https://dummyjson.com';

    return $table
        ->records(fn (): array => Http::baseUrl($baseUrl)
            ->get('products')
            ->collect()
            ->get('products', [])
        )
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('category'),
        ])
        ->headerActions([
            Action::make('create')
                ->modalHeading('Create product')
                ->schema([
                    TextInput::make('title')
                        ->required(),
                    Select::make('category')
                        ->options(fn (): Collection => Http::get("{$baseUrl}/products/categories")
                            ->collect()
                            ->pluck('name', 'slug')
                        )
                        ->required(),
                ])
                ->action(function (array $data) use ($baseUrl) {
                    $response = Http::post("{$baseUrl}/products/add", [
                        'title' => $data['title'],
                        'category' => $data['category'],
                    ]);

                    if ($response->failed()) {
                        Notification::make()
                            ->title('Product failed to create')
                            ->danger()
                            ->send();
                            
                        return;
                    }
                    
                    Notification::make()
                        ->title('Product created')
                        ->success()
                        ->send();
                }),
        ]);
}
```

- [`modalHeading()`](../actions/modals#customizing-the-modals-heading-description-and-submit-action-label) sets the title of the modal that appears when the action is triggered.
- [`schema()`](../actions/modals#rendering-a-schema-in-a-modal) defines the form fields displayed in the modal.
- `action()` defines the logic that will be executed when the user submits the form.

<Aside variant="warning">
    This is a basic example for demonstration purposes only. It's the developer's responsibility to implement proper authentication, authorization, validation, error handling, rate limiting, and other best practices when working with APIs.
</Aside>

<Aside variant="warning">
    [`DummyJSON`](https://dummyjson.com/docs/products#products-update) API will not add it into the server. It will simulate a `POST` request and will return the new created product with a new id.
</Aside>

If you don't need a modal, you can directly redirect users to a specified URL when they click the create action button. In this case, you can define a custom URL pointing to the product creation page:

```php
use Filament\Actions\Action;

Action::make('create')
    ->url(route('products.create'))
```

#### External API edit action example

The edit action in this example provides a [modal form](../actions/modals#rendering-a-form-in-a-modal) for editing product details fetched from an external API. Users can update fields such as the product title and category, and the changes will be sent to the external API using a `PUT` request.

```php
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

public function table(Table $table): Table
{
    $baseUrl = 'https://dummyjson.com';

    return $table
        ->records(fn (): array => Http::baseUrl($baseUrl)
            ->get('products')
            ->collect()
            ->get('products', [])
        )
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('category'),
        ])
        ->recordActions([
            Action::make('edit')
                ->icon(Heroicon::PencilSquare)
                ->modalHeading('Edit product')
                ->fillForm(fn (array $record) => $record)
                ->schema([
                    TextInput::make('title')
                        ->required(),
                    Select::make('category')
                        ->options(fn (): Collection => Http::get("{$baseUrl}/products/categories")
                            ->collect()
                            ->pluck('name', 'slug')
                        )
                        ->required(),
                ])
                ->action(function (array $data, array $record) use ($baseUrl) {
                    $response = Http::put("{$baseUrl}/products/{$record['id']}", [
                        'title' => $data['title'],
                        'category' => $data['category'],
                    ]);

                    if ($response->failed()) {
                        Notification::make()
                            ->title('Product failed to save')
                            ->danger()
                            ->send();
                            
                        return;
                    }
                    
                    Notification::make()
                        ->title('Product save')
                        ->success()
                        ->send();
                }),
        ]);
}
```

- `icon()` defines the icon shown for this action in the table.
- [`modalHeading()`](../actions/modals#customizing-the-modals-heading-description-and-submit-action-label) sets the title of the modal that appears when the action is triggered.
- [`fillForm()`](../actions/modals#filling-the-form-with-existing-data) automatically fills the form fields with the existing values of the selected record.
- [`schema()`](../actions/modals#rendering-a-schema-in-a-modal) defines the form fields displayed in the modal.
- `action()` defines the logic that will be executed when the user submits the form.

<Aside variant="warning">
    This is a basic example for demonstration purposes only. It's the developer's responsibility to implement proper authentication, authorization, validation, error handling, rate limiting, and other best practices when working with APIs.
</Aside>

<Aside variant="warning">
    [`DummyJSON`](https://dummyjson.com/docs/products#products-update) API will not update it into the server. It will simulate a `PUT`/`PATCH` request and will return updated product with modified data.
</Aside>

If you don't need a modal, you can directly redirect users to a specified URL when they click the action button. You can achieve this by defining a URL with a dynamic route that includes the `record` parameter:

```php
use Filament\Actions\Action;

Action::make('edit')
    ->url(fn (array $record): string => route('products.edit', ['product' => $record['id']]))
```

#### External API view action example

The view action in this example opens a [modal](../actions/modals) displaying detailed product information fetched from an external API. This allows you to build a user interface with various components such as [text entries](../infolists/text-entry) and [images](../infolists/image-entry).

```php
use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

public function table(Table $table): Table
{
    $baseUrl = 'https://dummyjson.com';

    return $table
        ->records(fn (): array => Http::baseUrl($baseUrl)
            ->get('products', [
                'select' => 'id,title,description,brand,category,thumbnail,price',
            ])
            ->collect()
            ->get('products', [])
        )
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('category'),
        ])
        ->recordActions([
            Action::make('view')
                ->color('gray')
                ->icon(Heroicon::Eye)
                ->modalHeading('View product')
                ->schema([
                    Section::make()
                        ->schema([
                            Flex::make([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('title'),
                                        TextEntry::make('category'),
                                        TextEntry::make('brand'),
                                        TextEntry::make('price')
                                            ->money(),
                                    ]),
                                ImageEntry::make('thumbnail')
                                    ->hiddenLabel()
                                    ->grow(false),
                            ])->from('md'),
                            TextEntry::make('description')
                                ->prose(),
                        ]),
                ])
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
        ]);
}
```

- `color()` sets the color of the action button.
- `icon()` defines the icon shown for this action in the table.
- [`modalHeading()`](../actions/modals#customizing-the-modals-heading-description-and-submit-action-label) sets the title of the modal that appears when the action is triggered.
- [`schema()`](../actions/modals#rendering-a-schema-in-a-modal) defines the form fields displayed in the modal.
- [`modalSubmitAction(false)`](../actions/modals#modifying-the-default-modal-footer-action-button) disables the submit button, making this a read-only view action.
- [`modalCancelActionLabel()`](../actions/modals#modifying-the-default-modal-footer-action-button) customizes the label for the close button.

<Aside variant="warning">
    This is a basic example for demonstration purposes only. It's the developer's responsibility to implement proper authentication, authorization, validation, error handling, rate limiting, and other best practices when working with APIs.
</Aside>

<Aside variant="info">
    The [`select`](https://dummyjson.com/docs/products#products-limit_skip) parameter is used to limit the fields returned by the API. This helps reduce payload size and improves performance when rendering the table.
</Aside>

If you don't need a modal, you can directly redirect users to a specified URL when they click the action button. You can achieve this by defining a URL with a dynamic route that includes the `record` parameter:

```php
use Filament\Actions\Action;

Action::make('view')
    ->url(fn (array $record): string => route('products.view', ['product' => $record['id']]))
```

#### External API delete action example

The delete action in this example allows users to delete a product fetched from an external API.

```php
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

public function table(Table $table): Table
{
    $baseUrl = 'https://dummyjson.com';

    return $table
        ->records(fn (): array => Http::baseUrl($baseUrl)
            ->get('products')
            ->collect()
            ->get('products', [])
        )
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('category'),
            TextColumn::make('price')
                ->money(),
        ])
        ->recordActions([
            Action::make('delete')
                ->color('danger')
                ->icon(Heroicon::Trash)
                ->modalIcon(Heroicon::OutlinedTrash)
                ->modalHeading('Delete Product')
                ->requiresConfirmation()
                ->action(function (array $record) use ($baseUrl) {
                    $response = Http::baseUrl($baseUrl)
                        ->delete("products/{$record['id']}");

                    if ($response->failed()) {
                        Notification::make()
                            ->title('Product failed to delete')
                            ->danger()
                            ->send();
                            
                        return;
                    }
                    
                    Notification::make()
                        ->title('Product deleted')
                        ->success()
                        ->send();
                }),
        ]);
}
```

- `color()` sets the color of the action button.
- `icon()` defines the icon shown for this action in the table.
- [`modalIcon()`](../actions/modals#adding-an-icon-inside-the-modal) sets the icon that will appear in the confirmation modal.
- [`modalHeading()`](../actions/modals#customizing-the-modals-heading-description-and-submit-action-label) sets the title of the modal that appears when the action is triggered.
- [`requiresConfirmation()`](../actions/modals#confirmation-modals) ensures that the user must confirm the deletion before it is executed.
- `action()` defines the logic that will be executed when the user confirms the submission.

<Aside variant="warning">
    This is a basic example for demonstration purposes only. It's the developer's responsibility to implement proper authentication, authorization, validation, error handling, rate limiting, and other best practices when working with APIs.
</Aside>

<Aside variant="warning">
    [`DummyJSON`](https://dummyjson.com/docs/products#products-update) API will not delete it into the server. It will simulate a `DELETE` request and will return deleted product with `isDeleted` and `deletedOn` keys.
</Aside>

### External API full example

This example demonstrates how to combine [sorting](#external-api-sorting), [search](#external-api-searching), [category filtering](#external-api-filtering), and [pagination](#external-api-pagination) when using an external API as the data source. The API used here is [DummyJSON](https://dummyjson.com), which supports these features individually but **does not allow combining all of them in a single request**. This is because each feature uses a different endpoint:

- [Search](#external-api-searching) is performed through the `/products/search` endpoint using the `q` parameter.
- [Category filtering](#external-api-filtering) uses the `/products/category/{category}` endpoint.
- [Sorting](#external-api-sorting) is handled by sending `sortBy` and `order` parameters to the `/products` endpoint.

The only feature that can be combined with each of the above is [pagination](#external-api-pagination), since the `limit` and `skip` parameters are supported across all three endpoints.

```php
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

public function table(Table $table): Table
{
    $baseUrl = 'https://dummyjson.com/';

    return $table
        ->records(function (
            ?string $sortColumn,
            ?string $sortDirection,
            ?string $search,
            array $filters,
            int $page,
            int $recordsPerPage
        ) use ($baseUrl): LengthAwarePaginator {
            // Get the selected category from filters (if any)
            $category = $filters['category']['value'] ?? null;

            // Choose endpoint depending on search or filter
            $endpoint = match (true) {
                filled($search) => 'products/search',
                filled($category) => "products/category/{$category}",
                default => 'products',
            };

            // Determine skip offset
            $skip = ($page - 1) * $recordsPerPage;

            // Base query parameters for all requests
            $params = [
                'limit' => $recordsPerPage,
                'skip' => $skip,
                'select' => 'id,title,brand,category,thumbnail,price,sku,stock',
            ];

            // Add search query if applicable
            if (filled($search)) {
                $params['q'] = $search;
            }

            // Add sorting parameters
            if ($endpoint === 'products' && $sortColumn) {
                $params['sortBy'] = $sortColumn;
                $params['order'] = $sortDirection ?? 'asc';
            }

            $response = Http::baseUrl($baseUrl)
                ->get($endpoint, $params)
                ->collect();

            return new LengthAwarePaginator(
                items: $response['products'],
                total: $response['total'],
                perPage: $recordsPerPage,
                currentPage: $page
            );
        })
        ->columns([
            ImageColumn::make('thumbnail')
                ->label('Image'),
            TextColumn::make('title')
                ->sortable(),
            TextColumn::make('brand')
                ->state(fn (array $record): string => Str::title($record['brand'] ?? 'Unknown')),
            TextColumn::make('category')
                ->formatStateUsing(fn (string $state): string => Str::headline($state)),
            TextColumn::make('price')
                ->money(),
            TextColumn::make('sku')
                ->label('SKU'),
            TextColumn::make('stock')
                ->label('Stock')
                ->sortable(),
        ])
        ->filters([
            SelectFilter::make('category')
                ->label('Category')
                ->options(fn (): Collection => Http::baseUrl($baseUrl)
                    ->get('products/categories')
                    ->collect()
                    ->pluck('name', 'slug')
                ),
        ])
        ->searchable();
}
```

<Aside variant="warning">
    This is a basic example for demonstration purposes only. It's the developer's responsibility to implement proper authentication, authorization, validation, error handling, rate limiting, and other best practices when working with APIs.
</Aside>

<Aside variant="warning">
    The [DummyJSON](https://dummyjson.com) API does not support combining sorting, search, and category filtering in a single request.
</Aside>

<Aside variant="info">
    The [`select`](https://dummyjson.com/docs/products#products-limit_skip) parameter is used to limit the fields returned by the API. This helps reduce payload size and improves performance when rendering the table.
</Aside>
