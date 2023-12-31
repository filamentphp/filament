---
title: Assets
---
import LaracastsBanner from "@components/LaracastsBanner.astro"

<LaracastsBanner
    title="Registering Plugin Assets"
    description="Watch the Build Advanced Components for Filament series on Laracasts - it will teach you how to get started with registering assets into a plugin. Alternatively, continue reading this text-based guide."
    url="https://laracasts.com/series/build-advanced-components-for-filament/episodes/14"
    series="building-advanced-components"
/>

## Overview

All packages in the Filament ecosystem share an asset management system. This allows both official plugins and third-party plugins to register CSS and JavaScript files that can then be consumed by Blade views.

## The `FilamentAsset` facade

The `FilamentAsset` facade is used to register files into the asset system. These files may be sourced from anywhere in the filesystem, but are then copied into the `/public` directory of the application when the `php artisan filament:assets` command is run. By copying them into the `/public` directory for you, we can predictably load them in Blade views, and also ensure that third party packages are able to load their assets without having to worry about where they are located.

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

### Using Tailwind CSS in plugins

Typically, registering CSS files is used to register custom stylesheets for your application. If you want to process these files using Tailwind CSS, you need to consider the implications of that, especially if you are a plugin developer.

Tailwind builds are unique to every application - they contain a minimal set of utility classes, only the ones that you are actually using in your application. This means that if you are a plugin developer, you probably should not be building your Tailwind CSS files into your plugin. Instead, you should provide the raw CSS files and instruct the user that they should build the Tailwind CSS file themselves. To do this, they probably just need to add your vendor directory into the `content` array of their `tailwind.config.js` file:

```js
export default {
    content: [
        './resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/danharrin/filament-blog/resources/views/**/*.blade.php', // Your plugin's vendor directory
    ],
    // ...
}
```

This means that when they build their Tailwind CSS file, it will include all the utility classes that are used in your plugin's views, as well as the utility classes that are used in their application and the Filament core.

However, with this technique, there might be extra complications for users who use your plugin with the [Panel Builder](../panels). If they have a [custom theme](../panels/themes), they will be fine, since they are building their own CSS file anyway using Tailwind CSS. However, if they are using the default stylesheet which is shipped with the Panel Builder, you might have to be careful about the utility classes that you use in your plugin's views. For instance, if you use a utility class that is not included in the default stylesheet, the user is not compiling it themselves, and it will not be included in the final CSS file. This means that your plugin's views might not look as expected. This is one of the few situations where I would recommend compiling and [registering](#registering-css-files) a Tailwind CSS-compiled stylesheet in your plugin.

### Lazy loading CSS

By default, all CSS files registered with the asset system are loaded in the `<head>` of every Filament page. This is the simplest way to load CSS files, but sometimes they may be quite heavy and not required on every page. In this case, you can leverage the [Alpine.js Lazy Load Assets](https://github.com/tanthammar/alpine-lazy-load-assets) package that comes bundled with Filament. It allows you to easily load CSS files on-demand using Alpine.js. The premise is very simple, you use the `x-load-css` directive on an element, and when that element is loaded onto the page, the specified CSS files are loaded into the `<head>` of the page. This is perfect for both small UI elements and entire pages that require a CSS file:

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

### Lazy loading JavaScript

By default, all JavaScript files registered with the asset system are loaded at the bottom of every Filament page. This is the simplest way to load JavaScript files, but sometimes they may be quite heavy and not required on every page. In this case, you can leverage the [Alpine.js Lazy Load Assets](https://github.com/tanthammar/alpine-lazy-load-assets) package that comes bundled with Filament. It allows you to easily load JavaScript files on-demand using Alpine.js. The premise is very simple, you use the `x-load-js` directive on an element, and when that element is loaded onto the page, the specified JavaScript files are loaded at the bottom of the page. This is perfect for both small UI elements and entire pages that require a JavaScript file:

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

<LaracastsBanner
    title="Using Async Alpine components"
    description="Watch the Build Advanced Components for Filament series on Laracasts - it will teach you how to get started with Async Alpine components into a plugin."
    url="https://laracasts.com/series/build-advanced-components-for-filament/episodes/15"
    series="building-advanced-components"
/>

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

Finally, you can load this asynchronous Alpine component in your view using `ax-load` attributes and the `FilamentAsset::getAlpineComponentSrc()` method:

```blade
<div
    x-ignore
    ax-load
    ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('test-component') }}"
    x-data="testComponent({
        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
    })"
>
    <input x-model="state" />
</div>
```

This example is for a [custom form field](../forms/fields/custom). It passes the `state` in as a parameter to the `testComponent()` function, which is entangled with a Livewire component property. You can pass in any parameters you want, and access them in the `testComponent()` function. If you're not using a custom form field, you can ignore the `state` parameter in this example.

The `ax-load` attributes come from the [Async Alpine](https://async-alpine.dev/docs/strategies) package, and any features of that package can be used here.

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

### Registering JavaScript files from a URL

If you want to register a JavaScript file from a URL, you may do so. These assets will be loaded on every page as normal, but not copied into the `/public` directory when the `php artisan filament:assets` command is run. This is useful for registering external scripts from a CDN, or scripts that you are already compiling directly into the `/public` directory:

```php
use Filament\Support\Assets\Js;

FilamentAsset::register([
    Js::make('example-external-script', 'https://example.com/external.js'),
    Js::make('example-local-script', asset('js/local.js')),
]);
```
