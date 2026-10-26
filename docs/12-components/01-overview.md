---
title: Overview
---

## Introduction

Filament packages consume a set of core components that aim to provide a consistent and maintainable foundation for all interfaces. Some of these components are also available for use in your own applications and Filament plugins.

## Package components

The various packages in the Filament project can be used outside of a panel:

- [Action](action)
- [Form](form)
- [Infolist](infolist)
- [Notifications](notifications)
- [Schema](schema)
- [Table](table)
- [Widget](widget)

## Blade components

Aside from the core packages, all Filament projects can also consume the Blade components that Filament uses internally:

- [Avatar](avatar)
- [Badge](badge)
- [Button](button)
- [Breadcrumbs](breadcrumbs)
- [Callout](callout)
- [Checkbox](checkbox)
- [Dropdown](dropdown)
- [Empty state](empty-state)
- [Fieldset](fieldset)
- [Icon button](icon-button)
- [Input](input)
- [Input wrapper](input-wrapper)
- [Link](link)
- [Loading indicator](loading-indicator)
- [Modal](modal)
- [Pagination](pagination)
- [Section](section)
- [Select](select)
- [Tabs](tabs)

## Using React, Vue, and Svelte components

Some Support components, such as [Avatar](avatar#using-avatars-in-javascript-components), also have React, Vue, and Svelte implementations. Their documentation lists the available frameworks and supported props. These components use Filament's existing theme styles; they do not load a stylesheet themselves.

### Configuring runtime imports

Use the `@filament/support/react`, `@filament/support/vue`, and `@filament/support/svelte` aliases to import components from any directory in your application. These are aliases to source files installed by Composer, not npm packages. You do not need to install a separate Filament npm package.

Add the aliases for the frameworks you use to `resolve.alias` in your application's `vite.config.js`, alongside your existing plugins and build configuration. This example assumes the Vite configuration is beside `composer.json`:

```js
import { fileURLToPath } from 'node:url'
import { defineConfig } from 'vite'

export default defineConfig({
    // Keep your existing plugins and build configuration.
    resolve: {
        alias: {
            '@filament/support/react': fileURLToPath(new URL('./vendor/filament/support/resources/js/react', import.meta.url)),
            '@filament/support/vue': fileURLToPath(new URL('./vendor/filament/support/resources/js/vue', import.meta.url)),
            '@filament/support/svelte': fileURLToPath(new URL('./vendor/filament/support/resources/js/svelte', import.meta.url)),
        },
    },
})
```

Keep any other aliases your application uses. If you use a custom Composer vendor directory, adjust these paths. Configure `@vitejs/plugin-vue` for Vue or `@sveltejs/vite-plugin-svelte` for Svelte. For loading your compiled code in Filament, see [building lazy-loaded ES modules](../advanced/assets#building-lazy-loaded-es-modules).

Import each component as a default export from its framework directory. For React:

```js
import Avatar from '@filament/support/react/Avatar'
```

For Vue, keep the `.vue` extension:

```js
import Avatar from '@filament/support/vue/Avatar.vue'
```

For Svelte, keep the `.svelte` extension:

```js
import Avatar from '@filament/support/svelte/Avatar.svelte'
```

The same imports work in JavaScript and TypeScript applications. Vite compiles the components' TypeScript source even when your application uses JavaScript.

### Configuring TypeScript imports

For TypeScript applications, also add matching `paths` to your application's `tsconfig.json`. Merge these entries with your existing compiler options and paths:

```json
{
    "compilerOptions": {
        "paths": {
            "@filament/support/react/*": ["./vendor/filament/support/resources/js/react/*"],
            "@filament/support/vue/*": ["./vendor/filament/support/resources/js/vue/*"],
            "@filament/support/svelte/*": ["./vendor/filament/support/resources/js/svelte/*"]
        }
    }
}
```

These paths are relative to `tsconfig.json`, or to `compilerOptions.baseUrl` if you set it. Adjust them if your configuration is not in the application root. TypeScript paths provide type resolution; they do not configure runtime imports. You still need the Vite aliases above. JavaScript applications do not need a `tsconfig.json` for these imports.
