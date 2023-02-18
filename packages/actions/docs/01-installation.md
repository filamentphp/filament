---
title: Installation
---

**Using actions inside the [app framework](../app)**? You can skip this installation. This guide is for people who want to use the package inside their custom TALL-stack app.

## Requirements

Filament has a few requirements to run:

- PHP 8.0+
- Laravel v8.0+
- Livewire v2.0+

First, require the actions package using Composer:

```bash
composer require filament/actions:"^3.0"
```

## New Laravel projects

To get started with Filament quickly, you can set up [Livewire](https://laravel-livewire.com), [Alpine.js](https://alpinejs.dev) and [Tailwind CSS](https://tailwindcss.com) with these commands:

```bash
php artisan filament:install --scaffold --actions
npm install
npm run dev
```

> These commands will ruthlessly overwrite existing files in your application, hence why we only recommend using this method for new projects.

## Existing Laravel projects

First, run the following command to install the package's assets:

```bash
php artisan filament:install --actions
```

### Installing Tailwind CSS

First, use NPM to install Tailwind CSS and its `forms` and `typography` plugins:

```bash
npm install tailwindcss @tailwindcss/forms @tailwindcss/typography postcss --save-dev
```

Create a new `tailwind.config.js` file. Ensure that you add Filament's `content` path, custom `colors`, and the `plugins` you installed:

```js
const colors = require('tailwindcss/colors')

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                danger: colors.rose,
                primary: colors.blue,
                secondary: colors.gray,
                success: colors.green,
                warning: colors.yellow,
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
```

Of course, you may specify your own custom `primary`, `secondary`, `success`, `warning` and `danger` colors, which will be used instead. But each color needs to be a [Tailwind CSS color](https://tailwindcss.com/docs/customizing-colors#color-palette-reference), or have all 50 - 900 variants specified - a single hex code or RGB value won't work here.

### Configuring styles

In `resources/css/app.css`, import Tailwind CSS, and the Filament forms CSS, which is used for action modal forms:

```css
@import '../../vendor/filament/forms/dist/index.css';

@tailwind base;
@tailwind components;
@tailwind utilities;
```

Most Laravel projects use Vite for bundling assets by default. However, your project may still use Laravel Mix. Read the steps below for the bundler used in your project.

#### Vite

Create a `postcss.config.js` file in the root of your project, and register Tailwind CSS and Autoprefixer as plugins:

```js
module.exports = {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
}
```

You may also want to update your `vite.config.js` file to refresh the page after Livewire components have been updated:

```js
import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin' // [tl! focus]

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [ // [tl! focus:start]
                ...refreshPaths,
                'app/Http/Livewire/**',
            ], // [tl! focus:end]
        }),
    ],
})
```

Compile your new CSS and JS assets using `npm run dev`.

#### Laravel Mix

In your `webpack.mix.js` file, register Tailwind CSS as a PostCSS plugin:

```js
const mix = require('laravel-mix')

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'), // [tl! focus]
    ])
```

Compile your new CSS and JS assets using `npm run dev`.

### Configuring layout

Finally, create a new `resources/views/layouts/app.blade.php` layout file for Livewire components:

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <meta name="application-name" content="{{ config('app.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <style>[x-cloak] { display: none !important; }</style>
        @livewireStyles
        @filamentStyles
        @vite('resources/css/app.css')
    </head>

    <body class="antialiased">
        {{ $slot }}

        @livewire('notifications')

        @livewireScripts
        @filamentScripts
        @vite('resources/js/app.js')
        <script src="//unpkg.com/alpinejs" defer></script>
    </body>
</html>
```

## Publishing configuration

If you wish, you may publish the configuration of the package using:

```bash
php artisan vendor:publish --tag=filament-config
```

## Upgrading

To upgrade the package to the latest version, you must run:

```bash
composer update
php artisan filament:upgrade
```

We recommend adding the `filament:upgrade` command to your `composer.json`'s `post-update-cmd` to run it automatically:

```json
"post-update-cmd": [
    // ...
    "@php artisan filament:upgrade"
],
```

This should be done during the `filament:install` process, but double check it's been done.
