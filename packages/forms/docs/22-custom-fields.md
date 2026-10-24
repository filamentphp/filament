---
title: Custom fields
---

## Introduction

You may create your own custom field classes and views, which you can reuse across your project, and even release as a plugin to the community.

## Custom field classes

To create a custom field class and view, you may use the following command:

```bash
php artisan make:filament-form-field LocationPicker
```

This will create the following component class:

```php
use Filament\Forms\Components\Field;

class LocationPicker extends Field
{
    protected string $view = 'filament.forms.components.location-picker';
}
```

It will also create a view file at `resources/views/filament/forms/components/location-picker.blade.php`.

<Aside variant="info">
    Filament form fields are **not** Livewire components. Defining public properties and methods on a form field class will not make them accessible in the Blade view.
</Aside>

## Binding a field to its state

A field's state is stored in a public property on its Livewire component. Use `$getStatePath()` in the field's view to bind to the correct property, including when the field is nested inside a repeater or another component. You can bind with [`wire:model`](https://livewire.laravel.com/docs/properties#data-binding) or [entangle an Alpine.js property](https://livewire.laravel.com/docs/javascript#the-wire-object):

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <input wire:model="{{ $getStatePath() }}" />

    <!-- Or -->

    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        <input x-model="state" />
    </div>
</x-dynamic-component>
```

See [obeying state binding modifiers](#obeying-state-binding-modifiers) to support options such as `live()` and `debounce()`. Preserve the field's [accessibility associations](overview#preserving-accessibility-in-custom-field-views) when creating its control markup.

If your field uses third-party JavaScript libraries, you can [load its Alpine.js component asynchronously](../advanced/assets#asynchronous-alpinejs-components) so those assets are only loaded when needed.

## Accessing the state of another component in the Blade view

Inside the Blade view, you may access the state of another component in the schema using the `$get()` function:

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    {{ $get('email') }}
</x-dynamic-component>
```

<Aside variant="tip">
    Unless a form field is [reactive](../forms/overview#the-basics-of-reactivity), the Blade view will not refresh when the value of the field changes, only when the next user interaction occurs that makes a request to the server. If you need to react to changes in a field's value, it should be `live()`.
</Aside>

## Accessing the Eloquent record in the Blade view

Inside the Blade view, you may access the current Eloquent record using the `$record` variable:

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    {{ $record->name }}
</x-dynamic-component>
```

## Accessing the current operation in the Blade view

Inside the Blade view, you may access the current operation, usually `create`, `edit` or `view`, using the `$operation` variable:

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @if ($operation === 'create')
        This is a new conference.
    @else
        This is an existing conference.
    @endif
</x-dynamic-component>
```

## Accessing the current Livewire component instance in the Blade view

Inside the Blade view, you may access the current Livewire component instance using `$this`:

```blade
@php
    use Filament\Resources\Users\RelationManagers\ConferencesRelationManager;
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @if ($this instanceof ConferencesRelationManager)
        You are editing conferences the of a user.
    @endif
</x-dynamic-component>
```

## Accessing the current field instance in the Blade view

Inside the Blade view, you may access the current field instance using `$field`. You can call public methods on this object to access other information that may not be available in variables:

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @if ($field->getState())
        This is a new conference.
    @endif
</x-dynamic-component>
```

## Adding a configuration method to a custom field class

You may add a public method to the custom field class that accepts a configuration value, stores it in a protected property, and returns it again from another public method:

```php
use Filament\Forms\Components\Field;

class LocationPicker extends Field
{
    protected string $view = 'filament.forms.components.location-picker';
    
    protected ?float $zoom = null;

    public function zoom(?float $zoom): static
    {
        $this->zoom = $zoom;

        return $this;
    }

    public function getZoom(): ?float
    {
        return $this->zoom;
    }
}
```

Now, in the Blade view for the custom field, you may access the zoom using the `$getZoom()` function:

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    {{ $getZoom() }}
</x-dynamic-component>
```

Any public method that you define on the custom field class can be accessed in the Blade view as a variable function in this way.

To pass the configuration value to the custom field class, you may use the public method:

```php
use App\Filament\Forms\Components\LocationPicker;

LocationPicker::make('location')
    ->zoom(0.5)
```

## Allowing utility injection in a custom field configuration method

[Utility injection](overview#field-utility-injection) is a powerful feature of Filament that allows users to configure a component using functions that can access various utilities. You can allow utility injection by ensuring that the parameter type and property type of the configuration allows the user to pass a `Closure`. In the getter method, you should pass the configuration value to the `$this->evaluate()` method, which will inject utilities into the user's function if they pass one, or return the value if it is static:

```php
use Closure;
use Filament\Forms\Components\Field;

class LocationPicker extends Field
{
    protected string $view = 'filament.forms.components.location-picker';
    
    protected float | Closure | null $zoom = null;

    public function zoom(float | Closure | null $zoom): static
    {
        $this->zoom = $zoom;

        return $this;
    }

    public function getZoom(): ?float
    {
        return $this->evaluate($this->zoom);
    }
}
```

Now, you can pass a static value or a function to the `zoom()` method, and [inject any utility](overview#field-utility-injection) as a parameter:

```php
use App\Filament\Forms\Components\LocationPicker;

LocationPicker::make('location')
    ->zoom(fn (Conference $record): float => $record->isGlobal() ? 1 : 0.5)
```

## Obeying state binding modifiers

When you bind a field to a state path, you may use the `defer` modifier to ensure that the state is only sent to the server when the user submits the form, or whenever the next Livewire request is made. This is the default behavior.

However, you may use the [`live()`](overview#the-basics-of-reactivity) on a field to ensure that the state is sent to the server immediately when the user interacts with the field. This allows for lots of advanced use cases as explained in the [reactivity](overview#the-basics-of-reactivity) section of the documentation.

Filament provides a `$applyStateBindingModifiers()` function that you may use in your view to apply any state binding modifiers to a `wire:model` or `$wire.$entangle()` binding:

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <input {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}" />

    <!-- Or -->

    <div x-data="{ state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }} }">
        <input x-model="state" />
    </div>
</x-dynamic-component>
```

## Calling field methods from JavaScript

You can call methods on your custom field class from JavaScript in a Blade view. For example, a location picker might call a geocoding service in PHP. Expose the method using `#[Exposed]`.

### Exposing a method

Add `#[Exposed]` to a public instance method on your field:

```php
use Filament\Forms\Components\Field;
use Filament\Support\Components\Attributes\Exposed;

class LocationPicker extends Field
{
    protected string $view = 'filament.forms.components.location-picker';

    #[Exposed]
    public function geocodeAddress(string $address): array
    {
        // Perform geocoding logic...

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }
}
```

<Aside variant="warning">
    Only methods marked with `#[Exposed]` can be called from JavaScript. The attribute does not authorize access to the method's data or operations. Validate client-supplied arguments and [authorize sensitive operations](../advanced/security#authorization) inside the method.

    Exposed methods remain callable when the field is [disabled](overview#disabling-a-field), since read methods may still be needed to display its value. If a method changes the field's value and should respect its disabled state, check `$this->isDisabled()` before making changes.
</Aside>

Arguments must be a JSON-serializable object keyed by PHP parameter name. Filament binds each call to the current field, including nested repeater instances. The returned promise resolves to the method's return value, or `null` if the component or exposed method is missing. Calls make Livewire requests and can send pending deferred edits, regardless of `live()`.

Use the PHP method's casing in JavaScript. Built-in utility and Alpine magic names are reserved; a method named `get`, for example, is available through `$callSchemaComponentMethod()` without replacing `$get()`. Do not register Alpine magics with the same names as your exposed methods.

### Calling the method from JavaScript

In your Blade view, call the exposed method using its `$`-prefixed name. Pass an object keyed by PHP parameter name. The utility is bound to this field, so you do not need its key. You can also use `$callSchemaComponentMethod('geocodeAddress', { address: this.address })`, including when a method name collides with a built-in utility such as `$get()`:

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            address: '',
            coordinates: null,
            async geocode() {
                this.coordinates = await this.$geocodeAddress(
                    { address: this.address },
                )
            },
        }"
    >
        <input type="text" x-model="address" />
        <button type="button" x-on:click="geocode">Geocode</button>

        <template x-if="coordinates">
            <p x-text="`${coordinates.latitude}, ${coordinates.longitude}`"></p>
        </template>
    </div>
</x-dynamic-component>
```

### Preventing re-renders

By default, calling an exposed method triggers a re-render of the Livewire component. If your method only returns data and does not change state or PHP props that need rendering, add Livewire's `#[Renderless]` attribute. Other pending updates or calls in the same request may still require rendering:

```php
use Filament\Forms\Components\Field;
use Filament\Support\Components\Attributes\Exposed;
use Livewire\Attributes\Renderless;

class LocationPicker extends Field
{
    protected string $view = 'filament.forms.components.location-picker';

    #[Exposed]
    #[Renderless]
    public function geocodeAddress(string $address): array
    {
        // ...
    }
}
```
