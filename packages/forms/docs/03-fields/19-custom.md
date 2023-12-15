---
title: Custom fields
---
import LaracastsBanner from "@components/LaracastsBanner.astro"

<LaracastsBanner
    title="Build a Custom Form Field"
    description="Watch the Build Advanced Components for Filament series on Laracasts - it will teach you how to build components, and you'll get to know all the internal tools to help you."
    url="https://laracasts.com/series/build-advanced-components-for-filament/episodes/6"
    series="building-advanced-components"
/>

## View fields

Aside from [building custom fields](#custom-field-classes), you may create "view" fields which allow you to create custom fields without extra PHP classes.

```php
use Filament\Forms\Components\ViewField;

ViewField::make('rating')
    ->view('filament.forms.components.range-slider')
```

This assumes that you have a `resources/views/filament/forms/components/range-slider.blade.php` file.

### Passing data to view fields

You can pass a simple array of data to the view using `viewData()`:

```php
use Filament\Forms\Components\ViewField;

ViewField::make('rating')
    ->view('filament.forms.components.range-slider')
    ->viewData([
        'min' => 1,
        'max' => 5,
    ])
```

However, more complex configuration can be achieved with a [custom field class](#custom-field-classes).

## Custom field classes

You may create your own custom field classes and views, which you can reuse across your project, and even release as a plugin to the community.

> If you're just creating a simple custom field to use once, you could instead use a [view field](#view) to render any custom Blade file.

To create a custom field class and view, you may use the following command:

```bash
php artisan make:form-field RangeSlider
```

This will create the following field class:

```php
use Filament\Forms\Components\Field;

class RangeSlider extends Field
{
    protected string $view = 'filament.forms.components.range-slider';
}
```

It will also create a view file at `resources/views/filament/forms/components/range-slider.blade.php`.

## How fields work

Livewire components are PHP classes that have their state stored in the user's browser. When a network request is made, the state is sent to the server, and filled into public properties on the Livewire component class, where it can be accessed in the same way as any other class property in PHP can be.

Imagine you had a Livewire component with a public property called `$name`. You could bind that property to an input field in the HTML of the Livewire component in one of two ways: with the [`wire:model` attribute](https://livewire.laravel.com/docs/properties#data-binding), or by [entangling](https://livewire.laravel.com/docs/javascript#the-wire-object) it with an Alpine.js property:

```blade
<input wire:model="name" />

<!-- Or -->

<div x-data="{ state: $wire.$entangle('name') }">
    <input x-model="state" />
</div>
```

When the user types into the input field, the `$name` property is updated in the Livewire component class. When the user submits the form, the `$name` property is sent to the server, where it can be saved.

This is the basis of how fields work in Filament. Each field is assigned to a public property in the Livewire component class, which is where the state of the field is stored. We call the name of this property the "state path" of the field. You can access the state path of a field using the `$getStatePath()` function in the field's view:

```blade
<input wire:model="{{ $getStatePath() }}" />

<!-- Or -->

<div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
    <input x-model="state" />
</div>
```

If your component heavily relies on third party libraries, we advise that you asynchronously load the Alpine.js component using the Filament asset system. This ensures that the Alpine.js component is only loaded when it's needed, and not on every page load. To find out how to do this, check out our [Assets documentation](../../support/assets#asynchronous-alpinejs-components).

## Rendering the field wrapper

Filament includes a "field wrapper" component, which is able to render the field's label, validation errors, and any other text surrounding the field. You may render the field wrapper like this in the view:

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <!-- Field -->
</x-dynamic-component>
```

It's encouraged to use the field wrapper component whenever appropriate, as it will ensure that the field's design is consistent with the rest of the form.

## Accessing the Eloquent record

Inside your view, you may access the Eloquent record using the `$getRecord()` function:

```blade
<div>
    {{ $getRecord()->name }}
</div>
```

## Obeying state binding modifiers

When you bind a field to a state path, you may use the `defer` modifier to ensure that the state is only sent to the server when the user submits the form, or whenever the next Livewire request is made. This is the default behaviour.

However, you may use the [`live()`](../advanced#the-basics-of-reactivity) on a field to ensure that the state is sent to the server immediately when the user interacts with the field. This allows for lots of advanced use cases as explained in the [advanced](../advanced) section of the documentation.

Filament provides a `$applyStateBindingModifiers()` function that you may use in your view to apply any state binding modifiers to a `wire:model` or `$wire.$entangle()` binding:

```blade
<input {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}" />

<!-- Or -->

<div x-data="{ state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }} }">
    <input x-model="state" />
</div>
```
