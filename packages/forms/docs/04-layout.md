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

#### Controlling field column span

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

### Saving data to relationships

You may load and save the contents of a layout component to a `HasOne`, `BelongsTo` or `MorphOne` Eloquent relationship, using the `relationship()` method:

```php
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

Fieldset::make('Metadata')
    ->relationship('metadata')
    ->schema([
        TextInput::make('title'),
        Textarea::make('description'),
        FileUpload::make('image'),
    ])
```

In this example, the `title`, `description` and `image` are automatically loaded from the `metadata` relationship, and saved again when the form is submitted. If the `metadata` record does not exist, it is automatically created.

> To set this functionality up, **you must also follow the instructions set out in the [field relationships](getting-started#field-relationships) section**. If you're using the [admin panel](/docs/admin), you can skip this step.

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

The first tab will be open by default. You can change the default open tab using the `activeTab()` method:

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
    ->activeTab(2)
```

Tabs may have an icon and badge, which you can set using the `icon()` and `badge()` methods:

```php
use Filament\Forms\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Notifications')
            ->icon('heroicon-o-bell') // [tl! focus:start]
            ->badge('39') // [tl! focus:end]
            ->schema([
                // ...
            ]),
        // ...
    ])
```

Icons can be modified using the `iconPosition()` and `iconColor()` methods:

```php
use Filament\Forms\Components\Tabs;

Tabs::make('Heading')
    ->tabs([
        Tabs\Tab::make('Notifications')
            ->icon('heroicon-o-bell')
            ->iconPosition('after') // `before` or `after` [tl! focus:end]
            ->iconColor('success') // `danger`, `primary`, `success`, `warning` or `secondary` [tl! focus:end]
            ->schema([
                // ...
            ]),
        // ...
    ])
```

## Wizard

Similar to [tabs](#tabs), you may want to use a multistep form wizard to reduce the number of components that are visible at once. These are especially useful if your form has a definite chronological order, in which you want each step to be validated as the user progresses.

```php
use Filament\Forms\Components\Wizard;

Wizard::make([
    Wizard\Step::make('Order')
        ->schema([
            // ...
        ]),
    Wizard\Step::make('Delivery')
        ->schema([
            // ...
        ]),
    Wizard\Step::make('Billing')
        ->schema([
            // ...
        ]),
])
```

> We have different setup instructions if you're looking to add a wizard to an admin panel [resource Create page](../admin/resources/creating-records#wizards) or a table [action](../tables/actions#wizards). Following that documentation will ensure that the ability to submit the form is only available on the last step.

Each step has a mandatory label. You may optionally also add a description for extra detail:

```php
use Filament\Forms\Components\Wizard;

Wizard\Step::make('Order')
    ->description('Review your basket')
    ->schema([
        // ...
    ]),
```

Steps may also have an icon, which can be the name of any Blade icon component:

```php
use Filament\Forms\Components\Wizard;

Wizard\Step::make('Order')
    ->icon('heroicon-o-shopping-bag')
    ->schema([
        // ...
    ]),
```

You may use the `submitAction()` method to render submit button HTML or a view at the end of the wizard, on the last step. This provides a clearer UX than displaying a submit button below the wizard at all times:

```php
use Filament\Forms\Components\Wizard;
use Illuminate\Support\HtmlString;

Wizard::make([
    // ...
])->submitAction(view('order-form.submit-button'))

Wizard::make([
    // ...
])->submitAction(new HtmlString('<button type="submit">Submit</button>'))
```

You may use the `startOnStep()` method to load a specific step in the wizard:

```php
use Filament\Forms\Components\Wizard;

Wizard::make([
    // ...
])->startOnStep(2)
```

If you'd like to allow free navigation, so all steps are skippable, use the `skippable()` method:

```php
use Filament\Forms\Components\Wizard;

Wizard::make([
    // ...
])->skippable()
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

You may use the `aside()` to align heading & description on the left, and the form components inside a card on the right:

```php
use Filament\Forms\Components\Section;

Section::make('Heading')
    ->description('Description')
    ->aside()
    ->schema([
        // ...
    ])
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

When nesting sections, you can use a more compact styling:

```php
use Filament\Forms\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->compact()
```

## Placeholder

Placeholders can be used to render text-only "fields" within your forms. Each placeholder has `content()`, which cannot be changed by the user.

> **Important:** All fields require a unique name. That also applies to Placeholders!

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

## Inline labels

You may use the `inlineLabel()` method to make the form labels and fields in separate columns, inline with each other. It works on all layout components, each field inside will have an inline label.

```php
use Filament\Forms\Components\Card;

Card::make()
    ->schema([
        // ...
    ])
    ->inlineLabel()
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
