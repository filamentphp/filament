---
title: Filter layout
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Positioning filters into grid columns

To change the number of columns that filters may occupy, you may use the `filtersFormColumns()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->filtersFormColumns(3);
}
```

## Controlling the width of the filters dropdown

To customize the dropdown width, you may use the `filtersFormWidth()` method, and specify a width - `ExtraSmall`, `Small`, `Medium`, `Large`, `ExtraLarge`, `TwoExtraLarge`, `ThreeExtraLarge`, `FourExtraLarge`, `FiveExtraLarge`, `SixExtraLarge` or `SevenExtraLarge`. By default, the width is `ExtraSmall`:

```php
use Filament\Support\Enums\Width;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->filtersFormWidth(Width::FourExtraLarge);
}
```

## Controlling the maximum height of the filters dropdown

To add a maximum height to the filters' dropdown content, so that they scroll, you may use the `filtersFormMaxHeight()` method, passing a [CSS length](https://developer.mozilla.org/en-US/docs/Web/CSS/length):

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->filtersFormMaxHeight('400px');
}
```

## Displaying filters in a modal

To render the filters in a modal instead of in a dropdown, you may use:

```php
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ], layout: FiltersLayout::Modal);
}
```

You may use the [trigger action API](overview#customizing-the-filters-trigger-action) to [customize the modal](../actions/modals), including [using a `slideOver()`](../actions/modals#using-a-slide-over-instead-of-a-modal).

## Displaying filters above the table content

To render the filters above the table content instead of in a dropdown, you may use:

```php
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ], layout: FiltersLayout::AboveContent);
}
```

<AutoScreenshot name="tables/filters/above-content" alt="Table with filters above content" version="4.x" />

### Allowing filters above the table content to be collapsed

To allow the filters above the table content to be collapsed, you may use:

```php
use Filament\Tables\Enums\FiltersLayout;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ], layout: FiltersLayout::AboveContentCollapsible);
}
```

## Displaying filters below the table content

To render the filters below the table content instead of in a dropdown, you may use:

```php
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ], layout: FiltersLayout::BelowContent);
}
```

<AutoScreenshot name="tables/filters/below-content" alt="Table with filters below content" version="4.x" />

## Hiding the filter indicators

To hide the active filters indicators above the table, you may use `hiddenFilterIndicators()`:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->hiddenFilterIndicators();
}
```

## Customizing the filter form schema

You may customize the [form schema](../../schemas/layouts) of the entire filter form at once, in order to rearrange filters into your desired layout, and use any of the [layout components](../../schemas/layout) available to forms. To do this, use the `filterFormSchema()` method, passing a closure function that receives the array of defined `$filters` that you can insert:

```php
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            Filter::make('is_featured'),
            Filter::make('published_at'),
            Filter::make('author'),
        ])
        ->filtersFormColumns(2)
        ->filtersFormSchema(fn (array $filters): array => [
            Section::make('Visibility')
                ->description('These filters affect the visibility of the records in the table.')
                ->schema([
                    $filters['is_featured'],
                    $filters['published_at'],
                ])
                    ->columns(2)
                ->columnSpanFull(),
            $filters['author'],
        ]);
}
```

In this example, we have put two of the filters inside a [section](../../schemas/sections) component, and used the `columns()` method to specify that the section should have two columns. We have also used the `columnSpanFull()` method to specify that the section should span the full width of the filter form, which is also 2 columns wide. We have inserted each filter into the form schema by using the filter's name as the key in the `$filters` array.
