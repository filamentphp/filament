---
title: Custom fields
---

## View fields

Aside from [building custom fields](custom), you may create "view" fields which allow you to create custom fields without extra PHP classes.

```php
use Filament\Forms\Components\ViewField;

ViewField::make('rating')->view('filament.forms.components.range-slider')
```

This assumes that you have a `resources/views/filament/forms/components/range-slider.blade.php` file.

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

Imagine you had a Livewire component with a public property called `$name`. You could bind that property to an input field in the HTML of the Livewire component in one of two ways: by a the [`wire:model` attribute](https://laravel-livewire.com/docs/properties#data-binding), or by [entangling](https://laravel-livewire.com/docs/2.x/alpine-js#sharing-state) it with an Alpine.js property:

```blade
<input wire:model.defer="name" />

<!-- Or -->

<div x-data="{ state: $wire.entangle('name').defer }">
    <input x-model="state" />
</div>
```

When the user types into the input field, the `$name` property is updated in the Livewire component class. When the user submits the form, the `$name` property is sent to the server, where it can be saved.

This is the basis of how fields work in Filament. Each field is assigned to a public property in the Livewire component class, which is where the state of the field is stored. We call the name of this property the "state path" of the field. You can access the state path of a field using the `$getStatePath()` function in the field's view:

```blade
<input wire:model.defer="{{ $getStatePath() }}" />

<!-- Or -->

<div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }">
    <input x-model="state" />
</div>
```

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

However, you may use the [`reactive()`](../advanced#the-basics-of-reactivity) on a field to ensure that the state is sent to the server immediately when the user interacts with the field. This allows for lots of advanced use cases as explained in the [advanced](../advanced) section of the documentation.

Filament provides a `$applyStateBindingModifiers()` function that you may use in your view to apply any state binding modifiers to a `wire:model` or `$wire.entangle()` binding:

```blade
<input {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}" />

<!-- Or -->

<div x-data="{ state: $wire.{{ $applyStateBindingModifiers("entangle('{$getStatePath()}')") }} }">
    <input x-model="state" />
</div>
```

## Asynchronously loading Alpine.js components

Sometimes, you may want to load external JavaScript libraries for your Alpine.js-based components. The best way to do this is by storing the compiled JavaScript and Alpine component in a separate file, and letting us load it whenever the component is rendered.

Firstly, you should install [esbuild](https://esbuild.github.io) via NPM, which we will use to create a single JavaScript file containing your external library and Alpine component:

```bash
npm install esbuild --save-dev
```

Then, you must create a script to compile your JavaScript and Alpine component. You can put this anywhere, for example `bin/build.mjs`:

```js
import * as esbuild from 'esbuild'

const isDev = process.argv.includes('--dev')

async function compile(options) {
    const context = await esbuild.context(options)

    if (isDev) {
        await context.watch()
    } else {
        await context.rebuild()
        await context.dispose()
    }
}

const defaultOptions = {
    define: {
        'process.env.NODE_ENV': isDev ? `'development'` : `'production'`,
    },
    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    sourcemap: isDev ? 'inline' : false,
    sourcesContent: isDev,
    treeShaking: true,
    target: ['es2020'],
    minify: !isDev,
    plugins: [{
        name: 'watchPlugin',
        setup: function (build) {
            build.onStart(() => {
                console.log(`Build started at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
            })

            build.onEnd((result) => {
                if (result.errors.length > 0) {
                    console.log(`Build failed at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`, result.errors)
                } else {
                    console.log(`Build finished at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
                }
            })
        }
    }],
}

compile({
    ...defaultOptions,
    entryPoints: ['./resources/js/components/test-component.js'],
    outfile: './resources/js/dist/components/test-component.js',
})
```

As you can see at the bottom of the script, we are compiling a file called `resources/js/components/test-component.js` into `resources/js/dist/components/test-component.js`. You can change these paths to suit your needs. You can compile as many components as you want.

Now, create a new file called `resources/js/components/test-component.js`:

```js
// Import any external JavaScript libraries from NPM here.

export default function testComponent({
    state,
}) {
    return {
        state,
        
        // You can define any other Alpine.js properties here.

        init: function () {
            // Initialise the Alpine component here, if you need to.
        },
        
        // You can define any other Alpine.js functions here.
    }
}
```

Now, you can compile this file into `resources/js/dist/components/test-component.js` by running the following command:

```bash
node bin/build.mjs
```

If you want to watch for changes to this file instead of compiling once, try the following command:

```bash
node bin/build.mjs --dev
```

Now, you need to tell Filament to publish this compiled JavaScript file into the `/public` directory of the Laravel application, so it is accessible to the browser. You can do this by using the `FilamentAsset` facade in the `boot()` method of a service provider class:

```php
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;

FilamentAsset::register([
    AlpineComponent::make('test-component', resource_path('js/dist/components/test-component.js'),
]);
```

When you run `php artisan filament:assets`, the compiled file will be copied into the `/public` directory.

Finally, you can load this asynchronous Alpine component in your view using `ax-load` attributes and the `FilamentAsset::getAlpineComponentSrc()` method:

```blade
<div
    x-ignore
    ax-load
    ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('test-component') }}"
    x-data="testComponent({
        state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')") }},
    })"
>
    <input x-model="state" />
</div>
```

The `ax-load` attributes come from the [Async Alpine](https://async-alpine.dev/docs/strategies) package, and any features of that package can be used here.
