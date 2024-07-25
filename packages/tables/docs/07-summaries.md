---
title: Summaries
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

You may render a "summary" section below your table content. This is great for displaying the results of calculations such as averages, sums, counts, and ranges of the data in your table.

By default, there will be a single summary line for the current page of data, and an additional summary line for the totals for all data if multiple pages are available. You may also add summaries for [groups](grouping) of records, see ["Summarising groups of rows"](#summarising-groups-of-rows).

"Summarizer" objects can be added to any [table column](columns) using the `summarize()` method:

```php
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('rating')
    ->summarize(Average::make())
```

Multiple "summarizers" may be added to the same column:

```php
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('rating')
    ->numeric()
    ->summarize([
        Average::make(),
        Range::make(),
    ])
```

> The first column in a table may not use summarizers. That column is used to render the heading and subheading/s of the summary section.

<AutoScreenshot name="tables/summaries" alt="Table with summaries" version="3.x" />

## Available summarizers

Filament ships with four types of summarizer:

- [Average](#average)
- [Count](#count)
- [Range](#range)
- [Sum](#sum)

You may also [create your own custom summarizers](#custom-summaries) to display data in whatever way you wish.

## Average

Average can be used to calculate the average of all values in the dataset:

```php
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('rating')
    ->summarize(Average::make())
```

In this example, all ratings in the table will be added together and divided by the number of ratings.

## Count

Count can be used to find the total number of values in the dataset. Unless you just want to calculate the number of rows, you will probably want to [scope the dataset](#scoping-the-dataset) as well:

```php
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Count;
use Illuminate\Database\Query\Builder;

IconColumn::make('is_published')
    ->boolean()
    ->summarize(
        Count::make()->query(fn (Builder $query) => $query->where('is_published', true)),
    ),
```

In this example, the table will calculate how many posts are published.

### Counting the occurrence of icons

Using a count on an [icon column](columns/icon) allows you to use the `icons()` method, which gives the user a visual representation of how many of each icon are in the table:

```php
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Count;
use Illuminate\Database\Query\Builder;

IconColumn::make('is_published')
    ->boolean()
    ->summarize(Count::make()->icons()),
```

## Range

Range can be used to calculate the minimum and maximum value in the dataset:

```php
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('price')
    ->summarize(Range::make())
```

In this example, the minimum and maximum price in the table will be found.

### Date range

You may format the range as dates using the `minimalDateTimeDifference()` method:

```php
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('created_at')
    ->dateTime()
    ->summarize(Range::make()->minimalDateTimeDifference())
```

This method will present the minimal difference between the minimum and maximum date. For example:

- If the minimum and maximum dates are different days, only the dates will be displayed.
- If the minimum and maximum dates are on the same day at different times, both the date and time will be displayed.
- If the minimum and maximum dates and times are identical, they will only appear once.

### Text range

You may format the range as text using the `minimalTextualDifference()` method:

```php
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('sku')
    ->summarize(Range::make()->minimalTextualDifference())
```

This method will present the minimal difference between the minimum and maximum. For example:

- If the minimum and maximum start with different letters, only the first letters will be displayed.
- If the minimum and maximum start with the same letter, more of the text will be rendered until a difference is found.
- If the minimum and maximum are identical, they will only appear once.

### Including null values in the range

By default, we will exclude null values from the range. If you would like to include them, you may use the `excludeNull(false)` method:

```php
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('sku')
    ->summarize(Range::make()->excludeNull(false))
```

## Sum

Sum can be used to calculate the total of all values in the dataset:

```php
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('price')
    ->summarize(Sum::make())
```

In this example, all prices in the table will be added together.

## Setting a label

You may set a summarizer's label using the `label()` method:

```php
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('price')
    ->summarize(Sum::make()->label('Total'))
```

## Scoping the dataset

You may apply a database query scope to a summarizer's dataset using the `query()` method:

```php
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Query\Builder;

TextColumn::make('rating')
    ->summarize(
        Average::make()->query(fn (Builder $query) => $query->where('is_published', true)),
    ),
```

In this example, now only rows where `is_published` is set to `true` will be used to calculate the average.

This feature is especially useful with the [count](#count) summarizer, as it can count how many records in the dataset pass a test:

```php
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Count;
use Illuminate\Database\Query\Builder;

IconColumn::make('is_published')
    ->boolean()
    ->summarize(
        Count::make()->query(fn (Builder $query) => $query->where('is_published', true)),
    ),
```

In this example, the table will calculate how many posts are published.

## Formatting

### Number formatting

The `numeric()` method allows you to format an entry as a number:

```php
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('rating')
    ->summarize(Average::make()->numeric())
```

If you would like to customize the number of decimal places used to format the number with, you can use the `decimalPlaces` argument:

```php
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('rating')
    ->summarize(Average::make()->numeric(
        decimalPlaces: 0,
    ))
```

By default, your app's locale will be used to format the number suitably. If you would like to customize the locale used, you can pass it to the `locale` argument:

```php
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('rating')
    ->summarize(Average::make()->numeric(
        locale: 'nl',
    ))
```

Alternatively, you can set the default locale used across your app using the `Table::$defaultNumberLocale` method in the `boot()` method of a service provider:

```php
use Filament\Tables\Table;

Table::$defaultNumberLocale = 'nl';
```

### Currency formatting

The `money()` method allows you to easily format monetary values, in any currency:

```php
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('price')
    ->summarize(Sum::make()->money('EUR'))
```

There is also a `divideBy` argument for `money()` that allows you to divide the original value by a number before formatting it. This could be useful if your database stores the price in cents, for example:

```php
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('price')
    ->summarize(Sum::make()->money('EUR', divideBy: 100))
```

By default, your app's locale will be used to format the money suitably. If you would like to customize the locale used, you can pass it to the `locale` argument:

```php
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('price')
    ->summarize(Sum::make()->money('EUR', locale: 'nl'))
```

Alternatively, you can set the default locale used across your app using the `Table::$defaultNumberLocale` method in the `boot()` method of a service provider:

```php
use Filament\Tables\Table;

Table::$defaultNumberLocale = 'nl';
```

### Limiting text length

You may `limit()` the length of the summary's value:

```php
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('sku')
    ->summarize(Range::make()->limit(5))
```

### Adding a prefix or suffix

You may add a prefix or suffix to the summary's value:

```php
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;

TextColumn::make('volume')
    ->summarize(Sum::make()
        ->prefix('Total volume: ')
        ->suffix(new HtmlString(' m&sup3;'))
    )
```

## Custom summaries

You may create a custom summary by returning the value from the `using()` method:

```php
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Query\Builder;

TextColumn::make('name')
    ->summarize(Summarizer::make()
        ->label('First last name')
        ->using(fn (Builder $query): string => $query->min('last_name')))
```

The callback has access to the database `$query` builder instance to perform calculations with. It should return the value to display in the table.

## Conditionally hiding the summary

To hide a summary, you may pass a boolean, or a function that returns a boolean, to the `hidden()` method. If you need it, you can access the Eloquent query builder instance for that summarizer via the `$query` argument of the function:

```php
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

TextColumn::make('sku')
    ->summarize(Summarizer::make()
        ->hidden(fn (Builder $query): bool => ! $query->exists()))
```

Alternatively, you can use the `visible()` method to achieve the opposite effect:

```php
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

TextColumn::make('sku')
    ->summarize(Summarizer::make()
        ->visible(fn (Builder $query): bool => $query->exists()))
```

## Summarising groups of rows

You can use summaries with [groups](grouping) to display a summary of the records inside a group. This works automatically if you choose to add a summariser to a column in a grouped table.

### Hiding the grouped rows and showing the summary only

You may hide the rows inside groups and just show the summary of each group using the `groupsOnly()` method. This is beneficial in many reporting scenarios.

```php
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('views_count')
                ->summarize(Sum::make()),
            TextColumn::make('likes_count')
                ->summarize(Sum::make()),
        ])
        ->defaultGroup('category')
        ->groupsOnly();
}
```
