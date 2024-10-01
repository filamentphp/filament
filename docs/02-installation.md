---
title: Installation
contents: false
---
import RadioGroup from "@components/RadioGroup.astro"
import RadioGroupOption from "@components/RadioGroupOption.astro"

Filament requires the following to run:

- PHP 8.1+
- Laravel v10.0+
- Livewire v3.0+

Installation comes in two flavors, depending on whether you want to build an app using our panel builder, or if you want to use the components within the Blade views of your app:

<div x-data="{ package: (window.location.hash === '#components') ? 'components' : 'panels' }">

<RadioGroup model="package">
    <RadioGroupOption value="panels">
        Panel builder

        <span slot="description">
            Most people choose this option, to build an admin panel for their app. The panel builder combines all the individual components together into a cohesive framework. You can create as many panels as you like within a Laravel installation, but you only need to install it once.
        </span>
    </RadioGroupOption>

    <RadioGroupOption value="components">
        Individual components

        <span slot="description">
            If you are using Blade to build your app from scratch, you can install individual components from Filament to enrich your views.
        </span>
    </RadioGroupOption>
</RadioGroup>

<div x-show="package === 'panels'" x-cloak>

## Installing the panel builder

Install the Filament Panel Builder by running the following commands in your Laravel project directory:

```bash
composer require filament/filament

php artisan filament:install --panels
```

This will create and register a new [Laravel service provider](https://laravel.com/docs/providers) called `app/Providers/Filament/AdminPanelProvider.php`.

> If you get an error when accessing your panel, check that the service provider was registered in `bootstrap/providers.php` (Laravel 11 and above) or `config/app.php` (Laravel 10 and below). If not, you should manually add it.

You can create a new user account with the following command:

```bash
php artisan make:filament-user
```

Open `/admin` in your web browser, sign in, and start building your app!

</div>

<div
    x-show="package === 'components'"
    x-data="{ laravelProject: 'new' }"
    x-cloak
>

## Installing the individual components

To start with, you need to install the Filament components you'd like to use with Composer:

```bash
composer require
    filament/forms # A variety of field types and layouts, complete with integrated frontend and backend validation.
    filament/infolists # The easiest way to create read-only views of data, using a combination of different data types and layouts.
    filament/tables # Integrative tables and grids, complete with advanced filtering, sorting and grouping options.
    filament/actions # Buttons that open complex modals that can be used to confirm actions, collect data, or display information.
    filament/notifications # Send flash messages to the user after an interaction, through a live websocket connection, or via Laravel's database notifications.
    filament/widgets # Dashboard components like charts and stats overviews, which can be integrated with static or live data.
```

You can install additional packages later in your project without needing to follow all these steps.

If you would like to only use the set of [Blade UI components](ui), you need to require `filament/support` at this stage.

<RadioGroup model="laravelProject">
    <RadioGroupOption value="new">
        New Laravel projects

        <span slot="description">
            Get started with the Filament components quickly by running a simple command. However, this could overwrite modified files in your app, so is only suitable for new Laravel projects.
        </span>
    </RadioGroupOption>

    <RadioGroupOption value="existing">
        Existing Laravel projects

        <span slot="description">
            If you have an existing Laravel project, you can still install Filament, but should do so manually to avoid breaking any existing functionality.
        </span>
    </RadioGroupOption>
</RadioGroup>

<div x-show="laravelProject === 'new'" x-cloak>

To quickly get started with Filament in a new Laravel project, run the following commands to install [Livewire](https://livewire.laravel.com), [Alpine.js](https://alpinejs.dev), and [Tailwind CSS](https://tailwindcss.com):

> Since these commands will overwrite existing files in your application, only run this in a new Laravel project!

Run the following command to install the Filament frontend assets:

```bash
php artisan filament:install --scaffold

npm install

npm run dev
```

When scaffolding, if you have the [notifications](notifications) package installed, Filament will ask you if you would like to install the required Livewire component into the default layout file. This is required if you wish to send flash notifications to your users through Filament.

</div>

<div x-show="laravelProject === 'existing'" x-cloak>

Run the following command to install the Filament frontend assets:

```bash
php artisan filament:install
```

### Installing Tailwind CSS

Run the following command to install Tailwind CSS with the Tailwind Forms and Typography plugins:

```bash
npm install tailwindcss @tailwindcss/forms @tailwindcss/typography postcss postcss-nesting autoprefixer --save-dev
```

Create a new `tailwind.config.js` file and add the Filament `preset` *(includes the Filament color scheme and the required Tailwind plugins)*:

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

Add Tailwind's CSS layers to your `resources/css/app.css`:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
@tailwind variants;
```

Create a `postcss.config.js` file in the root of your project and register Tailwind CSS, PostCSS Nesting and Autoprefixer as plugins:

```js
export default {
    plugins: {
        'tailwindcss/nesting': 'postcss-nesting',
        tailwindcss: {},
        autoprefixer: {},
    },
}
```

### Automatically refreshing the browser

You may also want to update your `vite.config.js` file to refresh the page automatically when Livewire and Filament components are updated:

```js
import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                ...refreshPaths,
                'app/Filament/**',
                'app/Livewire/**',
            ],
        }),
    ],
})
```

### Compiling assets

Compile your new CSS and Javascript assets using `npm run dev`.

### Configuring your layout

Create a new `resources/views/components/layouts/app.blade.php` layout file for Livewire components:

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

        @livewire('notifications') {{-- Only required if you wish to send flash notifications --}}

        @filamentScripts
        @vite('resources/js/app.js')
    </body>
</html>
```

Please note the `@livewire('notifications')` line above - this is only required if you have the [notifications](notifications) package installed, and you wish to send flash notifications to your users through Filament.

</div>

</div>

</div>
