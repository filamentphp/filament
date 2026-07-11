---
title: Filter layout
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import Aside from "@components/Aside.astro"

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

<AutoScreenshot name="tables/filters/grid-columns" alt="Table with filters in grid columns" version="4.x" />

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

<AutoScreenshot name="tables/filters/modal" alt="Table with filters in a modal" version="4.x" />

You may use the [trigger action API](overview#customizing-the-filters-trigger-action) to [customize the modal](../../actions/modals), including [using a `slideOver()`](../../actions/modals#using-a-slide-over-instead-of-a-modal).

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

<AutoScreenshot name="tables/filters/above-content-collapsible" alt="Table with collapsible filters above content" version="4.x" />

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

## Displaying filters to the left or right of the table content

To render the filters to the left (before) or right (after) of the table content instead of in a dropdown, you may use:

```php
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ], layout: FiltersLayout::BeforeContent); // or `FiltersLayout::AfterContent`
}
```

<AutoScreenshot name="tables/filters/before-content" alt="Table with filters before content" version="4.x" />

<AutoScreenshot name="tables/filters/after-content" alt="Table with filters after content" version="4.x" />

### Allowing filters to be collapsible when displayed to the left or right of the table content

To allow the filters to be collapsible when displayed to the left or right of the table content, you may use:

```php
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ], layout: FiltersLayout::BeforeContentCollapsible); // or `FiltersLayout::AfterContentCollapsible`
}
```

## Placing filters in multiple locations at once

By default, every filter renders together in a single location (the dropdown, unless you change it with `filtersLayout()`). To split filters across several locations at the same time - for example, a status filter that is always visible above the table while the rest stay in the dropdown — pass `FilterPanel` objects to `filters()` instead of a flat array. Each panel takes a location and the filters that belong there:

```php
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\FilterPanel;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            FilterPanel::make(FiltersLayout::AboveContent, [
                SelectFilter::make('status'),
                SelectFilter::make('category'),
            ])->columns(2),

            FilterPanel::make(FiltersLayout::Dropdown, [
                SelectFilter::make('author'),
            ]),
        ]);
}
```

Here, the `status` and `category` filters render above the table in a two-column grid, while `author` stays behind the dropdown trigger.

<Aside variant="info">
    A table's `filters()` array must be **either** all `FilterPanel` objects **or** all filters — mixing the two throws an exception (including across `pushFilters()` calls).
</Aside>

### Configuring a panel

Each panel accepts the same presentation options as the table. Any option left unset on a panel falls through to the table-level `filtersForm*()` method, which itself falls back to a per-location default - so the table-level methods act as the defaults for every panel:

```php
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Filters\FilterPanel;

FilterPanel::make(FiltersLayout::Dropdown, [
    // ...
])
    ->columns(2)
    ->width(Width::Medium)
    ->maxHeight('400px')
    ->resetActionPosition(FiltersResetActionPosition::Footer)
    ->triggerAction(fn (Action $action): Action => $action->label('Filter'));
```

### Using the dropdown and a modal together

The `Dropdown` and `Modal` locations each render their own trigger, so a table with a panel in both shows two filter icons — one opening a dropdown, the other opening a modal:

```php
->filters([
    FilterPanel::make(FiltersLayout::Dropdown, [
        // ...
    ]),
    FilterPanel::make(FiltersLayout::Modal, [
        // ...
    ]),
])
```

### Resetting and applying filters across panels

Each panel has its own reset action that clears only the filters shown in that panel. To clear every filter at once, use the "remove all" button in the active filter indicators above the table. The apply button (when deferring filters) and the indicators are always global - filter state is a single value, so applying always submits every panel's filters.

### Merging panels that share a location

Declaring more than one panel for the same location - whether twice in the same `filters()` array, or by appending one later with `pushFilters()` (for example from a plugin) — **merges their filters into a single panel** for that location. The configuration of the **first** panel at that location wins; any configuration set on the later panels is ignored:

```php
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\FilterPanel;
use Filament\Tables\Filters\SelectFilter;

$table
    ->filters([
        FilterPanel::make(FiltersLayout::AboveContent, [
            SelectFilter::make('status'),
        ])->columns(2),

        // The same location again: its filters are merged into the panel above,
        // and its own configuration (such as `columns()`) is ignored.
        FilterPanel::make(FiltersLayout::AboveContent, [
            SelectFilter::make('category'),
        ]),
    ]);
```

This lets a plugin add filters to a shared location (such as the dropdown) without conflicting with the table's own panels.

<Aside variant="danger">
    A [custom filter form schema](#customizing-the-filter-form-schema) (`filtersFormSchema()`) cannot be combined with `FilterPanel` objects, as they are competing layout mechanisms. Use `filtersFormSchema()` with a flat `filters()` array only.
</Aside>

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

You may customize the [form schema](../../schemas/layouts) of the entire filter form at once, in order to rearrange filters into your desired layout, and use any of the [layout components](../../schemas/layouts) available to forms. To do this, use the `filterFormSchema()` method, passing a closure function that receives the array of defined `$filters` that you can insert:

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

<AutoScreenshot name="tables/filters/custom-form-schema" alt="Table with custom filter form schema" version="4.x" />

## Displaying the reset action in the footer

By default, the reset action appears in the header of the filters form. You may move it to the footer, next to the apply action, using the `filtersResetActionPosition()` method:

```php
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->filters([
            // ...
        ])
        ->filtersResetActionPosition(FiltersResetActionPosition::Footer);
}
```
