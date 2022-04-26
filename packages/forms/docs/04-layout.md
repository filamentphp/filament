---
title: Layout
---

## Getting started

Layout component classes can be found in the `Filament\Form\Components` namespace.

They reside within the schema of your form, alongside any [fields](fields).

If you're using the layout components in a Livewire component, you can put them in the `getFormSchema()` method:

```php
protected function getFormSchema(): array
{
    return [
        // ...
    ];
}
```

If you're using them in admin panel resources or relation managers, you must put them in the `$form->schema()` method:

```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            // ...
        ]);
}
```

Components may be created using the static `make()` method. Usually, you will then define the child component `schema()` to display inside:

```php
use Filament\Forms\Components\Grid;

Grid::make()
    ->schema([
        // ...
    ])
```

### Columns

You may create multiple columns within each layout component using the `columns()` method:

```php
use Filament\Forms\Components\Card;

Card::make()->columns(2)
```

> For more information about creating advanced, responsive column layouts, please see the [grid section](#grid). All column options in that section are also available in other layout components.

### Setting an ID

You may define an ID for the component using the `id()` method:

```php
use Filament\Forms\Components\Card;

Card::make()->id('main-card')
```

### Custom attributes

The HTML of components can be customized even further, by passing an array of `extraAttributes()`:

```php
use Filament\Forms\Components\Card;

Card::make()->extraAttributes(['class' => 'bg-gray-50'])
```

### Global settings

If you wish to change the default behaviour of a component globally, then you can call the static `configureUsing()` method inside a service provider's `boot()` method, to which you pass a Closure to modify the component using. For example, if you wish to make all card components have [2 columns](#columns) by default, you can do it like so:

```php
use Filament\Forms\Components\Card;

Card::configureUsing(function (Card $card): void {
    $card->columns(2);
});
```

Of course, you are still able to overwrite this on each field individually:

```php
use Filament\Forms\Components\Card;

Card::make()->columns(1)
```

## Grid

Generally, form fields are stacked on top of each other in one column. To change this, you may use a grid component:

```php
use Filament\Forms\Components\Grid;

Grid::make()
    ->schema([
        // ...
    ])
```

By default, grid components will create a two column grid for [the Tailwind `md` breakpoint](https://tailwindcss.com/docs/responsive-design#overview) and higher.

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

You may specify the number of columns that any component may span, at any breakpoint in an identical way:

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

## Fieldset

You may want to group fields into a Fieldset. Each fieldset has a label, a border, and a two-column grid by default:

```php
use Filament\Forms\Components\Fieldset;

Fieldset::make('Label')
    ->schema([
        // ...
    ])
```

You may use the `columns()` method to customize the [grid](#grid) within the fieldset:

```php
use Filament\Forms\Components\Fieldset;

Fieldset::make('Label')
    ->schema([
        // ...
    ])
    ->columns(3)
```

## Tabs

Some forms can be long and complex. You may want to use tabs to reduce the number of components that are visible at once:

```php
use Filament\Forms\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Label 1')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Label 2')
            ->schema([
                // ...
            ]),
        Tabs\Tab::make('Label 3')
            ->schema([
                // ...
            ]),
    ])
```

## Section

You may want to separate your fields into sections, each with a heading and description. To do this, you can use a section component:

```php
use Filament\Forms\Components\Section;

Section::make('Heading')
    ->description('Description')
    ->schema([
        // ...
    ])
```

You may use the `columns()` method to easily create a [grid](#grid) within the section:

```php
use Filament\Forms\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->columns(2)
```

Sections may be `collapsible()` to optionally hide content in long forms:

```php
use Filament\Forms\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->collapsible()
```

Your sections may be `collapsed()` by default:

```php
use Filament\Forms\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->collapsed()
```

## Placeholder

Placeholders can be used to render text-only "fields" within your forms. Each placeholder has `content()`, which cannot be changed by the user.

```php
use Filament\Forms\Components\Placeholder;

Placeholder::make('Label')
    ->content('Content, displayed underneath the label')
```

You may even render custom HTML within placeholder content:

```php
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

Placeholder::make('Documentation')
    ->content(new HtmlString('<a href="https://filamentphp.com/docs">filamentphp.com</a>'))
```

## Card

The card component may be used to render the form components inside a card:

```php
use Filament\Forms\Components\Card;

Card::make()
    ->schema([
        // ...
    ])
```

You may use the `columns()` method to easily create a [grid](#grid) within the card:

```php
use Filament\Forms\Components\Card;

Card::make()
    ->schema([
        // ...
    ])
    ->columns(2)
```

## View

Aside from [building custom layout components](#building-custom-layout-components), you may create "view" components which allow you to create custom layouts without extra PHP classes.

```php
use Filament\Forms\Components\View;

View::make('filament.forms.components.wizard')
```

Inside your view, you may render the component's `schema()` using the `$getChildComponentContainer()` closure:

```blade
<div>
    {{ $getChildComponentContainer() }}
</div>
```

## Building custom layout components

You may create your own custom component classes and views, which you can reuse across your project, and even release as a plugin to the community.

> If you're just creating a simple custom component to use once, you could instead use a [view component](#view) to render any custom Blade file.

To create a custom column class and view, you may use the following command:

```bash
php artisan make:form-layout Wizard
```

This will create the following layout component class:

```php
use Filament\Forms\Components\Component;

class Wizard extends Component
{
    protected string $view = 'filament.forms.components.wizard';
    
    public static function make(): static
    {
        return new static();
    }
}
```

Inside your view, you may render the component's `schema()` using the `$getChildComponentContainer()` closure:

```blade
<div>
    {{ $getChildComponentContainer() }}
</div>
```
