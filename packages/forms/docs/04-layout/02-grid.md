---
title: Grid
---

## Columns

You may create multiple columns within each layout component using the `columns()` method:

```php
use Filament\Forms\Components\Card;

Card::make()->columns(2)
```

> For more information about creating advanced, responsive column layouts, please see the [grid section](grid). All column options in that section are also available in other layout components.

### Controlling field column span

You may specify the number of columns that any component may span in the parent grid:

```php
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;

Grid::make(3)
    ->schema([
        TextInput::make('name')
            ->columnSpan(2),
        // ...
    ])
```

You may use `columnSpan('full')` to ensure that a column spans the full width of the parent grid, however many columns it has:

```php
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;

Grid::make(3)
    ->schema([
        TextInput::make('name')
            ->columnSpan('full'),
        // ...
    ])
```

Instead, you can even define how many columns a component may consume at any breakpoint:

```php
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;

Grid::make([
    'default' => 1,
    'sm' => 3,
    'xl' => 6,
    '2xl' => 8,
])
    ->schema([
        TextInput::make('name')
            ->columnSpan([
                'sm' => 2,
                'xl' => 3,
                '2xl' => 4,
            ]),
        // ...
    ])
```

## Grid component

Generally, form fields are stacked on top of each other in one column. To change this, you may use a grid component:

```php
use Filament\Forms\Components\Grid;

Grid::make()
    ->schema([
        // ...
    ])
```

By default, grid components will create a two column grid for [the Tailwind `md` breakpoint](https://tailwindcss.com/docs/responsive-design#overview) and higher.

You may pass a different number of columns to the grid's `md` breakpoint:

```php
use Filament\Forms\Components\Grid;

Grid::make(3)
    ->schema([
        // ...
    ])
```

To customize the number of columns in any grid at different [breakpoints](https://tailwindcss.com/docs/responsive-design#overview), you may pass an array of breakpoints and columns:

```php
use Filament\Forms\Components\Grid;

Grid::make([
    'default' => 1,
    'sm' => 2,
    'md' => 3,
    'lg' => 4,
    'xl' => 6,
    '2xl' => 8,
])
    ->schema([
        // ...
    ])
```

Since Tailwind is mobile-first, if you leave out a breakpoint, it will fall back to the one set below it:

```php
use Filament\Forms\Components\Grid;

Grid::make([
    'sm' => 2,
    'xl' => 6,
])
    ->schema([
        // ...
    ])
```
