---
title: Installation
---

## Requirements

Filament has a few requirements to run:

- PHP 8.0+
- Laravel v8.0+
- Livewire v2.0+

Notifications come pre-installed inside the [admin panel 2.x](/docs/admin/2.x), but you must still follow the installation instructions below if you're using it in the rest of your app.

First, require the notifications package using Composer:

```bash
composer require filament/notifications:"^2.0"
```

## New Laravel projects

To get started with notifications quickly, you can set up [Livewire](https://laravel-livewire.com), [Alpine.js](https://alpinejs.dev) and [Tailwind CSS](https://tailwindcss.com) with these commands:

```bash
php artisan notifications:install
npm install
npm run dev
```

> These commands will ruthlessly overwrite existing files in your application, hence why we only recommend using this method for new projects.

You're now ready to start [sending notifications](sending-notifications)!

![Filament Notifications](https://user-images.githubusercontent.com/44533235/180972526-da7ebb2f-24f6-4cf4-8e4c-d261574384ab.jpg)

## Existing Laravel projects

The package uses the following dependencies:

- [Alpine.js](https://alpinejs.dev)
- [Alpine Floating UI](https://github.com/awcodes/alpine-floating-ui)
- [Tailwind CSS](https://tailwindcss.com)

You may install these through NPM:

```bash
npm install alpinejs @awcodes/alpine-floating-ui tailwindcss --save-dev
```

### Configuring Tailwind CSS

To finish installing Tailwind, you must create a new `tailwind.config.js` file in the root of your project. The easiest way to do this is by running `npx tailwindcss init`.

In `tailwind.config.js`, add custom colors used by notifications:

```js
const colors = require('tailwindcss/colors') // [tl! focus]

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php', // [tl! focus]
    ],
    theme: {
        extend: {
            colors: { // [tl! focus:start]
                danger: colors.rose,
                primary: colors.blue,
                success: colors.green,
                warning: colors.yellow,
            }, // [tl! focus:end]
        },
    },
}
```

Of course, you may specify your own custom `primary`, `success`, `warning` and `danger` colors, which will be used instead.

### Bundling assets

New Laravel projects use Vite for bundling assets by default. However, your project may still use Laravel Mix. Read the steps below for the bundler used in your project.

#### Vite

If you're using Vite, you should manually install [Autoprefixer](https://github.com/postcss/autoprefixer) through NPM:

```bash
npm install autoprefixer --save-dev
```

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
import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin'; // [tl! focus]

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: [ // [tl! focus:start]
                ...refreshPaths,
                'app/Http/Livewire/**',
            ], // [tl! focus:end]
        }),
    ],
});
```

#### Laravel Mix

In your `webpack.mix.js` file, register Tailwind CSS as a PostCSS plugin:

```js
const mix = require('laravel-mix')

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'), // [tl! focus]
    ])
```

### Configuring styles

In `/resources/css/app.css`, import [Tailwind CSS](https://tailwindcss.com):

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

### Configuring scripts

In `/resources/js/app.js`, import [Alpine.js](https://alpinejs.dev), Alpine Floating UI and the `filament/notifications` plugin, and register them:

```js
import Alpine from 'alpinejs'
import AlpineFloatingUI from '@awcodes/alpine-floating-ui'
import NotificationsAlpinePlugin from '../../vendor/filament/notifications/dist/module.esm'

Alpine.plugin(AlpineFloatingUI)
Alpine.plugin(NotificationsAlpinePlugin)

window.Alpine = Alpine

Alpine.start()
```

### Compiling assets

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
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @livewireScripts
        @stack('scripts')
    </head>

    <body class="antialiased">
        {{ $slot }}

        @livewire('notifications')
    </body>
</html>
```

You're now ready to start [sending notifications](sending-notifications)!

## Publishing configuration

If you wish, you may publish the configuration of the package using:

```bash
php artisan vendor:publish --tag=notifications-config
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
