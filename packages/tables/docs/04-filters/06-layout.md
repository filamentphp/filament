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
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->filtersFormWidth(MaxWidth::FourExtraLarge);
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

You may use the [trigger action API](getting-started#customizing-the-filters-trigger-action) to [customize the modal](../actions/modals), including [using a `slideOver()`](../actions/modals#using-a-slide-over-instead-of-a-modal).

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

<AutoScreenshot name="tables/filters/above-content" alt="Table with filters above content" version="3.x" />

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

<AutoScreenshot name="tables/filters/below-content" alt="Table with filters below content" version="3.x" />
