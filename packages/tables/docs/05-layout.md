---
title: Layout
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## The problem with traditional table layouts

Traditional tables are notorious for having bad responsiveness. On mobile, there is only so much flexibility you have when rendering content that is horizontally long:

- Allow the user to scroll horizontally to see more table content
- Hide non-important columns on smaller devices

Both of these are possible with Filament. Tables automatically scroll horizontally when they overflow anyway, and you may choose to show and hide columns based on the responsive [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) of the browser. To do this, you may use a `visibleFrom()` or `hiddenFrom()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('slug')
    ->visibleFrom('md')
```

This is fine, but there is still a glaring issue - **on mobile, the user is unable to see much information in a table row at once without scrolling**.

Filament offers two solutions to this problem:

1. **Simple stacking** - Use `stackedOnMobile()` to automatically stack all cells vertically on mobile without changing your column definitions
2. **Custom layouts** - Use `Split`, `Stack`, and other layout components for fine-grained control over how content appears at each breakpoint

## Stacking table cells on mobile

The simplest way to make your table responsive is to use the `stackedOnMobile()` method on the table. This automatically converts the traditional horizontal table layout into a vertical card-like layout on mobile screens, while preserving the standard table appearance on larger screens:

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')
                ->searchable()
                ->sortable(),
            TextColumn::make('email'),
            TextColumn::make('phone'),
            TextColumn::make('job'),
        ])
        ->stackedOnMobile();
}
```

<AutoScreenshot name="tables/layout/stacked-on-mobile" alt="Table with stacked mobile layout on desktop" version="5.x" />

<AutoScreenshot name="tables/layout/stacked-on-mobile/mobile" alt="Table with stacked mobile layout on mobile" version="5.x" />

On mobile, each row displays as a card with the column label above its value. If you have sortable columns, a sort dropdown appears at the top of the table on mobile, allowing users to sort without the traditional header row. Bulk selection is also supported, with a checkbox appearing in the header area.

This approach works well when you want a quick responsive solution without restructuring your columns. However, for more complex layouts or fine-grained control over how content appears at different breakpoints, you may prefer to use the layout components described below.

## Custom column layouts

Filament lets you build responsive table-like interfaces, without touching HTML or CSS. These layouts let you define exactly where content appears in a table row, at each responsive breakpoint.

<AutoScreenshot name="tables/layout/demo" alt="Table with responsive layout" version="5.x" />

<AutoScreenshot name="tables/layout/demo/mobile" alt="Table with responsive layout on mobile" version="5.x" />

### Allowing columns to stack on mobile

Let's introduce a component - `Split`:

```php
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;

Split::make([
    ImageColumn::make('avatar')
        ->circular(),
    TextColumn::make('name')
        ->weight(FontWeight::Bold)
        ->searchable()
        ->sortable(),
    TextColumn::make('email'),
])
```

<AutoScreenshot name="tables/layout/split" alt="Table with a split layout" version="5.x" />

<AutoScreenshot name="tables/layout/split/mobile" alt="Table with a split layout on mobile" version="5.x" />

A `Split` component is used to wrap around columns, and allow them to stack on mobile.

By default, columns within a split will appear aside each other all the time. However, you may choose a responsive [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) where this behavior starts `from()`. Before this point, the columns will stack on top of each other:

```php
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

Split::make([
    ImageColumn::make('avatar')
        ->circular(),
    TextColumn::make('name')
        ->weight(FontWeight::Bold)
        ->searchable()
        ->sortable(),
    TextColumn::make('email'),
])->from('md')
```

In this example, the columns will only appear horizontally aside each other from `md` [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) devices onwards:

<AutoScreenshot name="tables/layout/split-desktop" alt="Table with a split layout on desktop" version="5.x" />

<AutoScreenshot name="tables/layout/split-desktop/mobile" alt="Table with a stacked layout on mobile" version="5.x" />

### Preventing a column from creating whitespace

Splits, like table columns, will automatically adjust their whitespace to ensure that each column has proportionate separation. You can prevent this from happening, using `grow(false)`. In this example, we will make sure that the avatar image will sit tightly against the name column:

```php
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

Split::make([
    ImageColumn::make('avatar')
        ->circular()
        ->grow(false),
    TextColumn::make('name')
        ->weight(FontWeight::Bold)
        ->searchable()
        ->sortable(),
    TextColumn::make('email'),
])
```

The other columns which are allowed to `grow()` will adjust to consume the newly-freed space:

<AutoScreenshot name="tables/layout/grow-disabled" alt="Table with a column that doesn't grow" version="5.x" />

<AutoScreenshot name="tables/layout/grow-disabled/mobile" alt="Table with a column that doesn't grow on mobile" version="5.x" />

### Stacking within a split

Inside a split, you may stack multiple columns on top of each other vertically. This allows you to display more data inside fewer columns on desktop:

```php
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

Split::make([
    ImageColumn::make('avatar')
        ->circular(),
    TextColumn::make('name')
        ->weight(FontWeight::Bold)
        ->searchable()
        ->sortable(),
    Stack::make([
        TextColumn::make('phone')
            ->icon('heroicon-m-phone'),
        TextColumn::make('email')
            ->icon('heroicon-m-envelope'),
    ]),
])
```

<AutoScreenshot name="tables/layout/stack" alt="Table with a stack" version="5.x" />

<AutoScreenshot name="tables/layout/stack/mobile" alt="Table with a stack on mobile" version="5.x" />

#### Hiding a stack on mobile

Similar to individual columns, you may choose to hide a stack based on the responsive [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) of the browser. To do this, you may use a `visibleFrom()` method:

```php
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

Split::make([
    ImageColumn::make('avatar')
        ->circular(),
    TextColumn::make('name')
        ->weight(FontWeight::Bold)
        ->searchable()
        ->sortable(),
    Stack::make([
        TextColumn::make('phone')
            ->icon('heroicon-m-phone'),
        TextColumn::make('email')
            ->icon('heroicon-m-envelope'),
    ])->visibleFrom('md'),
])
```

<AutoScreenshot name="tables/layout/stack-hidden-on-mobile" alt="Table with a stack" version="5.x" />

<AutoScreenshot name="tables/layout/stack-hidden-on-mobile/mobile" alt="Table with no stack on mobile" version="5.x" />

#### Aligning stacked content

By default, columns within a stack are aligned to the start. You may choose to align columns within a stack to the `Alignment::Center` or `Alignment::End`:

```php
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

Split::make([
    ImageColumn::make('avatar')
        ->circular(),
    TextColumn::make('name')
        ->weight(FontWeight::Bold)
        ->searchable()
        ->sortable(),
    Stack::make([
        TextColumn::make('phone')
            ->icon('heroicon-m-phone')
            ->grow(false),
        TextColumn::make('email')
            ->icon('heroicon-m-envelope')
            ->grow(false),
    ])
        ->alignment(Alignment::End)
        ->visibleFrom('md'),
])
```

Ensure that the columns within the stack have `grow(false)` set, otherwise they will stretch to fill the entire width of the stack and follow their own alignment configuration instead of the stack's.

<AutoScreenshot name="tables/layout/stack-aligned-right" alt="Table with a stack aligned right" version="5.x" />

#### Spacing stacked content

By default, stacked content has no vertical padding between columns. To add some, you may use the `space()` method, which accepts either `1`, `2`, or `3`, corresponding to [Tailwind's spacing scale](https://tailwindcss.com/docs/space):

```php
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;

Stack::make([
    TextColumn::make('phone')
        ->icon('heroicon-m-phone'),
    TextColumn::make('email')
        ->icon('heroicon-m-envelope'),
])->space(1)
```

<AutoScreenshot name="tables/layout/stack-spaced" alt="Table with spaced stacked content" version="5.x" />

### Controlling column width using a grid

Sometimes, using a `Split` creates inconsistent widths when columns contain lots of content. This is because it's powered by Flexbox internally and each row individually controls how much space is allocated to content.

Instead, you may use a `Grid` layout, which uses CSS Grid Layout to allow you to control column widths:

```php
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\TextColumn;

Grid::make([
    'lg' => 2,
])
    ->schema([
        TextColumn::make('phone')
            ->icon('heroicon-m-phone'),
        TextColumn::make('email')
            ->icon('heroicon-m-envelope'),
    ])
```

These columns will always consume equal width within the grid, from the `lg` [breakpoint](https://tailwindcss.com/docs/responsive-design#overview).

<AutoScreenshot name="tables/layout/column-grid" alt="Table with a grid column layout" version="5.x" />

You may choose to customize the number of columns within the grid at other breakpoints:

```php
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;

Grid::make([
    'lg' => 2,
    '2xl' => 4,
])
    ->schema([
        Stack::make([
            TextColumn::make('name'),
            TextColumn::make('job'),
        ]),
        TextColumn::make('phone')
            ->icon('heroicon-m-phone'),
        TextColumn::make('email')
            ->icon('heroicon-m-envelope'),
    ])
```

And you can even control how many grid columns will be consumed by each component at each [breakpoint](https://tailwindcss.com/docs/responsive-design#overview):

```php
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;

Grid::make([
    'lg' => 2,
    '2xl' => 5,
])
    ->schema([
        Stack::make([
            TextColumn::make('name'),
            TextColumn::make('job'),
        ])->columnSpan([
            'lg' => 'full',
            '2xl' => 2,
        ]),
        TextColumn::make('phone')
            ->icon('heroicon-m-phone')
            ->columnSpan([
                '2xl' => 2,
            ]),
        TextColumn::make('email')
            ->icon('heroicon-m-envelope'),
    ])
```

### Collapsible content

When you're using a column layout like split or stack, then you can also add collapsible content. This is very useful for when you don't want to display all data in the table at once, but still want it to be accessible to the user if they need to access it, without navigating away.

Split and stack components can be made `collapsible()`, but there is also a dedicated `Panel` component that provides a pre-styled background color and border radius, to separate the collapsible content from the rest:

```php
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

[
    Split::make([
        ImageColumn::make('avatar')
            ->circular(),
        TextColumn::make('name')
            ->weight(FontWeight::Bold)
            ->searchable()
            ->sortable(),
    ]),
    Panel::make([
        Stack::make([
            TextColumn::make('phone')
                ->icon('heroicon-m-phone'),
            TextColumn::make('email')
                ->icon('heroicon-m-envelope'),
        ]),
    ])->collapsible(),
]
```

You can expand a panel by default using the `collapsed(false)` method:

```php
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;

Panel::make([
    Split::make([
        TextColumn::make('phone')
            ->icon('heroicon-m-phone'),
        TextColumn::make('email')
            ->icon('heroicon-m-envelope'),
    ])->from('md'),
])->collapsed(false)
```

<AutoScreenshot name="tables/layout/collapsible" alt="Table with collapsible content" version="5.x" />

<AutoScreenshot name="tables/layout/collapsible/mobile" alt="Table with collapsible content on mobile" version="5.x" />

### Arranging records into a grid

Sometimes, you may find that your data fits into a grid format better than a list. Filament can handle that too!

Simply use the `$table->contentGrid()` method:

```php
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            Stack::make([
                // Columns
            ]),
        ])
        ->contentGrid([
            'md' => 2,
            'xl' => 3,
        ]);
}
```

In this example, the rows will be displayed in a grid:

- On mobile, they will be displayed in 1 column only.
- From the `md` [breakpoint](https://tailwindcss.com/docs/responsive-design#overview), they will be displayed in 2 columns.
- From the `xl` [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) onwards, they will be displayed in 3 columns.

These settings are fully customizable, any [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) from `sm` to `2xl` can contain `1` to `12` columns.

<AutoScreenshot name="tables/layout/grid" alt="Table with grid layout" version="5.x" />

<AutoScreenshot name="tables/layout/grid/mobile" alt="Table with grid layout on mobile" version="5.x" />

### Custom HTML

You may add custom HTML to your table using a `View` component. It can even be `collapsible()`:

```php
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

[
    Split::make([
        ImageColumn::make('avatar')
            ->circular(),
        TextColumn::make('name')
            ->weight(FontWeight::Bold)
            ->searchable()
            ->sortable(),
    ]),
    View::make('users.table.collapsible-row-content')
        ->collapsible(),
]
```

Now, create a `/resources/views/users/table/collapsible-row-content.blade.php` file, and add in your HTML. You can access the table record using `$getRecord()`:

```blade
<p class="px-4 py-3 bg-gray-100 rounded-lg">
    <span class="font-medium">
        Email address:
    </span>

    <span>
        {{ $getRecord()->email }}
    </span>
</p>
```

#### Embedding other components

You could even pass in columns or other layout components to the `components()` method:

```php
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

[
    Split::make([
        ImageColumn::make('avatar')
            ->circular(),
        TextColumn::make('name')
            ->weight(FontWeight::Bold)
            ->searchable()
            ->sortable(),
    ]),
    View::make('users.table.collapsible-row-content')
        ->components([
            TextColumn::make('email')
                ->icon('heroicon-m-envelope'),
        ])
        ->collapsible(),
]
```

Now, render the components in the Blade file:

```blade
<div class="px-4 py-3 bg-gray-100 rounded-lg">
    @foreach ($getComponents() as $layoutComponent)
        {{ $layoutComponent
            ->record($getRecord())
            ->recordKey($getRecordKey())
            ->rowLoop($getRowLoop())
            ->renderInLayout() }}
    @endforeach
</div>
```

## Rearranging the table layout

By default, Filament renders the header, the toolbar, the records, the empty state and the pagination of a table in a fixed order inside a card. If you need a different arrangement, such as the sort and search controls next to each other above a grid of records, pass a schema to the `layout()` method. The schema is built from table "parts", each rendering one piece of the table, and you may combine them with any [schema layout component](../schemas/layouts):

```php
use Filament\Schemas\Schema;
use Filament\Tables\Components\TableContent;
use Filament\Tables\Components\TableEmptyState;
use Filament\Tables\Components\TableFilterIndicators;
use Filament\Tables\Components\TableFiltersTrigger;
use Filament\Tables\Components\TablePagination;
use Filament\Tables\Components\TableSearch;
use Filament\Tables\Components\TableSelectionIndicator;
use Filament\Tables\Components\TableSortingSettings;
use Filament\Tables\Components\TableToolbar;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->contentGrid(['md' => 2, 'xl' => 3])
        ->layout(fn (Schema $schema): Schema => $schema->components([
            TableToolbar::make([
                TableSortingSettings::make(),
                TableSearch::make(),
                TableFiltersTrigger::make(),
            ]),
            TableSelectionIndicator::make(),
            TableFilterIndicators::make(),
            TableContent::make(),
            TableEmptyState::make(),
            TablePagination::make(),
        ]));
}
```

Each part renders exactly what the default table renders for that feature, including its borders and spacing, and hides itself when the feature is not configured. For example, `TableSearch` renders nothing when no column is `searchable()`, and `TablePagination` renders nothing when the table is not `paginated()`.

<Aside variant="info">
    Parts may only be used inside `layout()`. They read their configuration from the table of the Livewire component that renders them, so placing one in another schema throws an exception.
</Aside>

### Removing the card around the table

The default table is wrapped in a card with a border and a background. When the parts are arranged inside your own containers, you may remove that card with `contained(false)`. This example moves the filters form into a section next to the records:

```php
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Components\TableColumnManager;
use Filament\Tables\Components\TableContent;
use Filament\Tables\Components\TableEmptyState;
use Filament\Tables\Components\TableFilterIndicators;
use Filament\Tables\Components\TableFilters;
use Filament\Tables\Components\TableGroup;
use Filament\Tables\Components\TablePagination;
use Filament\Tables\Components\TableSearch;
use Filament\Tables\Components\TableSelectionIndicator;
use Filament\Tables\Components\TableToolbar;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->contained(false)
        ->filtersLayout(FiltersLayout::Hidden)
        ->layout(fn (Schema $schema): Schema => $schema->components([
            Grid::make(['lg' => 3])
                ->schema([
                    TableGroup::make([
                        TableToolbar::make([
                            TableSearch::make(),
                            TableColumnManager::make(),
                        ]),
                        TableSelectionIndicator::make(),
                        TableFilterIndicators::make(),
                        TableContent::make(),
                        TableEmptyState::make(),
                        TablePagination::make(),
                    ])
                        ->extraAttributes(['class' => 'fi-ta-ctn'])
                        ->columnSpan(['lg' => 2]),
                    Section::make('Filters')
                        ->schema([
                            TableFilters::make(),
                        ]),
                ]),
        ]));
}
```

`TableGroup` is a plain `<div>` without any styling of its own. Here it receives the `fi-ta-ctn` class so the records keep their card while the filters live in a section. `FiltersLayout::Hidden` keeps the filters trigger button out of the toolbar, since the form is always visible.

### Available parts

All parts live in the `Filament\Tables\Components` namespace:

- `TableHeader` - the heading, description and header actions.
- `TableToolbar` - a toolbar row. Without arguments it holds the default items in their default order; pass an array of parts to choose your own.
- `TableReorderTrigger` - the button that starts and stops reordering records.
- `TableToolbarActions` - the toolbar and bulk actions.
- `TableGroupingSettings` - the group and direction selects.
- `TableSortingSettings` - the column and direction selects, used by tables with a `contentGrid()` or a custom column layout, since row tables sort through their header cells.
- `TableSearch` - the global search field.
- `TableFiltersTrigger` - the filters button, including the dropdown or modal for those layouts.
- `TableColumnManager` - the column manager button and its dropdown or modal.
- `TableFilters` - the filters form. Use `collapsible()` to render a trigger that toggles the form.
- `TableSelectionIndicator` - the "records selected" bar with the select all and deselect all links, and the indicator shown while reordering.
- `TableFilterIndicators` - the active filter badges and the "remove all" action.
- `TableContent` - the records, including the summaries and the loading state.
- `TableEmptyState` - the empty state.
- `TablePagination` - the pagination links and the per page select.
- `TableGroup` - a plain `<div>` container for parts, with optional `extraAttributes()`.

### Rules for custom layouts

Filament throws a `LogicException` with instructions when a layout breaks one of these rules:

- The layout must contain a `TableContent` part, otherwise no records would be rendered.
- Each part may appear only once. The only exception is `TableFilters`, since the default layout places it in every position and renders the one matching the `filtersLayout()`.
- Parts must be rendered by a Livewire component that has a table. Placing a part in a form or infolist schema is not supported.

You may also call `$table->getDefaultLayout()` to get the default arrangement as a schema, which is useful when you want to make a small change to it.
