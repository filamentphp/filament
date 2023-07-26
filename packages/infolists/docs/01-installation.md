---
title: Installation
---

**Using infolists inside a [panel](../panels)**? You can skip this installation. This guide is for people who want to use the package inside their custom TALL-stack app.

## Requirements

Filament has a few requirements to run:

- PHP 8.1+
- Laravel v9.0+
- Livewire v3.0+

First, require the actions package using Composer:

```bash
composer require filament/infolists:"^3.0@beta"
```

## New Laravel projects

To get started with Filament quickly, you can set up [Livewire](https://laravel-livewire.com), [Alpine.js](https://alpinejs.dev) and [Tailwind CSS](https://tailwindcss.com) with these commands:

```bash
php artisan filament:install --scaffold --infolists
npm install
npm run dev
```

> These commands will ruthlessly overwrite existing files in your application, hence why we only recommend using this method for new projects.

## Existing Laravel projects

First, run the following command to install the package's assets:

```bash
php artisan filament:install --infolists
```

### Installing Tailwind CSS

First, use NPM to install Tailwind CSS and its `forms` and `typography` plugins:

```bash
npm install tailwindcss @tailwindcss/forms @tailwindcss/typography postcss autoprefixer --save-dev
```

Create a new `tailwind.config.js` file. Ensure that you add Filament's `preset` which configures colors and the plugins you installed:

```js
import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
```

### Configuring styles

In `resources/css/app.css`, import Tailwind CSS:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

Create a `postcss.config.js` file in the root of your project, and register Tailwind CSS and Autoprefixer as plugins:

```js
export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
}
```

You may also want to update your `vite.config.js` file to refresh the page after Livewire components have been updated:

```js
import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
            ],
        }),
    ],
})
```

Compile your new CSS and JS assets using `npm run dev`.

### Configuring layout

Finally, create a new `resources/views/components/layouts/app.blade.php` layout file for Livewire components:

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <meta name="application-name" content="{{ config('app.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @filamentStyles
        @vite('resources/css/app.css')
    </head>

    <body class="antialiased">
        {{ $slot }}

        @livewire('notifications')

        @filamentScripts
        @vite('resources/js/app.js')
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

We recommend adding the `filament:upgrade` command to your `composer.json`'s `post-autoload-dump` to run it automatically:

```json
"post-autoload-dump": [
    // ...
    "@php artisan filament:upgrade"
],
```

This should be done during the `filament:install` process, but double check it's been done.
