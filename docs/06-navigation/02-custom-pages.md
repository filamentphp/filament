---
title: Custom pages
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

Filament allows you to create completely custom pages for the app.

<AutoScreenshot name="panels/custom-page" alt="A custom page with header actions" version="4.x" />

## Creating a page

To create a new page, you can use:

```bash
php artisan make:filament-page Settings
```

This command will create two files - a page class in the `/Pages` directory of the Filament directory, and a view in the `/pages` directory of the Filament views directory.

Page classes are all full-page [Livewire](https://livewire.laravel.com) components with a few extra utilities you can use with the panel.

## Rendering page content with Inertia

You can render Vue, React, or Svelte components inside Filament custom pages. Filament keeps the page layout, navigation, header actions, and widgets, while Inertia manages the page content and its props.

### Installing the prerequisites

Install `inertiajs/inertia-laravel` version 3.3 or later within major version 3, together with the Inertia adapter and Vite plugin for your chosen framework. Inertia is optional, so Filament does not install these dependencies for you.

```bash
composer require inertiajs/inertia-laravel:"^3.3"
```

| Framework | JavaScript packages | Vite plugin |
| --- | --- | --- |
| Vue | `@inertiajs/vue3`, `vue` | `@vitejs/plugin-vue` |
| React | `@inertiajs/react`, `react`, `react-dom` | `@vitejs/plugin-react` |
| Svelte 5 | `@inertiajs/svelte`, `svelte` | `@sveltejs/vite-plugin-svelte` |

You also need `@inertiajs/core`, `vite`, and `laravel-vite-plugin`. Use mutually compatible package versions. TypeScript needs `typescript`, and React TypeScript also needs `@types/react` and `@types/react-dom`.

<Aside variant="danger">
    This integration requires the unreleased Inertia `externalNavigation` APIs. The public `@inertiajs/*` 3.7 packages do not provide the required contract yet. Use matching core and adapter builds that include those APIs. No Livewire patch is required. TypeScript SSR also requires Inertia's correction allowing a custom SSR `id` in its declarations.
</Aside>

### Generating an Inertia page

Pass one framework option to `make:filament-page`:

```bash
php artisan make:filament-page Reports --vue
php artisan make:filament-page Reports --react
php artisan make:filament-page Reports --svelte
```

The `--vue`, `--react`, and `--svelte` options are mutually exclusive. Add `--ts` or its `--typescript` alias to generate TypeScript files. All Inertia setup options require a framework option. Omitting all framework options preserves the existing Blade page behavior.

On first interactive setup, the command asks whether to generate an SSR entry. Pass `--ssr` to request it or `--no-ssr` to skip it without prompting. Non-interactive commands do not generate a new SSR entry unless you pass `--ssr`. Neither skipping generation nor passing `--no-ssr` disables SSR in your application.

The command rejects resource pages because their record lifecycle is not compatible with Inertia content. Before writing anything, it also checks for the required Composer and Node packages and prints actionable installation instructions when a dependency is missing. It does not install dependencies or change your panel, Vite, TypeScript, or SSR configuration.

For example, the Vue command creates:

```text
app/Filament/Pages/Reports.php
resources/js/filament/inertia.js
resources/js/filament/pages/Reports.vue
resources/js/filament/resolve.js
```

TypeScript changes the JavaScript extensions to `.ts`, React pages use `.jsx` or `.tsx`, and Svelte pages use `.svelte`. The shared resolver loads every component for the selected framework whose name starts with `Filament/`, and `inertia.js` exports a reusable Filament renderer. The `--ssr` option also creates `resources/js/filament/ssr.js` or `ssr.ts` when no existing server is detected.

Shared entry and resolver files are preserved if they already exist, including when you use `--force`. A second page reuses them. The command rejects a different framework when the existing renderer uses one of the other generated helpers; configure a separate renderer manually if your panel needs multiple frameworks. When switching a React page between JavaScript and TypeScript, rename or remove its existing component first so both extensions do not resolve to the same name.

#### Reusing an existing setup

| Existing setup | What the command does |
| --- | --- |
| Missing dependencies | Generates nothing and lists missing packages. Prints Composer and npm commands where applicable; use equivalent commands for another package manager. Matching unreleased Inertia builds must come from your chosen source rather than an incompatible public release. |
| Native Inertia outside Filament | Reuses installed packages and creates separate Filament files. Leaves the native bootstrap, components, middleware, build configuration and SSR server unchanged. |
| Conventional Filament renderer and resolver | Creates only the new PHP page and component, reusing the shared files. Registering the plugin with `rendererEntry()` lets the command identify the source entry without evaluating a renderer callback. |
| Custom Filament renderer | Uses explicit paths. Does not infer or edit arbitrary JavaScript imports and resolvers. If there is no sibling `resolve.js` or `resolve.ts`, creates only the page/component and reports that resolver registration and SSR integration are manual. |
| Existing SSR server | Preserves it and reports how to dispatch `Filament/` components through the Filament resolver. Does not generate a second server entry. |

For custom paths in a new setup, pass paths relative to your application's root. Add `--ssr` to generate a server entry alongside the renderer:

```bash
php artisan make:filament-page Reports --react --ts --ssr \
    --inertia-entry=resources/js/admin/inertia.ts \
    --inertia-pages=resources/js/admin/pages
```

`--inertia-entry` selects the **Filament renderer**, not the native application's `createInertiaApp()` bootstrap. If the panel already uses `rendererEntry()`, the option must agree with it. An existing `renderer()` URL or callback is still supported at runtime, but the command cannot determine its source; supply the paths explicitly or switch to `rendererEntry()`.

Without `--inertia-pages`, the conventional component directory is `pages` beside the renderer. Existing renderers outside `resources/js/filament` require an explicit component directory on subsequent invocations too. Existing resolvers are never rewritten when you change this directory; add the new component mapping or update the glob yourself. Generated resolvers use a relative glob for the supplied directory. Paths must use letters, numbers, slashes, dots, underscores or hyphens, without parent traversal. Other layouts can be configured manually.

The command detects `resources/js/ssr.js`, `resources/js/ssr.ts`, `resources/js/ssr.jsx`, and `resources/js/ssr.tsx` as existing shared server entries. It also detects a configured `inertia.ssr.bundle` or an existing `bootstrap/ssr/ssr.js` or `ssr.mjs` bundle. In these cases it does not generate a second server. A configured bundle is respected even before it has been built. Bundle detection does not prove that the server is running or identify its source: locate the source in your Vite configuration and merge the Filament dispatch there, not into the compiled bundle.

For an existing source in another location, you can identify it with `--inertia-ssr-entry=resources/server/ssr.ts`. That file must already exist; this option never creates or rewrites it. Arbitrary Vite configuration, remote SSR services, and other custom locations are not inspected. If none of the detected files or configuration exists, use `--no-ssr` and integrate your custom server manually rather than generating a second one. If SSR is already enabled, ensure it handles the new component even when using `--no-ssr`.

#### Adding SSR to existing pages

You do not need to regenerate your PHP pages or client components to enable SSR. Add a server entry using the framework setup described below, configure its Vite entry, build it, and start the server. If you already have an SSR server, add the Filament dispatch to that server instead.

Alternatively, when adding a genuinely new page, pass `--ssr` to generate the shared server entry if none is detected. Do not rerun the generator with `--force` on a customized page just to add SSR: it replaces the page and component, even though shared renderer files are preserved. Without `--force`, a page collision stops generation before any files are changed.

#### Completing manual setup

The command ends with a remaining-work checklist. A registered `rendererEntry()` and an entry present in `public/build/manifest.json` are reported separately from unverified configuration. A manifest only proves inclusion in the **last build**, not that your current configuration or newly generated page works. Custom manifest paths, Vite plugin configuration, export preservation, TypeScript settings, custom renderer wiring, and dependency peer-version compatibility are not verified automatically.

The command never installs packages, edits configuration, registers middleware, modifies your native Inertia app, runs builds, or starts an SSR server. Merge the following configuration manually, then build and test your page. Existing application-specific configuration is preserved rather than guessed or replaced.

### Configuring the generated files

Generation leaves four one-time application changes to you:

1. Register `InertiaPlugin` on the panel and point it at the generated browser entry:

```php
use Filament\Inertia\InertiaPlugin;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(
            InertiaPlugin::make()
                ->rendererEntry('resources/js/filament/inertia.js'),
        );
}
```

Use `.ts` in the asset path when you generated TypeScript. `rendererEntry()` resolves the source entry through Laravel's Vite integration. You can still use `renderer()` for a custom URL or callback; setting either option replaces the other.

2. Add `resources/js/filament/inertia.js` to the Laravel Vite plugin's `input` array and add your framework's Vite plugin. Set `preserveEntrySignatures: 'exports-only'` in `build.rollupOptions`, or in `build.rolldownOptions` when using Rolldown-based Vite. Filament dynamically imports the entry's default export, which would otherwise be removed from some production builds.
3. When using TypeScript, include the generated files in your TypeScript configuration. Use `moduleResolution: 'bundler'`, `allowImportingTsExtensions: true`, `noEmit: true`, and the `vite/client` types. React also needs `jsx: 'react-jsx'`. Use `vue-tsc` or `svelte-check` to check single-file components for those frameworks.
4. Build your assets.

If you generated an SSR entry, also set the Laravel Vite plugin's `ssr` option to `resources/js/filament/ssr.js` or `.ts`, then build and run your Inertia SSR bundle. Do not replace existing Vite, TypeScript, or SSR settings; merge the generated entries into them.

```bash
npm run build
npx vite build --ssr
php artisan inertia:start-ssr
```

#### Checking TypeScript

Install checker tooling separately from the packages required to generate pages. These recipes use TypeScript 6: the audited Vue 3.5 / `vue-tsc` 3.3 and Svelte 5 / `svelte-check` 4.7 tools do not support a plain TypeScript 7 installation. Check their compatibility before upgrading the compiler. The generator checks package presence, not checker compatibility.

```bash
# React
npm install --save-dev typescript@~6.0 @types/react@^19 @types/react-dom@^19 @types/node
npx tsc --noEmit

# Vue
npm install --save-dev typescript@~6.0 vue-tsc@~3.3 @types/node
npx vue-tsc --noEmit

# Svelte
npm install --save-dev typescript@~6.0 svelte-check@~4.7 @types/node
npx svelte-check --tsconfig ./tsconfig.json
```

Use React types matching your React major and Node types matching your supported Node runtime. The current unreleased core declarations also reference Axios: install `axios` if your strict check reports it missing, even when your application does not use that HTTP client. Merge these options into your existing `tsconfig.json`, retaining application-specific settings and includes:

```json
{
    "compilerOptions": {
        "target": "ES2022",
        "module": "ESNext",
        "moduleResolution": "bundler",
        "allowImportingTsExtensions": true,
        "noEmit": true,
        "strict": true,
        "skipLibCheck": false,
        "types": ["vite/client", "node"]
    },
    "include": ["resources/js/filament/**/*"]
}
```

React also needs `"jsx": "react-jsx"`. Node types cover SSR imports such as `node:stream`. Check generated server entries as well as browser entries: a successful Vite build is not a typecheck. Use matching Inertia core/adapter builds with the Svelte recursive-render declaration correction; do not hide declaration errors with `skipLibCheck`.

#### Working with linked development packages

Normal installed packages should not need development-only resolution settings. When testing Filament or Inertia through symlinks to adjacent checkouts, imports may resolve from the linked package rather than your application. In that setup, add your adapter and framework to Vite's `resolve.dedupe`, for example `['@inertiajs/core', '@inertiajs/react', 'react', 'react-dom']` for React. Vue uses `@inertiajs/vue3` and `vue`; Svelte uses `@inertiajs/svelte` and `svelte` alongside core.

TypeScript may also need `paths` mappings to the application's installed declarations to avoid missing or duplicate framework types. Inspect each package's `types` and `exports` fields rather than assuming declarations live in `dist`. Keep these mappings in your development setup, not generated application code. Package installation may remove manually created links; verify resolved package paths after installing tooling. Test packaged dependencies separately before treating a linked-checkout workaround as a normal installation requirement.

### Rendering PHP and client components

The generated page uses `InteractsWithInertia` and prefixes its component name with `Filament/`:

```php
use Filament\Pages\Concerns\InteractsWithInertia;
use Filament\Pages\Page;
use Inertia\Inertia;
use Inertia\Response;

class Reports extends Page
{
    use InteractsWithInertia;

    protected function getInertiaResponse(): Response
    {
        return Inertia::render('Filament/Reports', [
            'title' => 'Reports',
        ]);
    }
}
```

The generated Vue client component receives `title` as a prop:

```vue
<script setup>
defineProps({
    title: String,
})
</script>

<template>
    <h2>{{ title }}</h2>
</template>
```

Equivalent React and Svelte components can receive the same prop:

```jsx
export default function Reports({ title }) {
    return <h2>{title}</h2>
}
```

```svelte
<script>
    let { title } = $props()
</script>

<h2>{title}</h2>
```

### Passing props and submitting forms

Return a native `Inertia\Response` from `getInertiaResponse()`. Shared, deferred, optional, and other Inertia props work normally. Use your framework adapter's standard form helper for submissions. Register an authenticated POST route for the action; the generated page route only displays the page. For example, a Vue form submitting to your `/reports` action can use:

```vue
<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({ name: '' })
</script>

<template>
    <form @submit.prevent="form.post('/reports')">
        <label for="report-name">Name</label>
        <input id="report-name" v-model="form.name" name="name">
        <button type="submit" :disabled="form.processing">Save</button>
        <p v-if="form.errors.name">{{ form.errors.name }}</p>
    </form>
</template>
```

### Resolving multiple components

Keep one browser renderer and resolve all generated Filament components from it. A Vue resolver can use:

```js
const pages = import.meta.glob('./pages/**/*.vue')

export function resolve(name) {
    const page = pages[`./pages/${name.slice('Filament/'.length)}.vue`]

    if (!name.startsWith('Filament/') || !page) {
        throw new Error(`Unknown Filament page: ${name}`)
    }

    return page().then((module) => module.default)
}
```

The renderer imports that resolver:

```js
import { createRenderer } from '../../../vendor/filament/filament/resources/js/inertia/vue.js'
import { resolve } from './resolve.js'

export default createRenderer({ resolve })
```

Return `Inertia::render('Filament/Reports')` for `pages/Reports.vue`, or `Inertia::render('Filament/Sales/Orders')` for `pages/Sales/Orders.vue`. You do not need to edit the renderer for another page.

The React generator resolves both `.jsx` and `.tsx` components. The Svelte generator resolves `.svelte` modules, returning the module rather than its default export as required by that adapter. Component names outside the `Filament/` prefix remain owned by your other Inertia application. The browser entry exports a renderer instead of calling `createInertiaApp()` itself: Filament supplies the mounting element, setup callback, and navigation integration.

### Navigating and restoring state

Filament owns navigation between pages. Inertia links and redirects use Livewire navigation when the panel enables SPA mode, or full-document navigation otherwise. URLs matching the panel's `spaUrlExceptions()` also use full-document navigation.

Use Inertia's remember APIs for local state that should survive leaving a page; ordinary component state is recreated on navigation. Remembered state is scoped to the panel, tenant, authenticated user, session, and request URI. Override `getInertiaRememberKey()` to choose a page-specific key, such as one shared across query-string tabs.

GET partial reloads can send different query parameters on the same path without navigating. They update the requested props, but **do not update the address bar or add a browser history entry**, in either SPA mode. For example, `router.get('/reports', { period: 'October' }, { only: ['period', 'total'] })` can load October's figures while the address bar stays on `/reports`. Back/forward navigation does not traverse these prop-only updates. Use a normal Inertia link or GET visit without partial options when the new query should be a navigable URL; ordinary component state will then be recreated by the host.

A different path, component, fragment, or mutation redirect destination is handed back to Filament. For redirects to another page, use `Inertia::location($url)` when the destination needs one-time session data such as flash messages. This prevents an intermediate Inertia response from consuming that data before Filament loads the destination document.

### Rendering on the server

The optional generated SSR entry sets `id: 'filament-inertia'` in its `createInertiaApp()` options. Filament's browser host requires that exact root ID; the adapters' default `app` ID does not work.

If an existing SSR server also handles a native Inertia application, keep that application's bootstrap and root ID unchanged. The same server can dispatch by component prefix: use `filament-inertia` for `Filament/` components and the native application's existing ID for everything else. Merge this dispatch into your existing server entry rather than overwriting its SSR configuration.

For example, this Vue server selects the root ID and resolver per request. This example assumes your native resolver is exported from `resources/js/resolve.js`; use your existing resolver and native root ID instead:

```js
// resources/js/ssr.js
import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { renderToString } from '@vue/server-renderer'
import { createSSRApp, h } from 'vue'
import { resolve as resolveNative } from './resolve.js'
import { resolve as resolveFilament } from './filament/resolve.js'

createServer((page) => {
    const isFilament = page.component.startsWith('Filament/')

    return createInertiaApp({
        id: isFilament ? 'filament-inertia' : 'app',
        page,
        render: renderToString,
        resolve: isFilament ? resolveFilament : resolveNative,
        setup: ({ App, props, plugin }) =>
            createSSRApp({ render: () => h(App, props) }).use(plugin),
    })
})
```

Keep any additional native SSR plugins or options. For React, use `@inertiajs/react`, `renderToString` from `react-dom/server`, and `setup: ({ App, props }) => createElement(App, props)` with `createElement` imported from `react`. For Svelte 5, use `@inertiajs/svelte`, import `render` from `svelte/server`, and use `setup: ({ App, props }) => render(App, { props })` without a separate `render` option. Each adapter also provides its own `/server` import. If the two parts of your app use different frameworks or rendering configuration, branch to separate `createInertiaApp()` calls inside the **same** server callback instead of sharing those options. Do not change the native application's root ID to `filament-inertia`.

Without SSR, Filament displays a loading indicator until the framework mounts. With SSR, content remains visible but inert until hydration finishes. Deferred props can continue loading afterward. If the renderer cannot load or mount, Filament displays a reload action.

### Coexisting with an existing Inertia application

The generated `resources/js/filament/inertia.js` is a separate renderer for content mounted inside Filament. Keep an existing native Inertia application's browser bootstrap, root element, routes, and component resolver unchanged. Resolve only `Filament/` components through Filament's entry, and route native components through the existing entry. You can use one shared component resolver from both entries when it preserves that distinction.

To customize shared props or asset versioning for panel routes, pass your application's Inertia middleware class to `InertiaPlugin::middleware()`. The plugin runs it after session middleware, panel authentication, and tenant identification where applicable. Do not register the same middleware separately on the same panel routes.

### Authorizing requests and combining schema content

Livewire shell updates do not rebuild the Inertia response. Inertia requests resolve props directly without calling the page's Livewire `mount()` method, so read request and route context in `getInertiaResponse()` rather than relying on properties initialized only by `mount()`.

For a `Page`, Filament checks `canAccess()` before returning the document or Inertia props, and again during Livewire updates. Put additional restrictions in `canAccess()` or route middleware. A `SimplePage` does not have `Page::canAccess()`; use route middleware or implement `authorizeInertiaAccess()` for its request-based checks. You can use the trait on a `SimplePage`, but its routing and authentication remain your responsibility.

Use the default page view, or render `{{ $this->content }}` in a custom Blade view. If you override `content()`, include `getInertiaContentComponent()` where the framework component should appear:

```php
use Filament\Schemas\Schema;

public function content(Schema $schema): Schema
{
    return $schema->components([
        $this->getInertiaContentComponent(),
    ]);
}
```

Render one Inertia content component per page. You can override `getInertiaRenderer()` to choose a different renderer for an individual page.

## Authorization

You can prevent pages from appearing in the menu by overriding the `canAccess()` method in your Page class. This is useful if you want to control which users can see the page in the navigation, and also which users can visit the page directly:

```php
public static function canAccess(): bool
{
    return auth()->user()->canManageSettings();
}
```

## Adding actions to pages

Actions are buttons that can perform tasks on the page, or visit a URL. You can read more about their capabilities [here](../actions).

Since all pages are Livewire components, you can [add actions](../components/action#adding-the-action) anywhere. Pages already have the `InteractsWithActions` trait, `HasActions` interface, and `<x-filament-actions::modals />` Blade component all set up for you.

### Header actions

You can also easily add actions to the header of any page, including [resource pages](../resources/overview). You don't need to worry about adding anything to the Blade template, we handle that for you. Just return your actions from the `getHeaderActions()` method of the page class:

```php
use Filament\Actions\Action;

protected function getHeaderActions(): array
{
    return [
        Action::make('edit')
            ->url(route('posts.edit', ['post' => $this->post])),
        Action::make('delete')
            ->requiresConfirmation()
            ->action(fn () => $this->post->delete()),
    ];
}
```

#### Aligning header actions

By default, header actions are aligned to the left on mobile. To change the alignment of the header actions on mobile, set `$headerActionsAlignment`:

```php
use Filament\Support\Enums\Alignment;

protected ?Alignment $headerActionsAlignment = Alignment::End;
```

### Opening an action modal when a page loads

You can also open an action when a page loads by setting the `$defaultAction` property to the name of the action you want to open:

```php
use Filament\Actions\Action;

public $defaultAction = 'onboarding';

public function onboardingAction(): Action
{
    return Action::make('onboarding')
        ->modalHeading('Welcome')
        ->visible(fn (): bool => ! auth()->user()->isOnBoarded());
}
```

You can also pass an array of arguments to the default action using the `$defaultActionArguments` property:

```php
public $defaultActionArguments = ['step' => 2];
```

Alternatively, you can open an action modal when a page loads by specifying the `action` as a query string parameter to the page:

```
/admin/products/edit/932510?action=onboarding
```

### Refreshing form data

If you're using actions on an [Edit](../resources/editing-records) or [View](../resources/viewing-records) resource page, you can refresh data within the main form using the `refreshFormData()` method:

```php
use App\Models\Post;
use Filament\Actions\Action;

Action::make('approve')
    ->action(function (Post $record) {
        $record->approve();

        $this->refreshFormData([
            'status',
        ]);
    })
```

This method accepts an array of model attributes that you wish to refresh in the form.

## Adding widgets to pages

Filament allows you to display [widgets](../widgets) inside pages, below the header and above the footer.

To add a widget to a page, use the `getHeaderWidgets()` or `getFooterWidgets()` methods:

```php
use App\Filament\Widgets\StatsOverviewWidget;

protected function getHeaderWidgets(): array
{
    return [
        StatsOverviewWidget::class
    ];
}
```

`getHeaderWidgets()` returns an array of widgets to display above the page content, whereas `getFooterWidgets()` are displayed below.

If you'd like to learn how to build and customize widgets, check out the [Widgets](../widgets) documentation section.

### Customizing the widgets' grid

You may change how many grid columns are used to display widgets.

You may override the `getHeaderWidgetsColumns()` or `getFooterWidgetsColumns()` methods to return a number of grid columns to use:

```php
public function getHeaderWidgetsColumns(): int | array
{
    return 3;
}
```

#### Responsive widgets grid

You may wish to change the number of widget grid columns based on the responsive [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) of the browser. You can do this using an array that contains the number of columns that should be used at each breakpoint:

```php
public function getHeaderWidgetsColumns(): int | array
{
    return [
        'md' => 4,
        'xl' => 5,
    ];
}
```

This pairs well with [responsive widget widths](../widgets#responsive-widget-widths).

#### Passing data to widgets from the page

You may pass data to widgets from the page using the `getWidgetData()` method:

```php
public function getWidgetData(): array
{
    return [
        'stats' => [
            'total' => 100,
        ],
    ];
}
```

Now, you can define a corresponding public `$stats` array property on the widget class, which will be automatically filled:

```php
public $stats = [];
```

### Passing properties to widgets on pages

When registering a widget on a page, you can use the `make()` method to pass an array of [Livewire properties](https://livewire.laravel.com/docs/properties) to it:

```php
use App\Filament\Widgets\StatsOverviewWidget;

protected function getHeaderWidgets(): array
{
    return [
        StatsOverviewWidget::make([
            'status' => 'active',
        ]),
    ];
}
```

This array of properties gets mapped to [public Livewire properties](https://livewire.laravel.com/docs/properties) on the widget class:

```php
use Filament\Widgets\Widget;

class StatsOverviewWidget extends Widget
{
    public string $status;

    // ...
}
```

Now, you can access the `status` in the widget class using `$this->status`.

## Customizing the page title

By default, Filament will automatically generate a title for your page based on its name. You may override this by defining a `$title` property on your page class:

```php
protected static ?string $title = 'Custom Page Title';
```

Alternatively, you may return a string from the `getTitle()` method:

```php
use Illuminate\Contracts\Support\Htmlable;

public function getTitle(): string | Htmlable
{
    return __('Custom Page Title');
}
```

## Customizing the page navigation label

By default, Filament will use the page's [title](#customizing-the-page-title) as its [navigation](overview) item label. You may override this by defining a `$navigationLabel` property on your page class:

```php
protected static ?string $navigationLabel = 'Custom Navigation Label';
```

Alternatively, you may return a string from the `getNavigationLabel()` method:

```php
public static function getNavigationLabel(): string
{
    return __('Custom Navigation Label');
}
```

## Customizing the page URL

By default, Filament will automatically generate a URL (slug) for your page based on its name. You may override this by defining a `$slug` property on your page class:

```php
protected static ?string $slug = 'custom-url-slug';
```

## Customizing the page heading

By default, Filament will use the page's [title](#customizing-the-page-title) as its heading. You may override this by defining a `$heading` property on your page class:

```php
protected ?string $heading = 'Custom Page Heading';
```

Alternatively, you may return a string from the `getHeading()` method:

```php
public function getHeading(): string
{
    return __('Custom Page Heading');
}
```

### Adding a page subheading

You may also add a subheading to your page by defining a `$subheading` property on your page class:

```php
protected ?string $subheading = 'Custom Page Subheading';
```

Alternatively, you may return a string from the `getSubheading()` method:

```php
public function getSubheading(): ?string
{
    return __('Custom Page Subheading');
}
```

<AutoScreenshot name="panels/custom-page-subheading" alt="A custom page with a subheading" version="4.x" />

## Replacing the page header with a custom view

You may replace the default [heading](#customizing-the-page-heading), [subheading](#adding-a-page-subheading) and [actions](#header-actions) with a custom header view for any page. You may return it from the `getHeader()` method:

```php
use Illuminate\Contracts\View\View;

public function getHeader(): ?View
{
    return view('filament.settings.custom-header');
}
```

This example assumes you have a Blade view at `resources/views/filament/settings/custom-header.blade.php`.

## Rendering a custom view in the footer of the page

You may also add a footer to any page, below its content. You may return it from the `getFooter()` method:

```php
use Illuminate\Contracts\View\View;

public function getFooter(): ?View
{
    return view('filament.settings.custom-footer');
}
```

This example assumes you have a Blade view at `resources/views/filament/settings/custom-footer.blade.php`.

## Customizing the maximum content width

By default, Filament will restrict the width of the content on the page, so it doesn't become too wide on large screens. To change this, you may override the `getMaxContentWidth()` method. Options correspond to [Tailwind's max-width scale](https://tailwindcss.com/docs/max-width). The options are `ExtraSmall`, `Small`, `Medium`, `Large`, `ExtraLarge`, `TwoExtraLarge`, `ThreeExtraLarge`, `FourExtraLarge`, `FiveExtraLarge`, `SixExtraLarge`, `SevenExtraLarge`, `Full`, `MinContent`, `MaxContent`, `FitContent`,  `Prose`, `ScreenSmall`, `ScreenMedium`, `ScreenLarge`, `ScreenExtraLarge` and `ScreenTwoExtraLarge`. The default is `SevenExtraLarge`:

```php
use Filament\Support\Enums\Width;

public function getMaxContentWidth(): Width
{
    return Width::Full;
}
```

## Generating URLs to pages

Filament provides `getUrl()` static method on page classes to generate URLs to them. Traditionally, you would need to construct the URL by hand or by using Laravel's `route()` helper, but these methods depend on knowledge of the page's slug or route naming conventions.

The `getUrl()` method, without any arguments, will generate a URL:

```php
use App\Filament\Pages\Settings;

Settings::getUrl(); // /admin/settings
```

If your page uses URL / query parameters, you should use the argument:

```php
use App\Filament\Pages\Settings;

Settings::getUrl(['section' => 'notifications']); // /admin/settings?section=notifications
```

### Generating URLs to pages in other panels

If you have multiple panels in your app, `getUrl()` will generate a URL within the current panel. You can also indicate which panel the page is associated with, by passing the panel ID to the `panel` argument:

```php
use App\Filament\Pages\Settings;

Settings::getUrl(panel: 'marketing');
```

## Adding sub-navigation between pages

You may want to add a common sub-navigation to multiple pages, to allow users to quickly navigate between them. You can do this by defining a [cluster](clusters). Clusters can also contain [resources](../resources/overview), and you can switch between multiple pages or resources within a cluster.

## Setting the sub-navigation position

The sub-navigation is rendered at the start of the page by default. You may change the position for a page by setting the `$subNavigationPosition` property on the page. The value may be `SubNavigationPosition::Start`, `SubNavigationPosition::End`, or `SubNavigationPosition::Top` to render the sub-navigation as tabs:

```php
use Filament\Pages\Enums\SubNavigationPosition;

protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;
```

<AutoScreenshot name="panels/cluster-end" alt="Page with end sub-navigation position" version="4.x" />

The `SubNavigationPosition::Top` option renders the sub-navigation as tabs above the page content:

<AutoScreenshot name="panels/cluster-top" alt="Page with top sub-navigation position" version="4.x" />

## Adding extra attributes to the body tag of a page

You may wish to add extra attributes to the `<body>` tag of a page. To do this, you can set an array of attributes in `$extraBodyAttributes`:

```php
protected array $extraBodyAttributes = [];
```

Or, you can return an array of attributes and their values from the `getExtraBodyAttributes()` method:

```php
public function getExtraBodyAttributes(): array
{
    return [
        'class' => 'settings-page',
    ];
}
```
