---
title: Layout
---

## The problem with traditional table layouts

Traditional tables are notorious for having bad responsivity. On mobile, there is only so much flexibility you have when rendering content that is horizontally long:

- Allow the user to scroll horizontally to see more table content
- Hide non-important columns on smaller devices

Both of these are possible with Filament. Tables automatically scroll horizontally when they overflow anyway, and you may choose to show and hide columns based on the responsive [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) of the browser. To do this, you may use a `visibleFrom()` or `hiddenFrom()` method:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('slug')->visibleFrom('md')
```

This is fine, but there is still a glaring issue - **on mobile, the user is unable to see much information in a table row at once without scrolling**.

Thankfully, Filament lets you build responsive table-like interfaces, without touching HTML or CSS. These layouts let you define exactly where content appears in a table row, at each responsive breakpoint.

![Layout demo](https://user-images.githubusercontent.com/41773797/191832816-fe2d6e1b-a9b1-4133-b52a-d315705feb8f.png)

## Allowing columns to stack on mobile

Let's introduce a component - `Split`:

```php
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;

Split::make([
    ImageColumn::make('avatar'),
    TextColumn::make('name'),
    TextColumn::make('email'),
])
```

![Split layout](https://user-images.githubusercontent.com/41773797/191833019-efa1cede-3483-451a-8ee3-7f5405d5cb38.png)

A `Split` component is used to wrap around columns, and allow them to stack on mobile.

By default, columns within a split will appear aside each other all the time. However, you may choose a responsive [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) where this behaviour starts `from()`. Before this point, the columns will stack on top of each other:

```php
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

Split::make([
    ImageColumn::make('avatar'),
    TextColumn::make('name'),
    TextColumn::make('email'),
])->from('md')
```

In this example, the columns will only appear horizontally aside each other from `md` [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) devices onwards:

![Split layout only on desktop](https://user-images.githubusercontent.com/41773797/191833132-0648cbc8-5d31-45ea-b978-be316ebcf73c.png)

### Preventing a column from creating whitespace

Splits, like table columns, will automatically adjust their whitespace to ensure that each column has proportionate separation. You can prevent this from happening, using `grow(false)`. In this example, we will make sure that the avatar image will sit tightly against the name column:

```php
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

Split::make([
    ImageColumn::make('avatar')->grow(false),
    TextColumn::make('name'),
    TextColumn::make('email'),
])
```

The other columns which are allowed to `grow()` will adjust to consume the newly-freed space:

![Image column not growing](https://user-images.githubusercontent.com/41773797/191833422-d34035b9-f99d-4711-9a1c-e073b013e5b4.png)

### Stacking within a split

Inside a split, you may stack multiple columns on top of each other vertically. This allows you to display more data inside fewer columns on desktop:

```php
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

Split::make([
    ImageColumn::make('avatar'),
    TextColumn::make('name'),
    Stack::make([
        TextColumn::make('email'),
        TextColumn::make('phone'),
    ]),
])
```

![Stack within a split](https://user-images.githubusercontent.com/41773797/191833594-3ab5cf7a-e8f3-4662-ab34-1f8ee50c20f4.png)

#### Hiding a stack on mobile

Similar to individual columns, you may choose to hide a stack based on the responsive [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) of the browser. To do this, you may use a `visibleFrom()` method:

```php
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

Split::make([
    ImageColumn::make('avatar'),
    TextColumn::make('name'),
    Stack::make([
        TextColumn::make('email'),
        TextColumn::make('phone'),
    ])->visibleFrom('md'),
])
```

![Stack hidden on mobile](https://user-images.githubusercontent.com/41773797/191833730-2b2faa7f-5678-4746-9b80-570ae59ad9f5.png)

#### Aligning stacked content

By default, columns within a stack are aligned to the left. You may choose to align columns within a stack to the `center` or `right`:

```php
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

Split::make([
    ImageColumn::make('avatar'),
    TextColumn::make('name'),
    Stack::make([
        TextColumn::make('email'),
        TextColumn::make('phone'),
    ])->alignment('right'),
])
```

![Stacked content aligned to the right](https://user-images.githubusercontent.com/41773797/191833849-4dc5972a-9f3b-466a-9c13-22ac93ec801c.png)

#### Spacing stacked content

By default, stacked content has no vertical padding between columns. To add some, you may use the `space()` method, which accepts either `1`, `2`, or `3`, corresponding to [Tailwind's spacing scale](https://tailwindcss.com/docs/space):

```php
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;

Stack::make([
    TextColumn::make('email'),
    TextColumn::make('phone'),
])->space(1)
```

## Controlling column width using a grid

Sometimes, using a `Split` creates inconsistent widths when columns contain lots of content. This is because it's powered by Flexbox internally and each row individually controls how much space is allocated to content.

Instead, you may use a `Grid` layout, which uses CSS Grid Layout to allow you to control column widths:

```php
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\TextColumn;

Grid::make([
    'lg' => 2,
])
    ->schema([
        TextColumn::make('email'),
        TextColumn::make('phone'),
    ])
```

These columns will always consume equal width within the grid, from the `lg` [breakpoint](https://tailwindcss.com/docs/responsive-design#overview).

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
        TextColumn::make('email'),
        TextColumn::make('phone'),
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
        TextColumn::make('email')
            ->columnSpan([
                '2xl' => 2,
            ]),
        TextColumn::make('phone'),
    ])
```

## Collapsible content

When you're using a column layout like split or stack, then you can also add collapsible content. This is very useful for when you don't want to display all data in the table at once, but still want it to be accessible to the user if they need to access it, without navigating away.

Split and stack components can be made `collapsible()`, but there is also a dedicated `Panel` component that provides a pre-styled background color and border radius, to separate the collapsible content from the rest:

```php
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

[
    Split::make([
        ImageColumn::make('avatar'),
        TextColumn::make('name'),
    ]),
    Panel::make([
        Stack::make([
            TextColumn::make('email'),
            TextColumn::make('phone'),
        ]),
    ])->collapsible(),
]
```

You can expand a panel by default using the `collapsed(false)` method:

```php
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\TextColumn;

Panel::make([
    Stack::make([
        TextColumn::make('email'),
        TextColumn::make('phone'),
    ]),
])->collapsed(false)
```

![Collapsible content](https://user-images.githubusercontent.com/41773797/191834045-e423afd4-1ad3-4636-8635-fe9453423555.png)

### Adding a collapse animation

If you're not using the table builder within the admin panel, you may find that there is no animation when collapsing or expanding the content. You can enable this by installing the [Alpine.js Collapse Plugin](https://alpinejs.dev/plugins/collapse):

```bash
npm install @alpinejs/collapse --save-dev
```

Finally, import `@alpinejs/collapse` as an Alpine.js plugin in your JavaScript file:

```js
import Alpine from 'alpinejs'
import Collapse from '@alpinejs/collapse'

Alpine.plugin(Collapse)
```

## Arranging records into a grid

Sometimes, you may find that your data fits into a grid format better than a list. Filament can handle that too!

Simply define a new `getTableContentGrid()` method on your Livewire component:

```php
protected function getTableContentGrid(): ?array
{
    return [
        'md' => 2,
        'xl' => 3,
    ];
}
```

Or if you're using admin panel resources or relation managers, you must define a `$table->contentGrid()` method:

```php
public static function table(Table $table): Table
{
    return $table
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

![Records in a grid](https://user-images.githubusercontent.com/41773797/191834232-df5a73bc-392b-4fff-a4ac-8486f4e76aaf.png)

## Custom HTML

You may add custom HTML to your table using a `View` component. It can even be `collapsible()`:

```php
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

[
    Split::make([
        ImageColumn::make('avatar'),
        TextColumn::make('name'),
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

### Embedding other components

You could even pass in columns or other layout components to the `components()` method:

```php
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

[
    Split::make([
        ImageColumn::make('avatar'),
        TextColumn::make('name'),
    ]),
    View::make('users.table.collapsible-row-content')
        ->components([
            TextColumn::make('email'),
        ])
        ->collapsible(),
]
```

Now, render the components in the Blade file:

```blade
<div class="px-4 py-3 bg-gray-100 rounded-lg">
    <x-tables::columns.layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
    />
</div>
```
