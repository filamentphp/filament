---
title: Layouts
---
import Aside from "@components/Aside.astro"
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Filament's grid system allows you to create responsive, multi-column layouts using any layout component. Filament provides a set of built-in layout components to help you build these:

- [Grid](#grid-component)
- [Flex](#flex-component)
- [Fieldset](#fieldset-component)
- [Section](sections)
- [Tabs](tabs)
- [Wizard](wizards)
- [Empty state](empty-states)

You may also [create your own custom layout components](custom-components#custom-layout-components) to display components however you wish.

## Grid system

All layout components have a `columns()` method that you can use in a couple of different ways:

- You can pass an integer like `columns(2)`. This integer is the number of columns used on the `lg` breakpoint and higher. All smaller devices will have just 1 column.
- You can pass an array, where the key is the breakpoint and the value is the number of columns. For example, `columns(['md' => 2, 'xl' => 4])` will create a 2 column layout on medium devices, and a 4 column layout on extra large devices. The default breakpoint for smaller devices uses 1 column, unless you use a `default` array key.

Breakpoints (`sm`, `md`, `lg`, `xl`, `2xl`) are defined by Tailwind, and can be found in the [Tailwind documentation](https://tailwindcss.com/docs/responsive-design#overview).

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `columns()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Grid column spans

In addition to specifying how many columns a layout component should have, you may also specify how many columns a component should fill within the parent grid, using the `columnSpan()` method. This method accepts an integer or an array of breakpoints and column spans:

- You can pass an integer like `columnSpan(2)`. This integer is the number of columns that are consumed on the `lg` breakpoint and higher. All smaller devices span just 1 column.
- `columnSpan(['md' => 2, 'xl' => 4])` will make the component fill up to 2 columns on medium devices, and up to 4 columns on extra large devices. The default breakpoint for smaller devices uses 1 column, unless you use a `default` array key.
- `columnSpan('full')` will make the component fill the full width of the parent grid on the `lg` breakpoint and higher, regardless of how many columns there are. All smaller devices span just 1 column.
- `columnSpanFull()` will make the component fill the full width of the parent grid on all devices, regardless of how many columns it has.

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `columnSpan()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Grid column starts

If you want to start a component in a grid at a specific column, you can use the `columnStart()` method. This method accepts an integer, or an array of breakpoints and which column the component should start at:

- You can pass an integer like `columnStart(2)`. This integer is column that the component will start on for the `lg` breakpoint and higher. All smaller devices start the component on the first column.
- `columnStart(['md' => 2, 'xl' => 4])` will make the component start at column 2 on medium devices, and at column 4 on extra large devices. The default breakpoint for smaller devices uses 1 column, unless you use a `default` array key.

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

Grid::make()
    ->columns([
        'sm' => 3,
        'xl' => 6,
        '2xl' => 8,
    ])
    ->schema([
        TextInput::make('name')
            ->columnStart([
                'sm' => 2,
                'xl' => 3,
                '2xl' => 4,
            ]),
        // ...
    ])
```

In this example, the grid has 3 columns on small devices, 6 columns on extra large devices, and 8 columns on extra extra large devices. The text input will start at column 2 on small devices, column 3 on extra large devices, and column 4 on extra extra large devices. This is essentially producing a layout whereby the text input always starts halfway through the grid, regardless of how many columns the grid has.

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `columnStart()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Grid column ordering

If you want to control the visual order of components in a grid without changing their position in the markup, you can use the `columnOrder()` method. This method accepts an integer, a closure, or an array of breakpoints and order values:

- You can pass an integer like `columnOrder(2)`. This integer is the order that the component will appear in for the `lg` breakpoint and higher. All smaller devices use the default order, unless you use a `default` array key.
- `columnOrder(['md' => 2, 'xl' => 4])` will set the component's order to 2 on medium devices, and to 4 on extra large devices. The default breakpoint for smaller devices uses the default order, unless you use a `default` array key.
- `columnOrder(fn () => 1)` will dynamically calculate the order using a closure.

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

Grid::make()
    ->columns(3)
    ->schema([
        TextInput::make('first')
            ->columnOrder(3), // This will appear last
        TextInput::make('second')
            ->columnOrder(1), // This will appear first
        TextInput::make('third')
            ->columnOrder(2), // This will appear second
    ])
```

You can also use responsive ordering to change the visual order of components based on the screen size:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

Grid::make()
    ->columns([
        'sm' => 2,
        'lg' => 3,
    ])
    ->schema([
        TextInput::make('title')
            ->columnOrder([
                'default' => 1,
                'lg' => 3,
            ]),
        TextInput::make('description')
            ->columnOrder([
                'default' => 2,
                'lg' => 1,
            ]),
        TextInput::make('category')
            ->columnOrder([
                'default' => 3,
                'lg' => 2,
            ]),
    ])
```

In this example, on small screens the order will be: title, description, category. On large screens, the order will be: description, category, title.

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `columnOrder()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### An example of a responsive grid layout

In this example, we have a schema with a [section](sections) layout component. Since all layout components support the `columns()` method, we can use it to create a responsive grid layout within the section itself.

We pass an array to `columns()` as we want to specify different numbers of columns for different breakpoints. On devices smaller than the `sm` [Tailwind breakpoint](https://tailwindcss.com/docs/responsive-design#overview), we want to have 1 column, which is default. On devices larger than the `sm` breakpoint, we want to have 3 columns. On devices larger than the `xl` breakpoint, we want to have 6 columns. On devices larger than the `2xl` breakpoint, we want to have 8 columns.

Inside the section, we have a [text input](../forms/text-input). Since text inputs are form fields and all components have a `columnSpan()` method, we can use it to specify how many columns the text input should fill. On devices smaller than the `sm` breakpoint, we want the text input to fill 1 column, which is default. On devices larger than the `sm` breakpoint, we want the text input to fill 2 columns. On devices larger than the `xl` breakpoint, we want the text input to fill 3 columns. On devices larger than the `2xl` breakpoint, we want the text input to fill 4 columns.

Additionally, we're using the `columnOrder()` method to control the visual order of components in the grid based on screen size. This allows us to change the display order without altering the markup structure.

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

Section::make()
    ->columns([
        'sm' => 3,
        'xl' => 6,
        '2xl' => 8,
    ])
    ->schema([
        TextInput::make('name')
            ->columnSpan([
                'default' => 1,
                'sm' => 2,
                'xl' => 3,
                '2xl' => 4,
            ])
            ->columnOrder([
                'default' => 2,
                'xl' => 1,
            ]),
        TextInput::make('email')
            ->columnSpan([
                'default' => 1,
                'xl' => 2,
            ])
            ->columnOrder([
                'default' => 1,
                'xl' => 2,
            ]),
        // ...
    ])
```

In this example, on screens smaller than the `xl` breakpoint, the email field will appear first followed by the name field. On screens larger than the `xl` breakpoint, the order is reversed with the name field appearing first followed by the email field.

## Basic layout components

### Grid component

All layout components support the `columns()` method, but you also have access to an additional `Grid` component. If you feel that your schema would benefit from an explicit grid syntax with no extra styling, it may be useful to you. Instead of using the `columns()` method, you can pass your column configuration directly to `Grid::make()`:

```php
use Filament\Schemas\Components\Grid;

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

### Flex component

The `Flex` component allows you to define layouts with flexible widths, using flexbox. This component does not use Filament's [grid system](#grid-system).

```php
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Flex;

Flex::make([
    Section::make([
        TextInput::make('title'),
        Textarea::make('content'),
    ]),
    Section::make([
        Toggle::make('is_published'),
        Toggle::make('is_featured'),
    ])->grow(false),
])->from('md')
```

In this example, the first section will `grow()` to consume available horizontal space, without affecting the amount of space needed to render the second section. This creates a flexible width sidebar effect.

The `from()` method is used to control the [Tailwind breakpoint](https://tailwindcss.com/docs/responsive-design#overview) (`sm`, `md`, `lg`, `xl`, `2xl`) at which the horizontally-split layout should be used. In this example, the horizontally-split layout will be used on medium devices and larger. On smaller devices, the sections will stack on top of each other.

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing static values, the `grow()` and `from()` methods also accept functions to dynamically calculate them. You can inject various utilities into the functions as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/flex/simple" alt="Flex" version="4.x" />

### Fieldset component

You may want to group fields into a Fieldset. Each fieldset has a label, a border, and a two-column grid by default:

```php
use Filament\Schemas\Components\Fieldset;

Fieldset::make('Label')
    ->columns([
        'default' => 1,
        'md' => 2,
        'xl' => 3,
    ])
    ->schema([
        // ...
    ])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static label, the `make()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/fieldset/simple" alt="Fieldset" version="4.x" />

### Removing the border from a fieldset

You can remove the container border from a fieldset using the `contained(false)` method:

```php
use Filament\Schemas\Components\Fieldset;

Fieldset::make('Label')
    ->contained(false)
    ->schema([
        // ...
    ])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `contained()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/fieldset/not-contained" alt="Fieldset without a container border" version="4.x" />

## Using container queries

In addition to traditional breakpoints based on the size of the viewport, you can also use [container queries](https://tailwindcss.com/docs/responsive-design#container-queries) to create responsive layouts based on the size of a parent container. This is particularly useful when the size of the parent container is not directly tied to the size of the viewport. For example, when using a collapsible sidebar alongside the content, the content area dynamically adjusts its size depending on the collapse state of the sidebar.

The foundation of a container query is the container itself. The container is the element whose width determines the layout. To designate an element as a container, use the `gridContainer()` method on it. For instance, if you want to define the number of grid columns in a [`Grid` component] based on its width:

```php
use Filament\Schemas\Components\Grid;

Grid::make()
    ->gridContainer()
    ->columns([
        // ...
    ])
    ->schema([
        // ...
    ])
```

Once an element is specified as a grid container, the element or any of its children can utilize [container breakpoints](https://tailwindcss.com/docs/responsive-design#container-size-reference) instead of standard breakpoints. For example, you could use `@md` to define the number of grid columns when the container's width is at least `448px`, and `@xl` for when the width is at least `576px`.

```php
use Filament\Schemas\Components\Grid;

Grid::make()
    ->gridContainer()
    ->columns([
        '@md' => 3,
        '@xl' => 4,
    ])
    ->schema([
        // ...
    ])
```

You can also use container breakpoints in the `columnSpan()`, `columnStart()`, and `columnOrder()` methods:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

Grid::make()
    ->gridContainer()
    ->columns([
        '@md' => 3,
        '@xl' => 4,
    ])
    ->schema([
        TextInput::make('name')
            ->columnSpan([
                '@md' => 2,
                '@xl' => 3,
            ])
            ->columnOrder([
                'default' => 2,
                '@xl' => 1,
            ]),
        TextInput::make('email')
            ->columnSpan([
                'default' => 1,
                '@xl' => 1,
            ])
            ->columnOrder([
                'default' => 1,
                '@xl' => 2,
            ]),
        // ...
    ])
```

In this example, when the container width is smaller than the `@xl` breakpoint (576px), the email field will appear first followed by the name field. When the container width is at least 576px, the order is reversed with the name field appearing first followed by the email field.

### Supporting container queries on older browsers

Container queries are not yet widely [supported in browsers](https://caniuse.com/css-container-queries) compared to traditional breakpoints. To support older browsers, you can define an additional layer of breakpoints alongside the container breakpoints. By prefixing the traditional breakpoint with `!@`, you can specify that the fallback breakpoint should be used when container queries are not supported in the browser. 

For example, if you want to use the `@md` container breakpoint for the grid columns but also support older browsers, you can define the `!@md` fallback breakpoint, which will be applied when container queries are unavailable:

```php
use Filament\Schemas\Components\Grid;

Grid::make()
    ->gridContainer()
    ->columns([
        '@md' => 3,
        '@xl' => 4,
        '!@md' => 2,
        '!@xl' => 3,
    ])
    ->schema([
        // ...
    ])
```

You can also use `!@` fallback breakpoints in the `columnSpan()`, `columnStart()`, and `columnOrder()` methods:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

Grid::make()
    ->gridContainer()
    ->columns([
        '@md' => 3,
        '@xl' => 4,
        '!@md' => 2,
        '!@xl' => 3,
    ])
    ->schema([
        TextInput::make('name')
            ->columnSpan([
                '@md' => 2,
                '@xl' => 3,
                '!@md' => 2,
                '!@xl' => 2,
            ])
            ->columnOrder([
                'default' => 2,
                '@xl' => 1,
                '!@xl' => 1,
            ]),
        TextInput::make('email')
            ->columnOrder([
                'default' => 1,
                '@xl' => 2,
                '!@xl' => 2,
            ]),
        // ...
    ])
```

In this example, the fallback breakpoints ensure that even in browsers that don't support container queries, the layout will still respond to viewport size changes, with the name field appearing first and the email field second on larger screens.

## Controlling spacing between components

### Reducing space between components

The `dense()` method creates a more compact layout by reducing the spacing between components by 50%:

```php
use Filament\Schemas\Components\Grid;

Grid::make()
    ->dense()
    ->schema([
        // ...
    ])
```

<AutoScreenshot name="schemas/layout/dense" alt="A layout with dense spacing" version="4.x" />

### Removing space between components

The `gap(false)` method removes space between components:

```php
use Filament\Schemas\Components\Grid;

Grid::make()
    ->gap(false)
    ->schema([
        // ...
    ])
```

<AutoScreenshot name="schemas/layout/no-gap" alt="A layout with no gap" version="4.x" />

## Adding extra HTML attributes to a layout component

You can pass extra HTML attributes to the component via the `extraAttributes()` method, which will be merged onto its outer HTML element. The attributes should be represented by an array, where the key is the attribute name and the value is the attribute value:

```php
use Filament\Schemas\Components\Section;

Section::make()
    ->extraAttributes(['class' => 'custom-section-style'])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `extraAttributes()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

By default, calling `extraAttributes()` multiple times will overwrite the previous attributes. If you wish to merge the attributes instead, you can pass `merge: true` to the method.
