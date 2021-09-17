---
title: Installation
---

## New Laravel projects

<iframe width="560" height="315" src="https://www.youtube.com/embed/iy1DO8JXRDQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

To get started with the form builder quickly, you can set up [Alpine.js](https://alpinejs.dev), [TailwindCSS](https://tailwindcss.com) and [Livewire](https://laravel-livewire.com) with just one command:

```
composer require filament/forms && php artisan forms:install && npm install && npm run dev
```

> This command will ruthlessly overwrite existing files in your application, hence why we only recommend using it for new projects.

You're now ready to start [building forms](building-forms)!

## Existing Laravel projects

> Please note that this package is incompatible with `filament/filament` v1, until v2 is released in late 2021. This is due to namespacing collisions.

<iframe width="560" height="315" src="https://www.youtube.com/embed/XslPKxtMR70" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

You may download the form builder using Composer:

```bash
composer require filament/forms
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
        './resources/**/*.blade.php', // [tl! focus:start]
        './vendor/filament/forms/resources/views/**/*.blade.php', // [tl! focus:end]
    ],
    theme: {
        extend: {
            colors: { // [tl! focus:start]
                danger: colors.rose,
                primary: colors.blue,
            }, // [tl! focus:end]
        },
    },
    plugins: [
        require('@tailwindcss/forms'), // [tl! focus:start]
        require('@tailwindcss/typography'), // [tl! focus:end]
    ],
}
```

Of course, you may specify your own custom `primary` and `danger` colors, which will be used instead.

In your `webpack.mix.js` file, Register Tailwind CSS as a PostCSS plugin :

```js
const mix = require('laravel-mix')

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'), // [tl! focus]
    ])
```

In `/resources/css/app.css`, import `filament/forms` vendor CSS and [TailwindCSS](https://tailwindcss.com):

```css
@import '../../vendor/filament/forms/dist/module.esm.css';

@tailwind base;
@tailwind components;
@tailwind utilities;
```

In `/resources/js/app.js`, import [Alpine.js](https://alpinejs.dev), the `filament/forms` plugin, and register it:

```js
import Alpine from 'alpinejs'
import FormsAlpinePlugin from '../../vendor/filament/forms/dist/module.esm'

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
        <style>[x-cloak] { display: none !important; }</style>
        @livewireStyles
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Scripts -->
        @livewireScripts
        <script src="{{ mix('js/app.js') }}" defer></script>
        <script src="https://unpkg.com/dayjs@1.8.21/locale/{{ str_replace('_', '-', app()->getLocale()) }}.js"></script>
    </head>

    <body class="antialiased">
        {{ $slot }}
    </body>
</html>
```

You're now ready to start [building forms](building-forms)!
