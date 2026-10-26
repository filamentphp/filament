---
title: Custom widgets
---

## Introduction

You can create a reusable widget with a Blade view, React, Vue, Svelte, or framework-free JavaScript. Start by generating a widget, then customize its view or renderer. Filament handles the widget's grid placement and lazy loading.

To generate a Blade-based widget:

```bash
php artisan make:filament-widget RevenueOverview
```

Choose **Custom**, then edit the generated view. Widgets are Livewire components, so you can use public properties and methods in the view through `$this` and `$wire`.

### Generating a JavaScript widget

Install your application's JavaScript dependencies, including Vite, before running the generator, for example with `npm install`.

Add one renderer flag to the command:

```bash
php artisan make:filament-widget RevenueOverview --react
php artisan make:filament-widget RevenueOverview --vue
php artisan make:filament-widget RevenueOverview --svelte
php artisan make:filament-widget RevenueOverview --js
```

The command creates a PHP widget class and a renderer, normally in `resources/js/filament/widgets/`, instead of a Blade view. Replace the starter content with your own UI, which appears inside a standard Filament widget section.

The command installs missing dependencies, configures Vite where possible, and offers to compile your assets. Existing dependencies are preserved; incompatible versions require a manual upgrade. If your Vite configuration needs manual changes, follow the command's instructions and the [Vite module setup](../advanced/assets#building-lazy-loaded-es-modules).

For TypeScript, add `--typescript` or its alias `--ts`:

```bash
php artisan make:filament-widget RevenueOverview --react --ts
```

Register or discover the generated class in your panel like any other widget. You can also use it as a [page widget](../navigation/custom-pages#adding-widgets-to-pages) or a [resource widget](../resources/widgets).

#### Customizing generation

You can customize the command with these options:

- `--pm=yarn` uses Yarn instead of npm.
- `--skip-install` skips dependency installation. Vite must already be installed.
- `--skip-build` skips the compilation prompt.

The panel, resource, cluster, and `--force` options still apply. Renderer flags cannot be combined with `--chart`, `--stats-overview`, or `--table`.

The generated files depend on your chosen renderer:

- React: `revenue-overview.jsx`, or `revenue-overview.tsx` with `--typescript`.
- Vue: `revenue-overview.js` and `RevenueOverview.vue`. With `--typescript`, the entry uses `.ts` and the component uses `<script setup lang="ts">`.
- Svelte 5: `revenue-overview.svelte.js` and `RevenueOverview.svelte`. With `--typescript`, the entry uses `.svelte.ts` and the component uses `<script lang="ts">`.
- Framework-free JavaScript: `revenue-overview.js`, or `revenue-overview.ts` with `--typescript`.

The React component and Vue or Svelte import are named `RevenueOverview`, matching the PHP class. Names that start with a lowercase letter or conflict with a starter's imports use the local name `Component` instead. Filenames and the default-exported `mountRevenueOverview()` function still use the requested name.

Renderer directories follow the PHP namespace in kebab case, excluding your application's root namespace. For example, `App\Filament\Resources\Orders\Widgets\RevenueOverview` uses `resources/js/filament/resources/orders/widgets/`.

<Aside variant="info">
    Importing the renderer does not load CSS extracted by Vite. Include those styles through your application's stylesheet or [register them separately](../advanced/assets#registering-css-files).
</Aside>

## Passing configuration from PHP

Use `getRendererConfiguration()` to return JSON-serializable data. The renderer receives this data as `props.configuration`:

```php
namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\Concerns\HasJsRenderer;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Vite;

class RevenueOverview extends Widget
{
    use HasJsRenderer;

    public function getRenderer(): string
    {
        return Vite::asset('resources/js/filament/widgets/revenue-overview.js');
    }

    public function getRendererConfiguration(): array
    {
        return [
            'heading' => 'Revenue overview',
            'description' => 'Total revenue: ' . Order::query()->sum('total'),
        ];
    }
}
```

Use the entry extension generated for your framework. When the widget re-renders, Filament passes changed configuration to the renderer's `update()` method without remounting it. Changing `props.configuration` in JavaScript does not update PHP.

<Aside variant="danger">
    Only include data that the current user is authorized to see.
</Aside>

### Receiving dashboard filter values

Add `InteractsWithPageFilters` to read the dashboard's reactive `$pageFilters`, just as you would in any other widget. Include the filter values, or data calculated from them, in `getRendererConfiguration()`:

```php
use Filament\Widgets\Concerns\InteractsWithPageFilters;

// Inside your widget class:
use InteractsWithPageFilters;

public function getRendererConfiguration(): array
{
    return [
        'heading' => 'Revenue overview',
        'description' => 'Period starts: ' . ($this->pageFilters['startDate'] ?? 'All time'),
        'filters' => $this->pageFilters,
    ];
}
```

Set up the dashboard's [filters form or filter action](overview#filtering-widget-data) as usual. Only pass the filter values the renderer needs, and validate live form filters before using them in a query.

For other parent-provided properties, use Livewire's `#[Reactive]` attribute to trigger widget updates when they change.

## Writing a renderer

A renderer module default-exports a function receiving `{ host, props, utilities }`. Mount your UI inside `host` and return an object with `update(props)` and `destroy()` methods. The generated starters demonstrate each framework's lifecycle.

### Creating a framework-free JavaScript widget

```javascript
export default function mountRevenueOverview({ host, props: initialProps }) {
    const heading = document.createElement('h2')
    const description = document.createElement('p')

    const update = ({ configuration }) => {
        heading.textContent = configuration.heading
        description.textContent = configuration.description
    }

    update(initialProps)
    host.append(heading, description)

    return { update, destroy: () => host.replaceChildren() }
}
```

### Creating a React widget

```jsx
import React from 'react'
import { createRoot } from 'react-dom/client'

function RevenueOverview({ configuration }) {
    return <><h2>{configuration.heading}</h2><p>{configuration.description}</p></>
}

export default function mountRevenueOverview({ host, props: initialProps }) {
    const root = createRoot(host)
    const update = (props) => root.render(<RevenueOverview {...props} />)
    update(initialProps)

    return { update, destroy: () => root.unmount() }
}
```

### Creating a Vue widget

Use a shallow ref to replace the PHP props without deeply proxying them:

```javascript
import { createApp, h, shallowRef } from 'vue'
import RevenueOverview from './RevenueOverview.vue'

export default function mountRevenueOverview({ host, props: initialProps }) {
    const props = shallowRef(initialProps)
    const application = createApp({ setup: () => () => h(RevenueOverview, props.value) })
    application.mount(host)

    return {
        update: (nextProps) => { props.value = nextProps },
        destroy: () => application.unmount(),
    }
}
```

In `RevenueOverview.vue`:

```vue
<script setup>
defineProps(['configuration'])
</script>

<template>
    <h2>{{ configuration.heading }}</h2>
    <p>{{ configuration.description }}</p>
</template>
```

### Creating a Svelte widget

Use Svelte 5's reactive state in the `.svelte.js` entry:

```javascript
import { mount, unmount } from 'svelte'
import RevenueOverview from './RevenueOverview.svelte'

export default function mountRevenueOverview({ host, props: initialProps }) {
    const props = $state({ ...initialProps })
    const component = mount(RevenueOverview, { target: host, props })

    return {
        update: (nextProps) => { Object.assign(props, nextProps) },
        destroy: () => unmount(component),
    }
}
```

In `RevenueOverview.svelte`:

```svelte
<script>
    let { configuration } = $props()
</script>

<h2>{configuration.heading}</h2>
<p>{configuration.description}</p>
```

### Calling PHP methods

`utilities.$wire` is the widget's own Livewire proxy. Use it to call a public method, read a property, or update a property:

```javascript
export default function mountRevenueOverview({ host, props, utilities }) {
    const button = document.createElement('button')
    button.type = 'button'
    button.textContent = 'Refresh revenue'
    button.onclick = () => utilities.$wire.$call('refreshRevenue')
    host.append(button)

    return { update() {}, destroy: () => host.replaceChildren() }
}
```

Define `refreshRevenue()` on your widget class. Validate browser-provided arguments and authorize operations inside PHP methods. To call PHP from a React, Vue, or Svelte component, pass `utilities` to it as a prop.

### Typing renderers

The generator adds the `@filament/widgets/js-widget` type alias to standard JSON `tsconfig.json` files. To configure it manually, add this mapping, adjusting the path relative to `baseUrl` if set:

```json
{
    "compilerOptions": {
        "paths": {
            "@filament/widgets/js-widget": [
                "./vendor/filament/widgets/resources/js/types/js-widget.d.ts"
            ]
        }
    }
}
```

`JsWidgetRenderer<Configuration, Methods>` types the PHP configuration and optional public widget methods. `JsWidgetProps<Configuration>`, `JsWidgetRendererContext<Configuration, Methods>`, and `JsWidgetRendererInstance<Configuration>` are also available:

```typescript
import type { JsWidgetRenderer } from '@filament/widgets/js-widget'

type Configuration = { heading: string; total: number }
type Methods = { refreshRevenue(): Promise<void> }

const mountRevenueOverview: JsWidgetRenderer<Configuration, Methods> = ({ host, props, utilities }) => {
    const update = ({ configuration }: typeof props) => {
        host.textContent = `${configuration.heading}: ${configuration.total}`
    }
    update(props)

    return { update, destroy: () => host.replaceChildren() }
}

export default mountRevenueOverview
```

Vite builds do not check types. See [type-checking ES modules](../advanced/assets#type-checking-es-modules) for compiler setup and framework-specific commands.

### Synchronizing and disposing renderers

Livewire does not manage the contents of `host`. Use `update()` to apply PHP configuration while preserving local UI state. Do not render Blade, Livewire, or Alpine components inside this subtree.

Mounting may be asynchronous: Filament delivers the latest props when it completes, or disposes the instance if the widget was removed. Changing `getRenderer()` replaces the renderer; changing `getRendererConfiguration()` updates it in place.

Use `destroy()` to unmount your framework and release listeners, observers, timers, and subscriptions. Stop DOM work immediately: Filament does not wait for asynchronous cleanup before remounting. Cancel or order overlapping asynchronous updates in your renderer.

#### Handling renderer errors

If importing, mounting, or updating fails, Filament logs the error, disposes the renderer, and replaces its contents with an accessible error message. Reload or remount to retry.

If mounting allocates resources before throwing, clean them up in the mount function: Filament has not received an instance to dispose yet.

### Customizing the Blade view

Widgets using `HasJsRenderer` use `filament-widgets::js-widget` by default. To use a custom Blade view instead, set `$view` in your widget class:

```php
protected string $view = 'filament.widgets.revenue-overview';
```

The custom view replaces the default output; it does not automatically mount the JavaScript renderer. To keep the renderer, include the shared view in `resources/views/filament/widgets/revenue-overview.blade.php`:

```blade
@include('filament-widgets::js-widget')
```

The shared view already includes the widget and section wrappers. Do not wrap this include in another `<x-filament-widgets::widget>` or `<x-filament::section>`. If you replace it entirely, your custom view is responsible for the widget layout and any renderer integration.

### Building reusable plugin widgets

For a plugin, build the renderer as an ES module and register it as a [published JavaScript asset](../advanced/assets#building-lazy-loaded-es-modules), rather than using the host application's Vite manifest:

```php
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;

FilamentAsset::register([
    Js::make('revenue-overview', __DIR__ . '/../dist/revenue-overview.js')->loadedOnRequest(),
], 'acme/reports');
```

Return the published URL from the widget:

```php
use Filament\Support\Facades\FilamentAsset;

public function getRenderer(): string
{
    return FilamentAsset::getScriptSrc('revenue-overview', 'acme/reports');
}
```

Use `php artisan filament:assets` to publish the asset. Keep the module's default export and publish any imported chunks and styles too. You may alternatively return `Filament\Support\RawJs` containing a mount function; only use trusted JavaScript, never interpolate user input into it.
