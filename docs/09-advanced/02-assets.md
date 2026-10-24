---
title: Registering assets
---

## Introduction

All packages in the Filament ecosystem share an asset management system. This allows both official plugins and third-party plugins to register CSS and JavaScript files that can then be consumed by Blade views.

Register your files in a service provider, then publish them with:

```bash
php artisan filament:assets
```

Start with [CSS registration](#registering-css-files) or [JavaScript registration](#registering-javascript-files). For components that import JavaScript on demand, see [building lazy-loaded ES modules](#building-lazy-loaded-es-modules).

## The `FilamentAsset` facade

Use `FilamentAsset` to register asset files from your application or a plugin. Running `php artisan filament:assets` copies them into the application's `/public` directory.

Assets always have a unique ID chosen by you, which is used as the file name when the asset is copied into the `/public` directory. This ID is also used to reference the asset in Blade views. While the ID is unique, if you are registering assets for a plugin, then you do not need to worry about IDs clashing with other plugins, since the asset will be copied into a directory named after your plugin.

The `FilamentAsset` facade should be used in the `boot()` method of a service provider. It can be used inside an application service provider such as `AppServiceProvider`, or inside a plugin service provider.

The `FilamentAsset` facade has one main method, `register()`, which accepts an array of assets to register:

```php
use Filament\Support\Facades\FilamentAsset;

public function boot(): void
{
    // ...

    FilamentAsset::register([
        // ...
    ]);

    // ...
}
```

### Registering assets for a plugin

When registering assets for a plugin, you should pass the name of the Composer package as the second argument of the `register()` method:

```php
use Filament\Support\Facades\FilamentAsset;

FilamentAsset::register([
    // ...
], package: 'danharrin/filament-blog');
```

Now, all the assets for this plugin will be copied into their own directory inside `/public`, to avoid the possibility of clashing with other plugins' files with the same names.

## Registering CSS files

To register a CSS file with the asset system, use the `FilamentAsset::register()` method in the `boot()` method of a service provider. You must pass in an array of `Css` objects, which each represents a CSS file that should be registered in the asset system.

Each `Css` object has a unique ID and a path to the CSS file:

```php
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;

FilamentAsset::register([
    Css::make('custom-stylesheet', __DIR__ . '/../../resources/css/custom.css'),
]);
```

In this example, we use `__DIR__` to generate a relative path to the asset from the current file. For instance, if you were adding this code to `/app/Providers/AppServiceProvider.php`, then the CSS file should exist in `/resources/css/custom.css`.

Now, when the `php artisan filament:assets` command is run, this CSS file is copied into the `/public` directory. In addition, it is now loaded into all Blade views that use Filament. If you're interested in only loading the CSS when it is required by an element on the page, check out the [Lazy loading CSS](#lazy-loading-css) section.

### Registering CSS files from a URL

If you want to register a CSS file from a URL, you may do so. These assets will be loaded on every page as normal, but not copied into the `/public` directory when the `php artisan filament:assets` command is run. This is useful for registering external stylesheets from a CDN, or stylesheets that you are already compiling directly into the `/public` directory:

```php
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;

FilamentAsset::register([
    Css::make('example-external-stylesheet', 'https://example.com/external.css'),
    Css::make('example-local-stylesheet', asset('css/local.css')),
]);
```

### Registering CSS variables

Sometimes, you may wish to use dynamic data from the backend in CSS files. To do this, you can use the `FilamentAsset::registerCssVariables()` method in the `boot()` method of a service provider:

```php
use Filament\Support\Facades\FilamentAsset;

FilamentAsset::registerCssVariables([
    'background-image' => asset('images/background.jpg'),
]);
```

Now, you can access these variables from any CSS file:

```css
background-image: var(--background-image);
```

### Lazy loading CSS

Registered CSS files load on every Filament page by default. To load a file only where it is needed, register it with `loadedOnRequest()` and use the bundled `x-load-css` directive on the element that requires it:

```blade
<div
    x-data="{}"
    x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('custom-stylesheet'))]"
>
    <!-- ... -->
</div>
```

To prevent the CSS file from being loaded automatically, you can use the `loadedOnRequest()` method:

```php
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;

FilamentAsset::register([
    Css::make('custom-stylesheet', __DIR__ . '/../../resources/css/custom.css')->loadedOnRequest(),
]);
```

If your CSS file was [registered to a plugin](#registering-assets-for-a-plugin), you must pass that in as the second argument to the `FilamentAsset::getStyleHref()` method:

```blade
<div
    x-data="{}"
    x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('custom-stylesheet', package: 'danharrin/filament-blog'))]"
>
    <!-- ... -->
</div>
```

### Using Tailwind CSS in plugins

Typically, registering CSS files is used to register custom stylesheets for your application. If you want to process these files using Tailwind CSS, you need to consider the implications of that, especially if you are a plugin developer.

Tailwind builds are unique to every application - they contain a minimal set of utility classes, only the ones that you are actually using in your application. This means that if you are a plugin developer, you probably should not be building your Tailwind CSS files into your plugin. Instead, you should provide the raw CSS files and instruct the user that they should build the Tailwind CSS file themselves. To do this, they need to add your vendor directory to their custom theme's CSS file using the `@source` directive. In their [custom theme](../styling#creating-a-custom-theme) CSS file (e.g., `resources/css/filament/admin/theme.css`), they should add:

```css
@import "tailwindcss";

@source '../../../../app/Filament/**/*';
@source '../../../../resources/views/filament/**/*';
@source '../../../../vendor/danharrin/filament-blog/resources/views/**/*'; /* Your plugin's vendor directory */
```

This means that when they build their Tailwind CSS file, it will include all the utility classes that are used in your plugin's views, as well as the utility classes that are used in their application and the Filament core.

If your plugin must also work with Filament's default stylesheet, [register a compiled stylesheet](#registering-css-files) containing the styles it needs. The default stylesheet does not include every Tailwind utility used by third-party plugins, and users without a custom theme are not compiling those utilities themselves.

## Registering JavaScript files

To register a JavaScript file with the asset system, use the `FilamentAsset::register()` method in the `boot()` method of a service provider. You must pass in an array of `Js` objects, which each represents a JavaScript file that should be registered in the asset system.

Each `Js` object has a unique ID and a path to the JavaScript file:

```php
use Filament\Support\Assets\Js;

FilamentAsset::register([
    Js::make('custom-script', __DIR__ . '/../../resources/js/custom.js'),
]);
```

In this example, we use `__DIR__` to generate a relative path to the asset from the current file. For instance, if you were adding this code to `/app/Providers/AppServiceProvider.php`, then the JavaScript file should exist in `/resources/js/custom.js`.

Now, when the `php artisan filament:assets` command is run, this JavaScript file is copied into the `/public` directory. In addition, it is now loaded into all Blade views that use Filament. If you're interested in only loading the JavaScript when it is required by an element on the page, check out the [Lazy loading JavaScript](#lazy-loading-javascript) section.

### Registering JavaScript files from a URL

If you want to register a JavaScript file from a URL, you may do so. These assets will be loaded on every page as normal, but not copied into the `/public` directory when the `php artisan filament:assets` command is run. This is useful for registering external scripts from a CDN, or scripts that you are already compiling directly into the `/public` directory:

```php
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Js;

FilamentAsset::register([
    Js::make('example-external-script', 'https://example.com/external.js'),
    Js::make('example-local-script', asset('js/local.js')),
]);
```

### Registering script data

Sometimes, you may wish to make data from the backend available to JavaScript files. To do this, you can use the `FilamentAsset::registerScriptData()` method in the `boot()` method of a service provider:

```php
use Filament\Support\Facades\FilamentAsset;

FilamentAsset::registerScriptData([
    'user' => [
        'name' => auth()->user()?->name,
    ],
]);
```

Now, you can access that data from any JavaScript file at runtime, using the `window.filamentData` object:

```js
window.filamentData.user.name // 'Dan Harrin'
```

### Using Vite-compiled JavaScript files

The `php artisan filament:assets` command copies files as-is into the `/public` directory without bundling or resolving dependencies. This means that if your JavaScript file uses `import` statements to pull in npm packages, the browser will not be able to resolve them. To use JavaScript files that require bundling, you should compile them with [Vite](https://vitejs.dev) first, and then register the compiled output as a URL-based asset.

First, add your JavaScript file as an entry point in your `vite.config.js`:

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/timezone.js', // Your custom script
            ],
        }),
    ],
})
```

Then, compile the assets using Vite:

```bash
npm run build
```

Finally, register the compiled asset using `Vite::asset()` to resolve the versioned URL:

```php
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Vite;

FilamentAsset::register([
    Js::make('timezone', Vite::asset('resources/js/timezone.js')),
]);
```

This approach also works for TypeScript files or any other JavaScript that needs a build step. Since `Vite::asset()` returns a URL, the asset will not be copied by `php artisan filament:assets` — it is served directly from Vite's build output.

<Aside variant="info">
    If you need to bundle JavaScript for an [asynchronous Alpine.js component](#asynchronous-alpinejs-components), consider using esbuild instead, as documented in that section.
</Aside>

### Lazy loading JavaScript

Registered JavaScript files load on every Filament page by default. To load a file only where it is needed, register it with `loadedOnRequest()` and use the bundled `x-load-js` directive on the element that requires it:

```blade
<div
    x-data="{}"
    x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('custom-script'))]"
>
    <!-- ... -->
</div>
```

To prevent the JavaScript file from being loaded automatically, you can use the `loadedOnRequest()` method:

```php
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;

FilamentAsset::register([
    Js::make('custom-script', __DIR__ . '/../../resources/js/custom.js')->loadedOnRequest(),
]);
```

If your JavaScript file was [registered to a plugin](#registering-assets-for-a-plugin), you must pass that in as the second argument to the `FilamentAsset::getScriptSrc()` method:

```blade
<div
    x-data="{}"
    x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('custom-script', package: 'danharrin/filament-blog'))]"
>
    <!-- ... -->
</div>
```

#### Asynchronous Alpine.js components

Sometimes, you may want to load external JavaScript libraries for your Alpine.js-based components. The best way to do this is by storing the compiled JavaScript and Alpine component in a separate file, and letting us load it whenever the component is rendered.

Firstly, you should install [esbuild](https://esbuild.github.io) via NPM, which we will use to create a single JavaScript file containing your external library and Alpine component:

```bash
npm install esbuild --save-dev
```

Then, you must create a script to compile your JavaScript and Alpine component. You can put this anywhere, for example `bin/build.js`:

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
        setup(build) {
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

        init() {
            // Initialise the Alpine component here, if you need to.
        },

        // You can define any other Alpine.js functions here.
    }
}
```

Now, you can compile this file into `resources/js/dist/components/test-component.js` by running the following command:

```bash
node bin/build.js
```

If you want to watch for changes to this file instead of compiling once, try the following command:

```bash
node bin/build.js --dev
```

Now, you need to tell Filament to publish this compiled JavaScript file into the `/public` directory of the Laravel application, so it is accessible to the browser. To do this, you can use the `FilamentAsset::register()` method in the `boot()` method of a service provider, passing in an `AlpineComponent` object:

```php
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;

FilamentAsset::register([
    AlpineComponent::make('test-component', __DIR__ . '/../../resources/js/dist/components/test-component.js'),
]);
```

When you run `php artisan filament:assets`, the compiled file will be copied into the `/public` directory.

Finally, you can load this asynchronous Alpine component in your view using `x-load` attributes and the `FilamentAsset::getAlpineComponentSrc()` method:

```blade
<div
    x-load
    x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('test-component') }}"
    x-data="testComponent({
        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
    })"
>
    <input x-model="state" />
</div>
```

This example is for a [custom form field](../forms/custom-fields). It passes the `state` in as a parameter to the `testComponent()` function, which is entangled with a Livewire component property. You can pass in any parameters you want, and access them in the `testComponent()` function. If you're not using a custom form field, you can ignore the `state` parameter in this example.

The `x-load` attributes come from the [Async Alpine](https://async-alpine.dev/docs/strategies) package, and any features of that package can be used here.

### Building lazy-loaded ES modules

Some components load an ES module on demand rather than running a script on every page. Unlike a script included only for its side effects, an imported module must preserve the exports expected by its consumer, such as a default function that mounts a component.

Build the module in the application or plugin that owns it. Framework-free JavaScript needs no framework packages. If your component uses a framework, install its runtime and compiler dependencies in the project that builds the module. For a Vite 8 application, use the command for your chosen framework:

```bash
# React
npm install --save-dev react react-dom

# Vue
npm install --save-dev vue @vitejs/plugin-vue

# Svelte
npm install --save-dev svelte @sveltejs/vite-plugin-svelte
```

Use framework and compiler plugin versions compatible with your installed Vite version and component code. For TypeScript, also install a compatible TypeScript compiler; React additionally needs `@types/react` and `@types/react-dom`. Vite transpiles TypeScript but does not type-check it. Use `tsc`, `vue-tsc`, or `svelte-check` as appropriate for your component syntax.

For example, if your module is `resources/js/components/custom-component.js`, add it to your Laravel Vite plugin's `input` list and preserve entry exports in the production build:

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/components/custom-component.js',
            ],
        }),
    ],
    build: {
        rolldownOptions: { preserveEntrySignatures: 'exports-only' },
    },
})
```

For Vite versions using Rollup, use `rollupOptions` instead of `rolldownOptions`. Without preserving entry signatures, the production build may remove exports that are not used by another build entry, even though your component imports them at runtime. Adapt your existing configuration rather than replacing its plugins or entry points.

For React JSX, use a `.jsx` or `.tsx` entry. Vite supports JSX compilation; configure the JSX transform to match your code. For Vue single-file components, import and enable `@vitejs/plugin-vue` in the `plugins` array. For Svelte, import and enable `@sveltejs/vite-plugin-svelte`. If your Svelte entry uses runes, give it a `.svelte.js` or `.svelte.ts` extension so the compiler processes it. Vue and Svelte entries can import their `.vue` or `.svelte` component files.

Run `npm run build`, then resolve the module URL in PHP:

```php
use Illuminate\Support\Facades\Vite;

$moduleUrl = Vite::asset('resources/js/components/custom-component.js');
```

Pass this URL to the component or JavaScript code responsible for importing the module. Do not import it from your main application entry or include it with `@vite()`, as that would load it eagerly. `Vite::asset()` resolves the development URL or the production manifest's hashed URL. Include the Vite client through your normal application layout during development. Lazy loading alone does not provide automatic state-preserving hot replacement for an already mounted component.

<Aside variant="info">
    Importing a built JavaScript URL does not load CSS listed only in Vite's manifest. Include the component's CSS through your application's stylesheet or [register it separately](#registering-css-files). The browser caches imported modules by URL, so use versioned or hashed URLs when deploying changes.
</Aside>

#### Building modules with esbuild

You can use esbuild instead of Vite. Install `esbuild` and any runtime dependencies used by your component as development dependencies in the project that builds the module. Create `build.mjs`:

```js
import { build } from 'esbuild'

await build({
    entryPoints: { 'custom-component': 'resources/js/components/custom-component.js' },
    outdir: 'dist',
    bundle: true,
    format: 'esm',
    minify: true,
    define: { 'process.env.NODE_ENV': '"production"' },
})
```

Run `node build.mjs`. This bundles imported runtime dependencies into the module without requiring globals or an import map. For React, install `react` and `react-dom`, use a `.jsx` or `.tsx` entry, and either import React for esbuild's classic JSX transform or configure `jsx: 'automatic'`.

Vue single-file components and Svelte components require their framework compilers, which esbuild does not include. Use a build integration that supports your component syntax, styles, and TypeScript requirements, or use the Vite plugins described above. The resulting module must bundle its runtime dependencies and retain the exports expected by its consumer, whichever build tool you choose.

### Publishing ES modules in plugins

Build reusable component modules in your plugin repository and include the generated `dist/` directory in your Composer release. Consumers should not need npm dependencies or Vite configuration to use your component.

Register the prebuilt entry in your [plugin's service provider](../plugins/getting-started#registering-assets), using `loadedOnRequest()` to prevent Filament from loading it on every page:

```php
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;

FilamentAsset::register([
    Js::make('custom-component', __DIR__ . '/../dist/custom-component.js')
        ->loadedOnRequest(),
], package: 'vendor/custom-component');
```

Use `FilamentAsset::getScriptSrc('custom-component', 'vendor/custom-component')` to obtain the URL for your component's module loader. `loadedOnRequest()` only prevents eager loading; your component must still import the URL when needed. Consumers run `php artisan filament:assets` after installing or upgrading your package. `getScriptSrc()` includes the Composer package version in the entry URL. See [creating a plugin](../plugins/getting-started#creating-a-plugin) for package and service-provider setup.

#### Publishing stylesheets and shared chunks

If your build produces separate CSS, [register it explicitly](#registering-css-files). Importing the JavaScript URL does not attach esbuild's CSS output. Keep selectors package-specific.

For several component modules sharing dependencies, you can add `splitting: true` and `chunkNames: 'chunks/[name]-[hash]'` to esbuild. Publish every chunk at the relative path used by the entry. Chunk filenames need content hashes because relative imports do not inherit the entry URL's version query.

For a build that emits JavaScript and CSS in subdirectories, replace the single-entry registration above with this in your service provider:

```php
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\File;

foreach (File::allFiles(__DIR__ . '/../dist') as $file) {
    $extension = $file->getExtension();

    if (! in_array($extension, ['js', 'css'])) {
        continue;
    }

    $id = str_replace('\\', '/', $file->getRelativePathname());
    $id = substr($id, 0, -(strlen($extension) + 1));

    FilamentAsset::register([
        $extension === 'css'
            ? Css::make($id, $file->getPathname())
            : Js::make($id, $file->getPathname())->loadedOnRequest(),
    ], package: 'vendor/custom-component');
}
```

This preserves relative paths for JavaScript chunks and registers generated stylesheets. The stylesheets load on every Filament page by default; use [lazy loading CSS](#lazy-loading-css) if you only need them alongside the component. JavaScript and CSS are published into separate directories. If your CSS references fonts or images, publish those files separately at the URLs your stylesheet expects.

## Content Security Policy nonces

If your application sends a [Content Security Policy](security#content-security-policy-csp) header, Filament can add a `nonce` attribute to scripts rendered through `FilamentAsset`, including registered scripts and registered script data. Filament uses Laravel's `Vite` nonce, which is also used by Livewire:

```php
use Illuminate\Support\Facades\Vite;

Vite::useCspNonce($nonce);
```

You should call `Vite::useCspNonce()` for every request, before the response is rendered, using the same nonce that you add to the `Content-Security-Policy` header. You can usually do this in the middleware that creates the policy.

<Aside variant="info">
    The `Vite` facade is only used to share the nonce between Laravel, Livewire, and Filament. Your application does not need to compile its assets with Vite or use the `@vite` Blade directive, so this also works in applications that use Laravel Mix.
</Aside>

<Aside variant="info">
    Scripts registered with `Js::make()->html()`, where the HTML already contains a `<script>` element, are rendered exactly as you provide them. Filament does not modify that markup, so you should add the nonce yourself using `Vite::cspNonce()`.
</Aside>

## Ignoring published assets in version control

The files that the `php artisan filament:assets` command copies into the `/public` directory for Filament's own packages are generated, so there is no need to commit them to version control. When you run `php artisan filament:install`, Filament adds the following rules to your app's `.gitignore` file, if they are not already there:

```
/public/css/filament
/public/fonts/filament
/public/js/filament
```

If you have customized the `assets_path` in the `config/filament.php` file, the rules use that path instead, for example `/public/filament/css/filament`.

Assets registered by your app or third-party plugins may be published outside these `filament` directories. You should add their generated paths to `.gitignore` separately if needed.

The `/public/css/filament` rule also ignores any custom themes that you compile directly into that directory instead of through Vite. You should compile those themes as part of your deployment process, since the `filament:assets` command does not build them.

Since Filament's published package assets are not committed, you should run `php artisan filament:assets` when you deploy your app. The `php artisan filament:install` command adds `@php artisan filament:upgrade` to the `post-autoload-dump` scripts in your app's `composer.json` file, which runs the `filament:assets` command for you each time Composer dumps the autoloader.
