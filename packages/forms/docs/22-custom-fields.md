---
title: Custom fields
---
import Aside from "@components/Aside.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Livewire components are PHP classes that have their state stored in the user's browser. When a network request is made, the state is sent to the server, and filled into public properties on the Livewire component class, where it can be accessed in the same way as any other class property in PHP can be.

Imagine you had a Livewire component with a public property called `$name`. You could bind that property to an input field in the HTML of the Livewire component in one of two ways: with the [`wire:model` attribute](https://livewire.laravel.com/docs/properties#data-binding), or by [entangling](https://livewire.laravel.com/docs/javascript#the-wire-object) it with an Alpine.js property:

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <input wire:model="name" />
    
    <!-- Or -->
    
    <div x-data="{ state: $wire.$entangle('name') }">
        <input x-model="state" />
    </div>
</x-dynamic-component>
```

When the user types into the input field, the `$name` property is updated in the Livewire component class. When the user submits the form, the `$name` property is sent to the server, where it can be saved.

This is the basis of how fields work in Filament. Each field is assigned to a public property in the Livewire component class, which is where the state of the field is stored. We call the name of this property the "state path" of the field. You can access the state path of a field using the `$getStatePath()` function in the field's view:

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

If your component heavily relies on third party libraries, we advise that you asynchronously load the Alpine.js component using the Filament asset system. This ensures that the Alpine.js component is only loaded when it's needed, and not on every page load. To find out how to do this, check out our [Assets documentation](../advanced/assets#asynchronous-alpinejs-components).

## Custom field classes

You may create your own custom field classes and views, which you can reuse across your project, and even release as a plugin to the community.

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

## Rendering fields with JavaScript frameworks

You can use `JsField` to render an input using Vue, React, Svelte, or another JavaScript library. Filament owns the field wrapper and state synchronization. Your application owns the renderer and its dependencies; Filament does not install or bundle a framework.

```php
use Filament\Forms\Components\JsField;
use Illuminate\Support\Facades\Vite;

JsField::make('location')
    ->renderer(Vite::asset('resources/js/fields/location-picker.jsx'))
    ->default(['latitude' => 51.5, 'longitude' => -0.12])
```

Pass the URL of an ES module to `renderer()`. Filament loads it when the field initializes, without a global registry or a script tag. Each field gets its own mounted instance, even when the browser has already loaded the module for another field.

If you already load the renderer through another script, you may pass a trusted `Filament\Support\RawJs` function expression instead of a URL.

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `renderer()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

Choose one of the examples below, then follow the [Vite module setup](../advanced/assets#building-lazy-loaded-es-modules) to build its entry file. For a plugin, you can [publish a prebuilt module](../advanced/assets#publishing-es-modules-in-plugins) instead. Your application or plugin owns the framework dependencies; Filament does not install them.

### Creating a React field

Create `resources/js/fields/location-picker.jsx`. The component edits a latitude and longitude, storing an empty input as `null`. Apply Filament's input props so labels, descriptions, validation, and disabled or read-only states work:

```jsx
import React from 'react'
import { createRoot } from 'react-dom/client'

function LocationPicker(props) {
    return ['latitude', 'longitude'].map((coordinate) => (
        <label key={coordinate}>
            {coordinate === 'latitude' ? 'Latitude' : 'Longitude'}
            <input
                id={coordinate === 'latitude' ? props.id : `${props.id}-longitude`}
                type="number"
                step="any"
                value={props.value?.[coordinate] ?? ''}
                disabled={props.disabled}
                readOnly={props.readOnly}
                required={props.required}
                aria-invalid={props.invalid}
                aria-describedby={props.ariaDescribedBy}
                onChange={(event) => props.onChange({
                    ...props.value,
                    [coordinate]: event.target.value === '' ? null : Number(event.target.value),
                })}
                onBlur={props.onBlur}
            />
        </label>
    ))
}

export default function mountLocationPicker({ host, props: initialProps }) {
    const root = createRoot(host)
    const update = (props) => root.render(
        <LocationPicker {...initialProps} {...props} />,
    )

    update(initialProps)

    return { update, destroy: () => root.unmount() }
}
```

The default export mounts the component inside `host`. Filament calls `update(props)` when state or PHP configuration changes, and `destroy()` when removing or remounting the field. Keep the initial callbacks when updating props, as shown above.

### Creating a Vue field

Create `resources/js/fields/LocationPicker.vue`:

```vue
<script setup>
defineOptions({ inheritAttrs: false })

const props = defineProps(['value', 'id', 'disabled', 'readOnly', 'required', 'invalid', 'ariaDescribedBy', 'onChange', 'onBlur'])

function changeCoordinate(coordinate, event) {
    props.onChange({
        ...props.value,
        [coordinate]: event.target.value === '' ? null : Number(event.target.value),
    })
}
</script>

<template>
    <label v-for="coordinate in ['latitude', 'longitude']" :key="coordinate">
        {{ coordinate === 'latitude' ? 'Latitude' : 'Longitude' }}
        <input
            :id="coordinate === 'latitude' ? props.id : `${props.id}-longitude`"
            type="number"
            step="any"
            :value="props.value?.[coordinate] ?? ''"
            :disabled="props.disabled"
            :readonly="props.readOnly"
            :required="props.required"
            :aria-invalid="props.invalid"
            :aria-describedby="props.ariaDescribedBy"
            @input="changeCoordinate(coordinate, $event)"
            @blur="props.onBlur()"
        />
    </label>
</template>
```

Create `resources/js/fields/location-picker.js` to mount it. Use this entry's URL in `renderer()`:

```js
import { createApp, h, shallowRef } from 'vue'
import LocationPicker from './LocationPicker.vue'

export default function mountLocationPicker({ host, props: initialProps }) {
    const props = shallowRef(initialProps)
    const application = createApp({
        setup: () => () => h(LocationPicker, props.value),
    })

    application.mount(host)

    return {
        update(nextProps) {
            props.value = { ...initialProps, ...nextProps }
        },
        destroy: () => application.unmount(),
    }
}
```

### Creating a Svelte field

For Svelte 5, create `resources/js/fields/LocationPicker.svelte`:

```svelte
<script>
    let { value, id, disabled, readOnly, required, invalid, ariaDescribedBy, onChange, onBlur } = $props()

    function changeCoordinate(coordinate, event) {
        onChange({
            ...value,
            [coordinate]: event.target.value === '' ? null : Number(event.target.value),
        })
    }
</script>

{#each ['latitude', 'longitude'] as coordinate (coordinate)}
    <label>
        {coordinate === 'latitude' ? 'Latitude' : 'Longitude'}
        <input
            id={coordinate === 'latitude' ? id : `${id}-longitude`}
            type="number"
            step="any"
            value={value?.[coordinate] ?? ''}
            {disabled}
            readonly={readOnly}
            {required}
            aria-invalid={invalid}
            aria-describedby={ariaDescribedBy}
            oninput={(event) => changeCoordinate(coordinate, event)}
            onblur={onBlur}
        />
    </label>
{/each}
```

Create `resources/js/fields/location-picker.svelte.js` to mount it. The `.svelte.js` extension allows the Svelte compiler to process `$state`. Use this entry's URL in `renderer()`:

```js
import { mount, unmount } from 'svelte'
import LocationPicker from './LocationPicker.svelte'

export default function mountLocationPicker({ host, props: initialProps }) {
    const props = $state({ ...initialProps })
    const component = mount(LocationPicker, { target: host, props })

    return {
        update: (nextProps) => Object.assign(props, nextProps),
        destroy: () => unmount(component),
    }
}
```

The examples leave styling to your application. See [registering CSS files](../advanced/assets#registering-css-files) to include a stylesheet with your field.

### Receiving state and emitting changes

Use `props.value` to render the field's current state and call `props.onChange(nextValue)` after a user interaction. Always pass the complete value, rather than mutating the incoming object. Values must be JSON-serializable: `null`, scalars, arrays, or objects. Initialize a particular shape using `default()`, and handle cleared values in your component.

Filament sends fresh state and configuration to `update(props)`, without the callbacks. Retain the initial `onChange()` and `onBlur()` callbacks, as shown in each example. Do not call `onChange()` when receiving props or rendering the component, as this can create feedback loops.

By default, edits update local Livewire state and are sent on the next request. Use the usual [state binding modifiers](#obeying-state-binding-modifiers) to change this behavior:

```php
use Filament\Forms\Components\JsField;
use Illuminate\Support\Facades\Vite;

JsField::make('location')
    ->renderer(Vite::asset('resources/js/fields/location-picker.jsx'))
    ->live(onBlur: true)
```

`live()` sends changes immediately, `live(onBlur: true)` waits for `props.onBlur()`, and `live(debounce: 500)` waits after the latest edit. Other Livewire requests may send pending state earlier.

Apply `id`, `disabled`, `readOnly`, `required`, `invalid`, and `ariaDescribedBy` to your inputs as shown in the examples. Use `id` on the primary input to associate Filament's label, and give additional inputs their own labels. `ariaDescribedBy` associates the wrapper's helper and validation text; append any additional description IDs instead of replacing it. If you override the field wrapper, preserve its supplied `descriptionId` around that text.

### Passing configuration from PHP

You can pass configuration to your renderer using `rendererProps()`:

```php
use Filament\Forms\Components\JsField;
use Illuminate\Support\Facades\Vite;

JsField::make('location')
    ->renderer(Vite::asset('resources/js/fields/location-picker.jsx'))
    ->rendererProps(['zoom' => 12])
```

Read it from `props.config.zoom` in your component. Configuration is separate from built-in props such as `value` and `onChange`, so it cannot override the state binding. Only pass JSON-serializable data intended for the browser, not secrets. Modifying configuration in JavaScript does not write back to PHP.

In the Vue example, add `config` to `defineProps()`. In the Svelte example, read `config` from `$props()` alongside `value` and the other props.

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `rendererProps()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

For example, you can use the selected country to configure a location picker:

```php
use Filament\Forms\Components\JsField;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Vite;

JsField::make('location')
    ->renderer(Vite::asset('resources/js/fields/location-picker.jsx'))
    ->rendererProps(static fn (Get $get): array => [
        'country' => $get('country'),
        'zoom' => 12,
    ])
```

When the field renders on the server, Filament reevaluates the closure and passes changed configuration to `update(props)`. This preserves the mounted component and its local state. A deferred country edit reaches `props.config.country` on the next server render; use [`live()`](overview#the-basics-of-reactivity) on the country field if you need it sooner. A request that skips rendering this field does not update its PHP-derived props.

Treat configuration as immutable. Pass `null` to `rendererProps()` to clear it. For a [reusable field](#building-reusable-plugin-fields), override `getRendererProps(): array` instead; its default is `[]`.

### Accessing schema utilities

You can accept `utilities` alongside `host` and `props` in your mount function, then pass it to your component as a prop. These are the utilities from the surrounding Alpine schema scope. For example, inside an interaction handler you can read or change a sibling country field:

```js
const country = utilities.$get('country')
await utilities.$set('country', 'GB')
```

`$get(path, isAbsolute)` and `$set(path, value, isAbsolute, isLive = false)` use paths relative to the field's container unless `isAbsolute` is `true`. `$set()` is deferred unless `isLive` is `true`. It does not apply the target field's read-only configuration or state binding modifiers, so use `props.onChange()` for ordinary edits to this field.

`utilities.$statePath` is this field's absolute state path. Read `utilities.$state` when you need its current value. It is a getter, so destructuring it during mounting captures only that initial value. Treat returned state as read-only.

You can destructure functions such as `$get` and `$set`, but keep the `utilities` object intact when passing it into reactive framework state. Spreading it evaluates getters such as `$state` and `$wire` immediately. Reads do not subscribe your framework to sibling-field changes; use [PHP configuration](#passing-configuration-from-php) for values that should update after a server render.

In Svelte components, local names beginning with `$` are reserved. Keep property access such as `utilities.$get()`, or alias destructured functions: `const { $get: getState, $set: setState } = utilities`.

### Calling PHP methods

You can [expose methods on your custom field class](#calling-field-methods-from-javascript) and call them through `utilities`, such as `utilities.$geocodeAddress({ address })`. The same method exposure and authorization rules apply to Blade and JavaScript-rendered fields. See [calling an exposed method from a framework](#calling-an-exposed-method-from-a-framework) for an example.

For methods on the owning Livewire component rather than the field class, use `utilities.$wire`. For example, if your Livewire component defines `saveLocation()`:

```js
await utilities.$wire.$call('saveLocation')
```

`$wire` is the original Livewire proxy, exposed through a getter to avoid Svelte deeply proxying it. Pass the `utilities` object intact. Direct `$wire` writes bypass the field's state binding timing; prefer `props.onChange()` for field edits. Handle request failures in your interaction handlers, and stop using utilities when the renderer is destroyed.

### Building reusable plugin fields

You do not need to extend `JsField` to build a reusable field. Use `HasJsRenderer` on your own `Field` class and implement `HasEmbeddedView`. The trait provides `toEmbeddedHtml()`, the field wrapper, read-only configuration, and state synchronization. Implement `getRenderer()` to return your module URL:

```php
namespace Vendor\LocationPicker\Forms\Components;

use Filament\Forms\Components\Concerns\HasJsRenderer;
use Filament\Forms\Components\Field;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Facades\FilamentAsset;

class LocationPicker extends Field implements HasEmbeddedView
{
    use HasJsRenderer;

    public function getRenderer(): string
    {
        return FilamentAsset::getScriptSrc('location-picker', 'vendor/location-picker');
    }
}
```

Follow the [plugin guide](../plugins/getting-started#creating-a-plugin) to set up your package and [publish its prebuilt module](../advanced/assets#publishing-es-modules-in-plugins) under this asset ID and package name. Consumers can then use `LocationPicker::make('location')->live()` without a renderer URL, a Blade view, or changes to their Vite setup.

Use the same mount function as for `JsField`. To pass [configuration](#passing-configuration-from-php), override `getRendererProps(): array` and return your field's evaluated configuration values. You can expose those values through the usual [fluent configuration methods](#adding-a-configuration-method-to-a-custom-field-class).

### Typing renderers

Filament includes TypeScript declarations in its Composer package, without a JavaScript runtime dependency. Add a type alias to your application's `tsconfig.json`, relative to that configuration file:

```json
{
    "compilerOptions": {
        "paths": {
            "@filament/forms/js-field": ["./vendor/filament/forms/types/js-field.d.ts"]
        }
    }
}
```

Use `JsFieldRenderer` to type your mount function's state and PHP configuration. The following shows where to apply the type to one of the implementations above:

```ts
import type { JsFieldRenderer } from '@filament/forms/js-field'

type Location = { latitude: number | null; longitude: number | null } | null
type Config = { zoom: number }

const mountLocationPicker: JsFieldRenderer<Location, Config> = ({ host, props, utilities }) => {
    // Mount your component with `props` and `utilities`.

    return {
        update(nextProps) {
            // Update it with `nextProps.value` and `nextProps.config`.
        },
        destroy() {
            // Unmount it and remove subscriptions.
        },
    }
}

export default mountLocationPicker
```

You can also import `JsFieldRendererContext`, `JsFieldInitialProps`, `JsFieldProps`, `JsFieldUtilities`, and `JsFieldRendererInstance` to type individual parts of your implementation. Initial props include callbacks; subsequent props do not. State and configuration are deeply read-only snapshots in TypeScript, not runtime-frozen objects. Unspecified types must be narrowed before use. Keep `import type` so your bundler does not try to load the declaration file, and keep PHP validation even when using TypeScript.

For [exposed PHP methods](#calling-an-exposed-method-from-a-framework), supply a third type argument, such as `JsFieldRenderer<Location, Config, Methods>`. Declare the methods your field exposes; PHP signatures are not automatically translated into TypeScript:

```ts
interface Methods {
    $geocodeAddress(argumentsObject: { address: string }): Promise<Location>
}
```

`JsFieldLivewire` declares `$call()`, `$get()`, and `$set()` on `$wire`. Use `$call()` for custom Livewire methods. Other Livewire APIs remain available at runtime; extend the type locally to match your installed Livewire version when using them. In a plugin repository, point the type alias at your development Composer installation.

### Understanding the boundary with Blade and Alpine

Render only inside `host`, whose contents are ignored by Livewire. Do not change its parent or siblings. PHP records, operations, closures, Blade slots, and rendered Filament child schemas are not automatically available inside it. Compute serializable data in `rendererProps()` and keep server-rendered actions, hints, and child schemas outside the host. Filament and Livewire still own field state, validation, authorization, and HTTP responses.

Alpine directives and magic properties are not injected into React, Vue, or Svelte templates. Use the framework's event, reference, and lifecycle APIs, native DOM events, or the supplied `$wire` for Livewire-specific operations. There are no dedicated framework adapters for uploads, actions/modals, or sibling-state subscriptions; access to `$wire` does not automatically make those APIs reactive in your framework. Clean up listeners and subscriptions using their owning API's cleanup mechanism.

### Synchronizing and disposing renderers

Return `update(props)` and `destroy()` from your mount function, or return a promise for that object. Server changes update the renderer and cancel a pending debounce when the incoming value differs from the pending edit. Removal cancels pending commits and calls `destroy()`. A renderer that finishes initializing after removal is immediately destroyed.

Clean up subscriptions, event listeners, and framework roots in `destroy()`, including when the host is already detached. Stop DOM work synchronously even if cleanup returns a promise. Changes to the renderer URL or state binding configuration remount the renderer with current state; changes to `rendererProps()` update it in place. Keep persistent data in the field value, not only in component-local state.

### Handling renderer errors

If importing, mounting, or `update()` fails, Filament clears the host and shows an accessible error message without changing the field value. Console diagnostics identify the phase (`import`, `mount`, `update`, or `cleanup`), state path, renderer, and original error. Cleanup failures are reported without preventing other fields from being disposed. Both thrown errors and rejected lifecycle promises are handled. Filament does not add field values or PHP props to these diagnostics, but your error objects and renderer URLs may contain sensitive information.

Reload the page after correcting the module URL or implementation. If your mount function allocates resources and then throws before returning its lifecycle methods, it must clean up those resources itself. This handling is not a framework error boundary: errors in independently scheduled rendering, event handlers, or detached asynchronous work still need your framework's error handling. If you return a promise from `update()`, manage ordering and cancellation inside the renderer; Filament does not serialize updates or wait for asynchronous cleanup before remounting.

<Aside variant="danger">
    Only use trusted application URLs or `RawJs` code; never let user input choose executable modules or interpolate it into renderer expressions. Renderers are not sandboxed and must comply with your application's CSP and cross-origin policy. Validate and authorize submitted values in PHP, including for disabled or read-only fields.
</Aside>

## Calling field methods from JavaScript

You can call methods on your custom field class from a Blade view or a [JavaScript renderer](#rendering-fields-with-javascript-frameworks). For example, a location picker might call a geocoding service in PHP. Expose the method using `#[ExposedLivewireMethod]`.

### Exposing a method

Add `#[ExposedLivewireMethod]` to a public instance method on your field. This Blade-based example also applies to a class using [`HasJsRenderer`](#building-reusable-plugin-fields):

```php
use Filament\Forms\Components\Field;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;

class LocationPicker extends Field
{
    protected string $view = 'filament.forms.components.location-picker';

    #[ExposedLivewireMethod]
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

<Aside variant="danger">
    Only methods marked with `#[ExposedLivewireMethod]` can be called through these utilities, but exposing a method is not authorization. Validate arguments and authorize record access on the server. Disabled or read-only fields and hidden buttons are not security boundaries.
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

### Calling an exposed method from a framework

In a JavaScript renderer, the exposed methods are available on `utilities`. Call them from an interaction handler and pass the result to `props.onChange()`:

```js
const coordinates = await utilities.$geocodeAddress({ address: '10 Downing Street, London' })

if (coordinates !== null) {
    props.onChange(coordinates)
}
```

You can also call `utilities.$callSchemaComponentMethod('geocodeAddress', { address })`. Handle request failures in your component, and avoid updating it if it has been destroyed while awaiting the response. See [typing renderers](#typing-renderers) to declare named methods in TypeScript.

### Preventing re-renders

By default, calling an exposed method triggers a re-render of the Livewire component. If your method only returns data and does not change state or PHP props that need rendering, add Livewire's `#[Renderless]` attribute. Other pending updates or calls in the same request may still require rendering:

```php
use Filament\Forms\Components\Field;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Livewire\Attributes\Renderless;

class LocationPicker extends Field
{
    protected string $view = 'filament.forms.components.location-picker';

    #[ExposedLivewireMethod]
    #[Renderless]
    public function geocodeAddress(string $address): array
    {
        // ...
    }
}
```
