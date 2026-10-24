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

Install your application's existing JavaScript dependencies, including Vite, before running the generator, for example with `npm install`. The command reads the installed Vite version to select compatible compiler plugins, including when you use `--skip-install`.

Add one renderer flag to the command:

```bash
php artisan make:filament-widget RevenueOverview --react
php artisan make:filament-widget RevenueOverview --vue
php artisan make:filament-widget RevenueOverview --svelte
php artisan make:filament-widget RevenueOverview --js
```

The command creates a PHP widget class and a renderer in `resources/js/filament/widgets/`, without a Blade view. It installs missing development dependencies, updates recognizable Vite configurations, and offers to compile your assets. Existing compatible dependency declarations keep their version constraints and their `package.json` sections. Incompatible dependencies require a manual upgrade instead of being replaced automatically. Replace the starter heading and description with your own UI. The renderer appears inside a standard Filament widget section.

For TypeScript, add `--typescript` or its alias `--ts`:

```bash
php artisan make:filament-widget RevenueOverview --react --ts
```

Register or discover the generated class in your panel like any other widget. You can also use it as a [page widget](../navigation/custom-pages#adding-widgets-to-pages) or a [resource widget](../resources/widgets).

#### Customizing generation

Use `--pm=yarn` to use Yarn instead of npm, `--skip-install` to install dependencies yourself, or `--skip-build` to skip the compilation prompt. Blade-based generation does not run a package manager. The existing panel, resource, cluster, and `--force` options still apply. Renderer flags cannot be combined with `--chart`, `--stats-overview`, or `--table`.

The generated PHP class uses `HasJsRenderer` and loads its entry through `Vite::asset()`. The generated files depend on your chosen renderer:

- React: `revenue-overview.jsx`, or `revenue-overview.tsx` with `--typescript`.
- Vue: `revenue-overview.js` and `RevenueOverview.vue`. With `--typescript`, the entry uses `.ts` and the component uses `<script setup lang="ts">`.
- Svelte 5: `revenue-overview.svelte.js` and `RevenueOverview.svelte`. With `--typescript`, the entry uses `.svelte.ts` and the component uses `<script lang="ts">`.
- Framework-free JavaScript: `revenue-overview.js`, or `revenue-overview.ts` with `--typescript`.

Nested names, such as `Reports/RevenueOverview`, place the JavaScript files in a `reports/` subdirectory. TypeScript generation also configures the `@filament/widgets/js-widget` [type alias](#typing-renderers).

Typed starters install TypeScript 6 when TypeScript is missing, and preserve an existing compatible TypeScript 5 or 6 installation. Vue and Svelte tooling require the JavaScript compiler API provided by these versions, which TypeScript 7 does not provide.

The command adds the renderer to the Laravel Vite plugin's `input` array, enables the Vue or Svelte compiler plugin when needed, and preserves the renderer's default export in production builds. React uses Vite's built-in JSX support. If your configuration cannot be updated automatically, the command prints the remaining manual steps instead of replacing it. Follow the [Vite module setup](../advanced/assets#building-lazy-loaded-es-modules) to complete them.

New TypeScript configurations use `"jsx": "react-jsx"` so Vite transforms React JSX. Existing configurations are preserved. If yours uses `"jsx": "preserve"`, change it to `"react-jsx"` or configure a React JSX transformer in Vite; otherwise Vite 8 can fail with `Unexpected JSX expression`.

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

Use the entry extension generated for your chosen framework. Only include data that the current user is authorized to see. Props are snapshots, not writable Livewire state: changing `props.configuration` does not update PHP. When a Livewire request re-renders the widget, Filament sends changed props to the existing renderer's `update()` method without remounting it.

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

Set up the dashboard's [filters form or filter action](overview#filtering-widget-data) as usual. Filter updates re-render the widget, so both raw filter values and query results returned by `getRendererConfiguration()` reach JavaScript. Filters from a live form are not validated; validate values before using them in a query. Only pass the filter values the renderer needs.

The same pattern works with other reactive Livewire properties. Props are recalculated when the widget itself renders, not whenever arbitrary PHP or parent-page state changes. Mark custom parent-provided properties with Livewire's `#[Reactive]` attribute when they should trigger widget updates.

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

Define `refreshRevenue()` on your widget class. Normal Livewire authorization and validation rules apply: validate browser-provided arguments and authorize operations inside PHP methods. Unlike form fields, widgets do not require an `#[Exposed]` attribute and do not receive schema utilities, a field value, or `onChange()` / `onBlur()` callbacks. Pass `utilities` separately to a React, Vue, or Svelte component if it needs to interact with PHP.

### Typing renderers

The declarations ship at `vendor/filament/widgets/resources/js/types/js-widget.d.ts`. The generator configures an alias automatically for standard JSON `tsconfig.json` files. If it cannot, merge this mapping into your configuration, adjusting the path relative to `baseUrl` if you use one:

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

#### Checking types

`npm run build` compiles the renderers but does not check their types. For JavaScript/React TypeScript entries, run `npx tsc --noEmit`. For Vue single-file components, install `vue-tsc` and run `npx vue-tsc --noEmit`; plain `tsc` cannot resolve `.vue` imports. For Svelte components, install `svelte-check` and run `npx svelte-check --tsconfig ./tsconfig.json`.

In a mixed-framework application, use separate TypeScript configurations with `include` patterns for each framework and pass the appropriate configuration to each checker. Keep the Filament type alias in each configuration or inherit it from a shared configuration.

### Synchronizing and disposing renderers

Filament owns the outer widget and section. Your renderer owns only the ignored `host` subtree. Livewire does not morph that subtree, and you should not render Blade, Livewire, or Alpine components inside it. Preserve local UI state in your renderer while applying the latest PHP snapshots in `update()`.

Mounting may be asynchronous. If PHP props change while mounting, the latest snapshot is delivered after mounting completes. If the widget is removed before mounting completes, the returned instance is disposed. Changing `getRenderer()` replaces the renderer; changing only `getRendererConfiguration()` updates it in place.

Return `destroy()` to unmount your framework and remove event listeners, observers, timers, and subscriptions. Stop DOM work immediately. Asynchronous updates may overlap, so cancel or order requests inside your renderer. Cleanup promise failures are logged, but remounting does not wait for cleanup.

#### Handling renderer errors

Import, mount, and update failures are logged to the browser console. Filament disposes the renderer, clears its host, and displays an accessible error message. Reload or remount the widget to retry. If mounting allocates resources before throwing, clean those resources up in your mount function since no instance has been returned yet.

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
