---
title: Custom components
---

## Introduction

For a one-off component, insert a [Blade view](#inserting-a-blade-view-into-a-schema) into your schema. For a reusable component with its own PHP configuration, generate a class and view:

```bash
php artisan make:filament-schema-component Chart
```

Use the generated class in your schema:

```php
use App\Filament\Schemas\Components\Chart;

Chart::make()
```

Then customize the generated view, following [custom component classes](#custom-component-classes). Most custom components can use Blade and the schema's existing utilities; use a [nested Livewire component](#inserting-a-livewire-component-into-a-schema) when you need a separately managed Livewire component.

### Generating a JavaScript component

Install your application's JavaScript dependencies, including Vite, before running the generator, even with `--skip-install`:

```bash
npm install
```

Add one renderer flag to generate a component using React, Vue, Svelte, or framework-free JavaScript:

```bash
php artisan make:filament-schema-component Chart --react
php artisan make:filament-schema-component Chart --vue
php artisan make:filament-schema-component Chart --svelte
php artisan make:filament-schema-component Chart --js
```

The command creates a PHP component class and a renderer in `resources/js/filament/schemas/components/`, without a Blade view. It installs missing dependencies, configures Vite when possible, and offers to compile your assets. Use the generated class in your schema, then replace the starter paragraph with your own UI.

For TypeScript, add `--typescript` or its alias `--ts`:

```bash
php artisan make:filament-schema-component Chart --react --ts
```

Use a [custom field](../forms/custom-fields) for an input with state binding and validation. JavaScript schema components do not render child schemas; use a Blade component when you need children.

#### Customizing generation

Use `--pm=yarn` to use Yarn, `--skip-install` to install dependencies yourself, or `--skip-build` to skip compilation. Use `--force` or `-F` to overwrite existing files.

The generated PHP class uses `HasJsRenderer` and loads its entry through `Vite::asset()`. The generated files depend on your chosen renderer:

- React: `chart.jsx`, or `chart.tsx` with `--typescript`.
- Vue: `chart.js` and `Chart.vue`. With `--typescript`, the entry uses `.ts` and the component uses `<script setup lang="ts">`.
- Svelte 5: `chart.svelte.js` and `Chart.svelte`. With `--typescript`, the entry uses `.svelte.ts` and the component uses `<script lang="ts">`.
- Framework-free JavaScript: `chart.js`, or `chart.ts` with `--typescript`.

The React component and Vue or Svelte import are named `Chart`, matching the PHP class. Names that start with a lowercase letter or conflict with a starter's imports use the local name `Component` instead. Filenames and the default-exported `mountChart()` function still use the requested name.

Nested names, such as `Reports/Chart`, place JavaScript files in a `reports/` subdirectory.

For a custom component location, the renderer directory mirrors the PHP namespace under `resources/js/`, omitting your application's root namespace and converting each directory to kebab case.

Existing compatible dependencies are preserved; incompatible versions require a manual upgrade. If Vite cannot be configured automatically, follow the printed instructions and the [Vite module setup](../advanced/assets#building-lazy-loaded-es-modules). For a plugin, use a [published module](#building-reusable-plugin-components) instead of the application's Vite manifest.

## Inserting a Blade view into a schema

You may use a "view" component to insert a Blade view into a schema arbitrarily:

```php
use Filament\Schemas\Components\View;

View::make('filament.schemas.components.chart')
```

This assumes that you have a `resources/views/filament/schemas/components/chart.blade.php` file.

You may pass data to this view through the `viewData()` method:

```php
use Filament\Schemas\Components\View;

View::make('filament.schemas.components.chart')
    ->viewData(['data' => $data])
```

### Rendering the component's child schema

You may pass an array of child schema components to the `schema()` method of the component:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\View;

View::make('filament.schemas.components.chart')
    ->schema([
        TextInput::make('subtotal'),
        TextInput::make('total'),
    ])
```

Inside the Blade view, you may render the component's `schema()` using the `$getChildSchema()` function:

```blade
<div>
    {{ $getChildSchema() }}
</div>
```

### Accessing the state of another component in the Blade view

Inside the Blade view, you may access the state of another component in the schema using the `$get()` function:

```blade
<div>
    {{ $get('email') }}
</div>
```

<Aside variant="tip">
    Unless a form field is [reactive](../forms/overview#the-basics-of-reactivity), the Blade view will not refresh when the value of the field changes, only when the next user interaction occurs that makes a request to the server. If you need to react to changes in a field's value, it should be `live()`.
</Aside>

### Accessing the Eloquent record in the Blade view

Inside the Blade view, you may access the current Eloquent record using the `$record` variable:

```blade
<div>
    {{ $record->name }}
</div>
```

### Accessing the current operation in the Blade view

Inside the Blade view, you may access the current operation, usually `create`, `edit` or `view`, using the `$operation` variable:

```blade
<p>
    @if ($operation === 'create')
        This is a new post.
    @else
        This is an existing post.
    @endif
</p>
```

### Accessing the current Livewire component instance in the Blade view

Inside the Blade view, you may access the current Livewire component instance using `$this`:

```blade
@php
    use Filament\Resources\Users\RelationManagers\PostsRelationManager;
@endphp

<p>
    @if ($this instanceof PostsRelationManager)
        You are editing posts the of a user.
    @endif
</p>
```

### Accessing the current component instance in the Blade view

Inside the Blade view, you may access the current component instance using `$schemaComponent`. You can call public methods on this object to access other information that may not be available in variables:

```blade
<p>
    @if ($schemaComponent->getState())
        This is a new post.
    @endif
</p>
```

## Inserting a Livewire component into a schema

You may insert a Livewire component directly into a schema:

```php
use App\Livewire\Chart;
use Filament\Schemas\Components\Livewire;

Livewire::make(Chart::class)
```

<Aside variant="info">
    When inserting a Livewire component into the schema, there are limited capabilities. Only serializable data is accessible from the nested Livewire component, since they are rendered separately. As such, you can't [render a child schema](#rendering-the-components-child-schema), [access another component's live state](#accessing-the-state-of-another-component-in-the-blade-view), [access the current Livewire component instance](#accessing-the-current-livewire-component-instance-in-the-blade-view), or [access the current component instance](#accessing-the-current-component-instance-in-the-blade-view). Only [static data that you pass to the Livewire component](#passing-parameters-to-a-livewire-component), and [the current record](#accessing-the-current-record-in-the-livewire-component) are accessible. Situations where you should render a nested Livewire component instead of a [Blade view](#inserting-a-blade-view-into-a-schema) are rare because of these limitations.
</Aside>

<Aside variant="warning">
    A custom Livewire component does not inherit the parent component's model scoping or authorization. Re-query and authorize models in its public properties, including a record passed into the component. Do the same for model arguments passed to a lazy component's `mount()` method. Filament components such as widgets still run their own documented checks. See [Eloquent model restoration in Livewire](../advanced/security#understanding-eloquent-model-restoration-in-livewire) for more information.
</Aside>

If you are rendering multiple of the same Livewire component, please make sure to pass a unique `key()` to each:

```php
use App\Livewire\Chart;
use Filament\Schemas\Components\Livewire;

Livewire::make(Chart::class)
    ->key('chart-first')

Livewire::make(Chart::class)
    ->key('chart-second')

Livewire::make(Chart::class)
    ->key('chart-third')
```

### Passing parameters to a Livewire component

You can pass an array of parameters to a Livewire component:

```php
use App\Livewire\Chart;
use Filament\Schemas\Components\Livewire;

Livewire::make(Chart::class, ['bar' => 'baz'])
```

<UtilityInjection set="schemaComponents">As well as allowing a static value, the `make()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

Now, those parameters will be passed to the Livewire component's `mount()` method:

```php
class Chart extends Component
{
    public function mount(string $bar): void
    {       
        // ...
    }
}
```

Alternatively, they will be available as public properties on the Livewire component:

```php
class Chart extends Component
{
    public string $bar;
}
```

For server-provided configuration that should not be editable, add [`#[Locked]`](../advanced/security#protecting-server-managed-component-state) to the property.

#### Accessing the current record in the Livewire component

You can access the current record in the Livewire component using the `$record` parameter in the `mount()` method, or the `$record` property:

```php
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;

class Chart extends Component
{
    public function mount(?Model $record = null): void
    {       
        // ...
    }
    
    // or
    
    #[Locked]
    public ?Model $record = null;
}
```

Please be aware that when the record has not yet been created, it will be `null`. If you'd like to hide the Livewire component when the record is `null`, you can use the `hidden()` method:

```php
use Filament\Schemas\Components\Livewire;
use Illuminate\Database\Eloquent\Model;

Livewire::make(Chart::class)
    ->hidden(fn (?Model $record): bool => $record === null)
```

### Lazy loading a Livewire component

You may allow the component to [lazily load](https://livewire.laravel.com/docs/lazy#rendering-placeholder-html) when it enters the viewport using the `lazyLoaded()` method:

```php
use Filament\Schemas\Components\Livewire;
use App\Livewire\Chart;

Livewire::make(Chart::class)
    ->lazyLoaded()
```

## Custom component classes

You may create your own custom component classes and views, which you can reuse across your project, and even release as a plugin to the community.

<Aside variant="tip">
    If you're just creating a simple custom component to use once, you could instead use a [view component](#inserting-a-blade-view-into-a-schema) to render any custom Blade file.
</Aside>

To create a custom component class and view, you may use the following command:

```bash
php artisan make:filament-schema-component Chart
```

This will create the following component class:

```php
use Filament\Schemas\Components\Component;

class Chart extends Component
{
    protected string $view = 'filament.schemas.components.chart';

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }
}
```

It will also create a view file at `resources/views/filament/schemas/components/chart.blade.php`.

You may use the same utilities as you would when [inserting a Blade view into a schema](#inserting-a-blade-view-into-a-schema) to [render the component's child schema](#rendering-the-components-child-schema), [access another component's live state](#accessing-the-state-of-another-component-in-the-blade-view), [access the current Eloquent record](#accessing-the-eloquent-record-in-the-blade-view), [access the current operation](#accessing-the-current-operation-in-the-blade-view), [access the current Livewire component instance](#accessing-the-current-livewire-component-instance-in-the-blade-view), and [access the current component instance](#accessing-the-current-component-instance-in-the-blade-view).

<Aside variant="info">
    Filament schema components are **not** Livewire components. Defining public properties and methods on a schema component class will not make them accessible in the Blade view.
</Aside>

### Adding a configuration method to a custom component class

You may add a public method to the custom component class that accepts a configuration value, stores it in a protected property, and returns it again from another public method:

```php
use Filament\Schemas\Components\Component;

class Chart extends Component
{
    protected string $view = 'filament.schemas.components.chart';
    
    protected ?string $heading = null;

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function heading(?string $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getHeading(): ?string
    {
        return $this->heading;
    }
}
```

Now, in the Blade view for the custom component, you may access the heading using the `$getHeading()` function:

```blade
<div>
    {{ $getHeading() }}
</div>
```

Any public method that you define on the custom component class can be accessed in the Blade view as a variable function in this way.

To pass the configuration value to the custom component class, you may use the public method:

```php
use App\Filament\Schemas\Components\Chart;

Chart::make()
    ->heading('Sales')
```

#### Allowing utility injection in a custom component configuration method

[Utility injection](overview#component-utility-injection) is a powerful feature of Filament that allows users to configure a component using functions that can access various utilities. You can allow utility injection by ensuring that the parameter type and property type of the configuration allows the user to pass a `Closure`. In the getter method, you should pass the configuration value to the `$this->evaluate()` method, which will inject utilities into the user's function if they pass one, or return the value if it is static:

```php
use Closure;
use Filament\Schemas\Components\Component;

class Chart extends Component
{
    protected string $view = 'filament.schemas.components.chart';
    
    protected string | Closure | null $heading = null;

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function heading(string | Closure | null $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getHeading(): ?string
    {
        return $this->evaluate($this->heading);
    }
}
```

Now, you can pass a static value or a function to the `heading()` method, and [inject any utility](overview#component-utility-injection) as a parameter:

```php
use App\Filament\Schemas\Components\Chart;

Chart::make()
    ->heading(fn (Product $record): string => "{$record->name} Sales")
```

### Accepting a configuration value in the constructor of a custom component class

You may accept a configuration value in the `make()` constructor method of the custom component and pass it to the corresponding setter method:

```php
use Closure;
use Filament\Schemas\Components\Component;

class Chart extends Component
{
    protected string $view = 'filament.schemas.components.chart';

    protected string | Closure | null $heading = null;

    public function __construct(string | Closure | null $heading = null)
    {
        $this->heading($heading);
    }

    public static function make(string | Closure | null $heading = null): static
    {
        $static = app(static::class, ['heading' => $heading]);
        $static->configure();

        return $static;
    }

    public function heading(string | Closure | null $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getHeading(): ?string
    {
        return $this->evaluate($this->heading);
    }
}
```

### Calling component methods from JavaScript

Sometimes you need to call a method on the component class from JavaScript in the Blade view. For example, you might want to fetch data asynchronously or perform some server-side computation. Filament provides a way to expose methods on your component class to JavaScript using the `#[Exposed]` attribute.

#### Exposing a method

To expose a method to JavaScript, add the `#[Exposed]` attribute to a public method on your custom component class:

```php
use Filament\Schemas\Components\Component;
use Filament\Support\Components\Attributes\Exposed;

class Chart extends Component
{
    protected string $view = 'filament.schemas.components.chart';

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    #[Exposed]
    public function getChartData(): array
    {
        // Fetch and process chart data...

        return $chartData;
    }
}
```

<Aside variant="warning">
    Only methods marked with `#[Exposed]` can be called from JavaScript. The attribute does not authorize access to the method's data or operations. Validate client-supplied arguments and [authorize sensitive operations](../advanced/security#authorization) inside the method.

    Exposed methods remain callable when the component is [disabled](../forms/overview#disabling-a-field), since read methods may still be needed to display its value. If a method changes the component's state and should respect its disabled state, check `$this->isDisabled()` before making changes.
</Aside>

#### Calling the method from JavaScript

Give your component a unique `key()` when adding it to a schema so Filament can locate it when the method is called:

```php
use App\Filament\Schemas\Components\Chart;

Chart::make()->key('chart')
```

In your Blade view or embedded HTML, call the exposed public instance method using its `$`-prefixed name. The utility is bound to this schema component, so you do not need to pass its key:

```blade
<div
    x-data="{
        data: null,
        async loadData() {
            this.data = await this.$getChartData()
        },
    }"
    x-init="loadData"
>
    <template x-if="data">
        {{-- Render the chart using the data --}}
    </template>
</div>
```

You can also use `$callSchemaComponentMethod('getChartData')`. Built-in utility and Alpine magic names are reserved; methods with colliding names remain available through the general utility. Avoid registering your own Alpine magics with the same names as exposed methods.

#### Passing arguments to an exposed method

Pass arguments as an object keyed by PHP parameter name:

```blade
<div
    x-data="{
        data: null,
        dateRange: 'week',
        async loadData() {
            this.data = await this.$getChartData(
                { dateRange: this.dateRange },
            )
        },
    }"
    x-init="loadData"
>
    <select x-model="dateRange" x-on:change="loadData">
        <option value="week">This Week</option>
        <option value="month">This Month</option>
        <option value="year">This Year</option>
    </select>

    <template x-if="data">
        {{-- Render the chart using the data --}}
    </template>
</div>
```

#### Preventing re-renders

By default, calling an exposed method will trigger a re-render of the Livewire component. If your method doesn't need to update the UI, you may add Livewire's `#[Renderless]` attribute alongside `#[Exposed]` to skip the re-render:

```php
use Filament\Schemas\Components\Component;
use Filament\Support\Components\Attributes\Exposed;
use Livewire\Attributes\Renderless;

class Chart extends Component
{
    protected string $view = 'filament.schemas.components.chart';

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    #[Exposed]
    #[Renderless]
    public function getChartData(): array
    {
        // ...
    }
}
```

### Styling components outside the container grid

Components using `liberatedFromContainerGrid()` receive a `.fi-sc-liberated` Alpine scope wrapper with `display: contents`, so their contents still participate in the surrounding layout. If your [custom CSS](../styling/css-hooks) targets their output as a direct child of `.fi-sc`, include `.fi-sc > .fi-sc-liberated > ...` in that selector too. The wrapper provides the scope for [calling component methods](#calling-component-methods-from-javascript), but does not generate its own layout box.

## Rendering components with JavaScript frameworks

After [generating a JavaScript component](#generating-a-javascript-component), customize its renderer. Your application owns the renderer and its dependencies; Filament does not bundle a framework.

You can also use `JsComponent` directly without generating a PHP class. Pass the URL of your renderer's ES module to `renderer()`:

```php
use Filament\Schemas\Components\JsComponent;
use Illuminate\Support\Facades\Vite;

JsComponent::make()
    ->key('sales-summary')
    ->renderer(Vite::asset('resources/js/components/sales-summary.jsx'))
    ->rendererConfiguration(['message' => 'Sales increased by 12% this quarter.'])
```

Give each instance a unique `key()`. Instances can share a renderer without sharing their local state.

<Aside variant="danger">
    Only use trusted renderer URLs or `RawJs` code. Never let user input choose modules or generate executable code. Renderers are not sandboxed and must comply with your application's CSP and cross-origin policy. Validate and authorize requests in PHP.
</Aside>

<UtilityInjection set="schemaComponents">As well as allowing a static value, the `renderer()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

If you already load the renderer through another script, you may pass a trusted `Filament\Support\RawJs` function expression to `renderer()` instead of a URL.

### Writing a renderer

Each renderer exports a mount function receiving `host`, `props`, and schema `utilities`. Render inside `host`, then return `update(props)` and `destroy()` methods. Props contain `configuration`, your PHP configuration, and `id`, the component's nullable ID.

If you create the files manually, follow the [Vite module setup](../advanced/assets#building-lazy-loaded-es-modules) to compile the entry file. The examples leave styling to your application; see [registering CSS files](../advanced/assets#registering-css-files).

#### Creating a React component

Create `resources/js/components/sales-summary.jsx`:

```jsx
import React from 'react'
import { createRoot } from 'react-dom/client'

function SalesSummary(props) {
    return <p>{props.configuration.message}</p>
}

export default function mountSalesSummary({ host, props: initialProps }) {
    const root = createRoot(host)
    const update = (props) => root.render(<SalesSummary {...props} />)

    update(initialProps)

    return { update, destroy: () => root.unmount() }
}
```

#### Creating a Vue component

Create `resources/js/components/SalesSummary.vue`:

```vue
<script setup>
defineOptions({ inheritAttrs: false })

const props = defineProps(['id', 'configuration'])
</script>

<template>
    <p>{{ props.configuration.message }}</p>
</template>
```

Create `resources/js/components/sales-summary.js` to mount it. Use this entry's URL in `renderer()`:

```js
import { createApp, h, shallowRef } from 'vue'
import SalesSummary from './SalesSummary.vue'

export default function mountSalesSummary({ host, props: initialProps }) {
    const props = shallowRef(initialProps)
    const application = createApp({
        setup: () => () => h(SalesSummary, props.value),
    })

    application.mount(host)

    return {
        update(nextProps) {
            props.value = nextProps
        },
        destroy: () => application.unmount(),
    }
}
```

#### Creating a Svelte component

For Svelte 5, create `resources/js/components/SalesSummary.svelte`:

```svelte
<script>
    let { configuration } = $props()
</script>

<p>{configuration.message}</p>
```

Create `resources/js/components/sales-summary.svelte.js` to mount it. The `.svelte.js` extension allows the Svelte compiler to process `$state`. Use this entry's URL in `renderer()`:

```js
import { mount, unmount } from 'svelte'
import SalesSummary from './SalesSummary.svelte'

export default function mountSalesSummary({ host, props: initialProps }) {
    const props = $state({ ...initialProps })
    const component = mount(SalesSummary, { target: host, props })

    return {
        update: (nextProps) => Object.assign(props, nextProps),
        destroy: () => unmount(component),
    }
}
```

#### Creating a framework-free JavaScript component

Create `resources/js/components/sales-summary.js`:

```js
export default function mountSalesSummary({ host, props: initialProps }) {
    const paragraph = document.createElement('p')
    const update = (props) => {
        paragraph.textContent = props.configuration.message ?? ''
    }

    update(initialProps)
    host.append(paragraph)

    return { update, destroy: () => paragraph.remove() }
}
```

### Passing configuration from PHP

Use `rendererConfiguration()` to supply JSON-serializable data for the browser, not secrets. Read it from `props.configuration` and treat it as read-only; modifying it does not write back to PHP.

<UtilityInjection set="schemaComponents">As well as allowing a static value, the `rendererConfiguration()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

For example, configure your renderer using another field's state:

```php
use Filament\Schemas\Components\JsComponent;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Vite;

JsComponent::make()
    ->key('sales-summary')
    ->renderer(Vite::asset('resources/js/components/sales-summary.jsx'))
    ->rendererConfiguration(static fn (Get $get): array => [
        'message' => $get('summary'),
    ])
```

When this component renders on the server, Filament passes changed configuration to `update(props)` without remounting it. Use [`live()`](../forms/overview#the-basics-of-reactivity) on the source field to trigger updates; deferred edits wait for the next render. Requests that skip this component, including partial renders of other components, do not update its configuration.

Pass `null` to `rendererConfiguration()` to clear it. For a reusable component using `HasJsRenderer`, override `getRendererConfiguration(): array` instead; its default is `[]`.

### Accessing schema utilities

Accept `utilities` alongside `host` and `props` in your mount function, then pass it to your framework component as a prop. You can read or change sibling fields from an interaction handler:

```js
const country = utilities.$get('country')
await utilities.$set('country', 'GB')
```

`$get(path, isAbsolute)` and `$set(path, value, isAbsolute, isLive = false)` use paths relative to the component's container unless `isAbsolute` is `true`. `$set()` is deferred unless `isLive` is `true`. These utilities do not apply a target field's read-only configuration or state binding modifiers.

`utilities.$statePath` is the surrounding scope's absolute state path, or `null`. Read `utilities.$state` when needed and treat it as read-only. Neither it nor `$get()` subscribes your framework to changes; use [PHP configuration](#passing-configuration-from-php) for reactive values.

Keep `utilities` intact when passing it into framework state: spreading or destructuring it captures the current values of its `$state` and `$wire` getters. Functions such as `$get` and `$set` may be destructured, but alias them in Svelte, where local names beginning with `$` are reserved.

#### Calling PHP methods

Call [exposed component methods](#calling-component-methods-from-javascript) through `utilities.$getChartData({ dateRange: 'week' })` or `utilities.$callSchemaComponentMethod('getChartData', { dateRange: 'week' })`. The same exposure and authorization rules apply as in Blade.

For methods on the owning Livewire component, use `utilities.$wire.$call('methodName')`. Handle request failures and stop using utilities when the renderer is destroyed.

### Typing renderers

Use `--typescript` or `--ts` to generate a typed renderer and configure the `@filament/schemas/js-component` alias. See [type-checking ES modules](../advanced/assets#type-checking-es-modules) for compiler versions, React JSX configuration, and framework-specific type checks.

Filament includes declarations in its Composer package, without a JavaScript runtime dependency. For an existing renderer, add a type alias to your application's `tsconfig.json`, relative to that file or its `baseUrl`:

```json
{
    "compilerOptions": {
        "paths": {
            "@filament/schemas/js-component": ["./vendor/filament/schemas/resources/js/types/js-component.d.ts"]
        }
    }
}
```

Use `JsComponentRenderer<Configuration, Methods>` to type your PHP configuration and exposed methods:

```ts
import type { JsComponentRenderer } from '@filament/schemas/js-component'

type Configuration = { message: string }
interface Methods {
    $getChartData(argumentsObject: { dateRange: string }): Promise<number[]>
}

const mountSalesSummary: JsComponentRenderer<Configuration, Methods> = ({ host, props, utilities }) => {
    const paragraph = document.createElement('p')
    paragraph.textContent = props.configuration.message
    host.append(paragraph)

    return {
        update(nextProps) {
            paragraph.textContent = nextProps.configuration.message
        },
        destroy() {
            paragraph.remove()
        },
    }
}

export default mountSalesSummary
```

The same module exports `JsComponentRendererContext`, `JsComponentProps`, `JsComponentUtilities`, `JsComponentLivewire`, and `JsComponentRendererInstance`. Use `import type`. Configuration is deeply read-only in TypeScript, not frozen at runtime.

Declare PHP method signatures yourself. `$wire` includes `$call()`, `$get()`, and `$set()`; extend its type locally for other Livewire APIs.

### Synchronizing and disposing renderers

The mount function may return its lifecycle methods directly or through a promise. Configuration changes call `update(props)`; renderer URL or ID changes remount the component. Removal calls `destroy()`, including when mounting finishes after removal.

Clean up subscriptions, event listeners, and framework roots in `destroy()`, including when the host is already detached. Stop DOM work synchronously even if cleanup returns a promise. Filament does not serialize updates or wait for asynchronous cleanup before remounting; order or cancel asynchronous work inside your renderer.

#### Understanding the boundary with Blade and Alpine

Livewire ignores `host` contents. Render only inside it, and keep child schemas and server-rendered actions outside the component. Pass serializable PHP data through `rendererConfiguration()`; Blade slots and PHP objects are not available in framework templates.

Use framework events or the supplied utilities, not Alpine directives or magic properties. Access to `$wire` does not automatically integrate uploads, actions, modals, or reactive state into your framework.

#### Handling renderer errors

If importing, mounting, or `update()` throws or rejects, Filament clears the host and shows an accessible error message. Check the console for the phase, state path, renderer, and error, then reload after correcting it. Error objects and renderer URLs may contain sensitive information.

Clean up resources yourself if mounting fails before returning lifecycle methods. Filament logs cleanup failures without blocking other components, but does not provide a framework error boundary: handle errors in event handlers and independently scheduled work in your renderer.

### Customizing the Blade view

A component using `HasJsRenderer` does not need a Blade view, but you can set its `$view` property or call `view()` to render a custom view instead:

```php
use App\Filament\Schemas\Components\Chart;

Chart::make()
    ->key('chart')
    ->view('filament.schemas.components.chart')
```

The custom view replaces the default output; it does not automatically mount the JavaScript renderer. To keep the renderer inside your own Blade layout, render the component's `toEmbeddedHtml()` output in `resources/views/filament/schemas/components/chart.blade.php`:

```blade
<div>
    {!! $schemaComponent->toEmbeddedHtml() !!}
</div>
```

Keep any additional Blade content outside the renderer's `host`.

### Building reusable plugin components

Extend `Component` and use `Filament\Schemas\Components\Concerns\HasJsRenderer` for a reusable component. Implement `getRenderer()` and optionally `getRendererConfiguration()`. You do not need a Blade view or to extend `JsComponent`:

```php
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasJsRenderer;
use Filament\Support\Facades\FilamentAsset;

class SalesSummary extends Component
{
    use HasJsRenderer;

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function getRenderer(): string
    {
        return FilamentAsset::getScriptSrc('sales-summary', 'acme/reports');
    }

    public function getRendererConfiguration(): array
    {
        return ['message' => 'Sales increased by 12% this quarter.'];
    }
}
```

Follow the [plugin guide](../plugins/getting-started#creating-a-plugin) and [publish the module](../advanced/assets#publishing-es-modules-in-plugins) under this asset ID and package name. Consumers can use `SalesSummary::make()->key('sales-summary')` without configuring Vite. Expose PHP configuration through the usual [fluent methods](#adding-a-configuration-method-to-a-custom-component-class).
