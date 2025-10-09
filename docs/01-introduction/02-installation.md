---
title: Installation
contents: false
---
import Aside from "@components/Aside.astro"
import RadioGroup from "@components/RadioGroup.astro"
import RadioGroupOption from "@components/RadioGroupOption.astro"

Filament requires the following to run:

- PHP 8.2+
- Laravel v11.28+
- Tailwind CSS v4.0+

Installation comes in two flavors, depending on whether you want to build an app using our panel builder or use the components within your app's Blade views:

<div x-data="{ package: (window.location.hash === '#components') ? 'components' : 'panels' }">

<RadioGroup model="package">
    <RadioGroupOption value="panels">
        Panel builder

        <span slot="description">
            Most people choose this option to build a panel (e.g., admin panel) for their app. The panel builder combines all the individual components into a cohesive framework. You can create as many panels as you like within a Laravel installation, but you only need to install it once.
        </span>
    </RadioGroupOption>

    <RadioGroupOption value="components">
        Individual components

        <span slot="description">
            If you are using Blade to build your app from scratch, you can install individual components from Filament to enrich your UI.
        </span>
    </RadioGroupOption>
</RadioGroup>

<div x-show="package === 'panels'" x-cloak>

## Installing the panel builder

Install the Filament Panel Builder by running the following commands in your Laravel project directory:

```bash
composer require filament/filament:"^4.0"

php artisan filament:install --panels
```

<Aside variant="warning">
    When using Windows PowerShell to install Filament, you may need to run the command below, since it ignores `^` characters in version constraints:

    ```bash
    composer require filament/filament:"~4.0"

    php artisan filament:install --panels
    ```
</Aside>

This will create and register a new [Laravel service provider](https://laravel.com/docs/providers) called `app/Providers/Filament/AdminPanelProvider.php`.

<Aside variant="tip">
    If you get an error when accessing your panel, check that the service provider is registered in `bootstrap/providers.php`. If it's not registered, you'll need to [add it manually](https://laravel.com/docs/providers#registering-providers).
</Aside>

You can create a new user account using the following command:

```bash
php artisan make:filament-user
```

Open `/admin` in your web browser, sign in, and [start building your app](../getting-started)!

</div>

<div
    x-show="package === 'components'" 
    x-data="{ laravelProject: 'new' }"
    x-cloak
>

## Installing the individual components

Install the Filament components you want to use with Composer:

```bash
composer require
    filament/tables:"^4.0"
    filament/schemas:"^4.0"
    filament/forms:"^4.0"
    filament/infolists:"^4.0"
    filament/actions:"^4.0"
    filament/notifications:"^4.0"
    filament/widgets:"^4.0"
```

You can install additional packages later in your project without having to repeat these installation steps.

<Aside variant="warning">
    When using Windows PowerShell to install Filament, you may need to run the command below, since it ignores `^` characters in version constraints:

    ```bash
    composer require
        filament/tables:"~4.0"
        filament/schemas:"~4.0"
        filament/forms:"~4.0"
        filament/infolists:"~4.0"
        filament/actions:"~4.0"
        filament/notifications:"~4.0"
        filament/widgets:"~4.0"
    ```
</Aside>

If you only want to use the set of [Blade UI components](../components), you'll need to require `filament/support` at this stage.

<RadioGroup model="laravelProject">
    <RadioGroupOption value="new">
        New Laravel projects

        <span slot="description">
            Get started with Filament components quickly by running a simple command. Note that this will overwrite any modified files in your app, so it's only suitable for new Laravel projects.
        </span>
    </RadioGroupOption>

    <RadioGroupOption value="existing">
        Existing Laravel projects

        <span slot="description">
            If you have an existing Laravel project, you can still install Filament, but should do so manually to preserve your existing functionality.
        </span>
    </RadioGroupOption>
</RadioGroup>

<div x-show="laravelProject === 'new'" x-cloak>

To quickly set up Filament in a new Laravel project, run the following commands to install [Livewire](https://livewire.laravel.com), [Alpine.js](https://alpinejs.dev), and [Tailwind CSS](https://tailwindcss.com):

<Aside variant="warning">
    These commands will overwrite existing files in your application. Only run them in a new Laravel project!
</Aside>

Run the following command to install the Filament frontend assets:

```bash
php artisan filament:install --scaffold

npm install

npm run dev
```

During scaffolding, if you have the [Notifications](../notifications) package installed, Filament will ask if you want to install the required Livewire component into your default layout file. This component is required if you want to send flash notifications to users through Filament.

</div>

<div x-show="laravelProject === 'existing'" x-cloak>

Run the following command to install the Filament frontend assets:

```bash
php artisan filament:install
```

### Installing Tailwind CSS

Run the following command to install Tailwind CSS and its Vite plugin, if you don't have those installed already:

```bash
npm install tailwindcss @tailwindcss/vite --save-dev
```

### Configuring styles

To configure Filament's styles, you need to import the CSS files for the Filament packages you installed, usually in `resources/css/app.css`.

Depending on which combination of packages you installed, you can import only the necessary CSS files, to keep your app's CSS bundle size small:

```css
@import 'tailwindcss';

/* Required by all components */
@import '../../vendor/filament/support/resources/css/index.css';

/* Required by actions and tables */
@import '../../vendor/filament/actions/resources/css/index.css';

/* Required by actions, forms and tables */
@import '../../vendor/filament/forms/resources/css/index.css';

/* Required by actions and infolists */
@import '../../vendor/filament/infolists/resources/css/index.css';

/* Required by notifications */
@import '../../vendor/filament/notifications/resources/css/index.css';

/* Required by actions, infolists, forms, schemas and tables */
@import '../../vendor/filament/schemas/resources/css/index.css';

/* Required by tables */
@import '../../vendor/filament/tables/resources/css/index.css';

/* Required by widgets */
@import '../../vendor/filament/widgets/resources/css/index.css';

@variant dark (&:where(.dark, .dark *));
```

### Configure the Vite plugin

If it isn't already set up, add the `@tailwindcss/vite` plugin to your Vite configuration (`vite.config.js`):

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
})
```

### Compiling assets

Compile your new CSS and JavaScript assets using `npm run dev`.

### Configuring your layout 

If you don't have a Blade layout file yet, create it at `resources/views/components/layouts/app.blade.php` by running the following command:

```bash
php artisan livewire:layout
```

Add the following code to your new layout file:

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

The important parts of this are the `@filamentStyles` in the `<head>` of the layout, and the `@filamentScripts` at the end of the `<body>`. Make sure to also include your app's compiled CSS and JavaScript files from Vite!

<Aside variant="info">
    The `@livewire('notifications')` line is required in the layout only if you have the [Notifications](../notifications) package installed and want to send flash notifications to users through Filament.
</Aside>

</div>

</div>

</div>
