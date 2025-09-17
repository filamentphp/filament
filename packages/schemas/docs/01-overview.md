---
title: Overview
---
import Aside from "@components/Aside.astro"
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

Schemas form the foundation of Filament's Server-Driven UI approach. They allow you to build user interfaces declaratively using PHP configuration objects. These configuration objects represent components that define the structure and behavior of your UI, such as forms, tables, or lists. Rather than manually writing HTML or JavaScript, you create these schemas to control what gets rendered on the server, streamlining development and ensuring consistency across your app.

Schemas are used extensively across Filament to render UI elements dynamically. Whether you're defining a form field, the layout of your page, or an action button, the schema object defines both the component's configuration and how it interacts with your data. In essence, schemas are the building blocks of Filament UIs.

Filament packages provide you with various components. You can find a full list in the [available components section](#available-components):

- [Form fields](../forms) accept input from the user, for example, a text input, a select, or a checkbox. They come with integrated validation.
- [Infolist entries](../infolists) are components for rendering "description lists." Entries are key-value UI elements that can present read-only information like text, icons, and images. The data for an infolist can be sourced from anywhere but commonly comes from an individual Eloquent record.
- [Layout components](layouts) are used to structure the components. For example, a grid, tabs, or a multi-step form wizard.
- [Prime components](primes) are simple components that are used to render basic stand-alone static content, such as text, images, and buttons (actions).

Schemas act as a container for many components, and you can add any combination of components within them. Components can also nest child schemas within them, allowing for an infinite level of nesting.

A schema is represented by a `Filament\Schemas\Schema` object, and you can pass an array of components to it in the `components()` method.

## Available components

For building [forms](../forms), Filament includes a set of fields for different data types:

- [Text input](../forms/text-input)
- [Select](../forms/select)
- [Checkbox](../forms/checkbox)
- [Toggle](../forms/toggle)
- [Checkbox list](../forms/checkbox-list)
- [Radio](../forms/radio)
- [Date-time picker](../forms/date-time-picker)
- [File upload](../forms/file-upload)
- [Rich editor](../forms/rich-editor)
- [Markdown editor](../forms/markdown-editor)
- [Repeater](../forms/repeater)
- [Builder](../forms/builder)
- [Tags input](../forms/tags-input)
- [Textarea](../forms/textarea)
- [Key-value](../forms/key-value)
- [Color picker](../forms/color-picker)
- [Toggle buttons](../forms/toggle-buttons)
- [Slider](../forms/slider)
- [Code editor](../forms/code-editor)
- [Hidden](../forms/hidden)
- Or, build your own [custom form field](../forms/custom-fields)

For displaying data in a label-value "description list" format, Filament includes [infolist](../infolists) entry components:

- [Text entry](../infolists/text-entry)
- [Icon entry](../infolists/icon-entry)
- [Image entry](../infolists/image-entry)
- [Color entry](../infolists/color-entry)
- [Code entry](../infolists/code-entry)
- [Key-value entry](../infolists/key-value-entry)
- [Repeatable entry](../infolists/repeatable-entry)
- Or, build your own [custom infolist entry](../infolists/custom-entries)

To arrange components into a [layout](layouts), Filament includes layout components:

- [Grid](layouts#grid-component)
- [Flex](layouts#flex-component)
- [Fieldset](layouts#fieldset-component)
- [Section](sections)
- [Tabs](tabs)
- [Wizard](wizards)
- Or, build your own [custom layout component](custom-components#custom-layout-components)

For displaying arbitrary content, Filament includes [prime](primes) components:

- [Text](primes#text-component)
- [Icon](primes#icon-component)
- [Image](primes#image-component)
- [Unordered list](primes#unordered-list-component)

You can also insert "action" buttons into schemas. These can run PHP functions, and even open modals. For more information, see the [actions documentation](../actions).

You can learn more about building custom components to render your own Blade views in the [custom components documentation](custom-components).

## An example schema

For example, you may want to build a form with a schema. The name of the schema is usually dictated by the name of the method that it is defined in (`form` in this example). Filament creates the `Schema` object and passes it to the method, which then returns the schema with the components added:

```php
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

$schema
    ->components([
        Grid::make(2)
            ->schema([
                Section::make('Details')
                    ->schema([
                        TextInput::make('name'),
                        Select::make('position')
                            ->options([
                                'developer' => 'Developer',
                                'designer' => 'Designer',
                            ]),
                        Checkbox::make('is_admin'),
                    ]),
                Section::make('Auditing')
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ]),
            ]),
    ])
```

<AutoScreenshot name="schemas/overview/example" alt="Example schema" version="4.x" />

[Grid](layouts#grid-component) is a layout component that renders multiple components together in a responsive grid. The number of columns in the grid is specified in the `make()` method. The `schema()` method is used to nest components within the grid.

[Section](sections) is another layout component that renders multiple components together in a card, with a heading at the top.

[TextInput](../forms/text-input), [Select](../forms/select), and [Checkbox](../forms/checkbox) are form components that accept input from the user.

[TextEntry](../infolists/text-entry) is an infolist component that displays read-only information. In this example, it is used to display the created and updated timestamps of the record. The `dateTime()` method is used to format the timestamps as dates and times.

The schema object is the container for the components and can now be rendered. Rendering the schema will render all the components within it in the correct layout.

## Component utility injection

The vast majority of methods used to configure entries accept functions as parameters instead of hardcoded values:

```php
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

Grid::make(fn (): array => [
    'lg' => auth()->user()->isAdmin() ? 4 : 6,
])->schema([
    // ...
])

Section::make()
    ->heading(fn (): string => auth()->user()->isAdmin() ? 'Admin Dashboard' : 'User Dashboard')
    ->schema([
        // ...
    ])
```

This alone unlocks many customization possibilities.

The package is also able to inject many utilities to use inside these functions, as parameters. All customization methods that accept functions as arguments can inject utilities.

These injected utilities require specific parameter names to be used. Otherwise, Filament doesn't know what to inject.

### Injecting the state of another component

You may also retrieve the state (value) of a form field or infolist entry from within a callback, using a `$get` parameter:

```php
use Filament\Schemas\Components\Utilities\Get;

function (Get $get) {
    $email = $get('email'); // Store the value of the `email` entry in the `$email` variable.
    //...
}
```

<Aside variant="tip">
    Unless a form field is [reactive](../forms/overview#the-basics-of-reactivity), the schema will not refresh when the value of the field changes, only when the next user interaction occurs that makes a request to the server. If you need to react to changes in a field's value, it should be `live()`.
</Aside>

### Injecting the current Eloquent record

You may retrieve the Eloquent record for the current schema using a `$record` parameter:

```php
use Illuminate\Database\Eloquent\Model;

function (?Model $record) {
    // ...
}
```

### Injecting the current operation

If you're writing a schema for a panel resource or relation manager, and you wish to check if a schema is `create`, `edit` or `view`, use the `$operation` parameter:

```php
function (string $operation) {
    // ...
}
```

<Aside variant="info">
    You can manually set a schema's operation using the `$schema->operation()` method.
</Aside>

### Injecting the current Livewire component instance

If you wish to access the current Livewire component instance, define a `$livewire` parameter:

```php
use Livewire\Component;

function (Component $livewire) {
    // ...
}
```

### Injecting the current component instance

If you wish to access the current component instance, define a `$component` parameter:

```php
use Filament\Schemas\Components\Component;

function (Component $component) {
    // ...
}
```

### Injecting multiple utilities

The parameters are injected dynamically using reflection, so you are able to combine multiple parameters in any order:

```php
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Livewire\Component as Livewire;

function (Livewire $livewire, Get $get, Set $set) {
    // ...
}
```

### Injecting dependencies from Laravel's container

You may inject anything from Laravel's container like normal, alongside utilities:

```php
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Http\Request;

function (Request $request, Set $set) {
    // ...
}
```

## Global settings

If you wish to change the default behavior of a component globally, then you can call the static `configureUsing()` method inside a service provider's `boot()` method, to which you pass a Closure to modify the component using. For example, if you wish to make all section components have [2 columns](sections#using-grid-columns-within-a-section) by default, you can do it like so:

```php
use Filament\Schemas\Components\Section;

Section::configureUsing(function (Section $section): void {
    $section
        ->columns(2);
});
```

Of course, you are still able to overwrite this on each component individually:

```php
use Filament\Schemas\Components\Section;

Section::make()
    ->columns(1)
```
