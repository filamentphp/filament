---
title: Getting Started
---

## Installation

> This guide assumes that you are using a fresh Laravel installation. If you have already completed some of these steps to set up your existing app, feel free to skip them.

You may download the form builder and Livewire using Composer:

```bash
composer require filament/forms livewire/livewire
```

The package uses [Alpine.js](https://alpinejs.dev),  [Tailwind CSS](https://tailwindcss.com), the Tailwind Forms plugin, and the Tailwind Typography plugin. You may install these through NPM:

```bash
npm install alpinejs tailwindcss @tailwindcss/forms @tailwindcss/typography --save-dev
```

To finish installing Tailwind, you must create a new `tailwind.config.js` file in the root of your project. The easiest way to do this is by running `npm tailwindcss init`.

In `tailwind.config.js`, enable JIT mode, register the plugins you installed, and add custom colors used by the form builder:

```js
const colors = require('tailwindcss/colors')

module.exports = {
    mode: 'jit',
    purge: [
        './resources/**/*.blade.php', // [tl! add:start]
        './vendor/filament/filament/packages/forms/resources/views/**/*.blade.php', // [tl! add:end]
    ],
    theme: {
        extend: {
            colors: { // [tl! add:start]
                danger: colors.rose,
                primary: colors.yellow,
            }, // [tl! add:end]
        },
    },
    plugins: [
        require('@tailwindcss/forms'), // [tl! add:start]
        require('@tailwindcss/typography'), // [tl! add:end]
    ],
}
```

Of course, you may specify your own custom `primary` and `danger` colors, which will be used instead.

In your `webpack.mix.js` file, Register Tailwind CSS as a PostCSS plugin :

```js
const mix = require('laravel-mix')

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'), // [tl! add]
    ])
```

In `/resources/css/app.css`, import `filament/forms` vendor CSS, [TailwindCSS](https://tailwindcss.com), as well as [Alpine.js' `x-cloak`](https://alpinejs.dev/directives/cloak) directive:

```css
@import '../../vendor/filament/filament/packages/forms/dist/module.esm.css';

@tailwind base;
@tailwind components;
@tailwind utilities;

[x-cloak] {
    display: none !important;
}
```

In `/resources/js/app.js`, import [Alpine.js](https://alpinejs.dev), the `filament/forms` plugin, and register it:

```js
import Alpine from 'alpinejs'
import FormsAlpinePlugin from '../../vendor/filament/filament/packages/forms/dist/module.esm'

Alpine.plugin(FormsAlpinePlugin)

window.Alpine = Alpine

Alpine.start()
```

Compile your new CSS and JS assets using `npm run dev`.

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

        <!-- Styles -->
        @livewireStyles
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Scripts -->
        @livewireScripts
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>

    <body class="antialiased">
        {{ $slot }}
    </body>
</html>
```
