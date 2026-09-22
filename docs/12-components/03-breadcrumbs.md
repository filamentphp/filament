---
title: Breadcrumbs Blade component
---

import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

The breadcrumbs component is used to render a simple, linear navigation that informs the user of their current location within the application:

```blade
<x-filament::breadcrumbs :breadcrumbs="[
    '/' => 'Home',
    '/dashboard' => 'Dashboard',
    '/dashboard/users' => 'Users',
    '/dashboard/users/create' => 'Create User',
]" />
```

<AutoScreenshot name="components/breadcrumbs/simple" alt="Breadcrumbs navigation" version="4.x" />

The keys of the array are URLs that the user is able to click on to navigate, and the values are the text that will be displayed for each link.

## Using breadcrumbs in JavaScript components

You can use breadcrumbs in React 18 or 19, Vue 3.3.2 or later, and Svelte 5. Import the component from your Composer-installed Support package using your application's Vite build. Vue and Svelte require their respective Vite plugins. JavaScript applications can import these TypeScript components without converting their own code to TypeScript.

These components use the same markup and existing Filament theme CSS as Blade. They do not require Alpine or Livewire. The examples assume your component is in `resources/js/`; adjust the import path for other directories.

### Using React

```jsx
import React from 'react'
import Breadcrumbs from '../../vendor/filament/support/resources/js/react/Breadcrumbs'

export default function UserLocation() {
    return (
        <Breadcrumbs breadcrumbs={[
            { label: 'Home', href: '/' },
            { label: 'Users', href: '/users' },
            { label: 'Create user' },
        ]} />
    )
}
```

### Using Vue

```vue
<script setup>
import Breadcrumbs from '../../vendor/filament/support/resources/js/vue/Breadcrumbs.vue'

const breadcrumbs = [
    { label: 'Home', href: '/' },
    { label: 'Users', href: '/users' },
    { label: 'Create user' },
]
</script>

<template>
    <Breadcrumbs :breadcrumbs="breadcrumbs" />
</template>
```

### Using Svelte

```svelte
<script>
    import Breadcrumbs from '../../vendor/filament/support/resources/js/svelte/Breadcrumbs.svelte'

    const breadcrumbs = [
        { label: 'Home', href: '/' },
        { label: 'Users', href: '/users' },
        { label: 'Create user' },
    ]
</script>

<Breadcrumbs {breadcrumbs} />
```

### Configuring breadcrumbs

All three components accept an ordered `breadcrumbs` array, defaulting to `[]`. Each entry has a text `label` and an optional `href`. Omit `href` to render a `<span>` rather than a link. The final entry receives `aria-current="page"`, whether linked or not. Labels are escaped text, not HTML.

Pass native anchor attributes such as `target`, `rel`, and `title` on each entry. Native event handlers use `onClick` in React and Vue entries, and `onclick` in Svelte entries. Use `className` in React and `class` in Vue or Svelte. Svelte classes accept space-separated strings. Additional component attributes and events are forwarded to the `<nav>` element. Changing props updates the trail, including switching entries between links and plain text.

The navigation's default `aria-label` is `Breadcrumbs`. Pass a translated `aria-label` when needed; JavaScript components do not read Laravel's locale. Set `dir` on the document to switch between LTR and RTL chevrons. As with Blade, the existing theme does not support nesting a trail with the opposite direction inside an explicitly directed ancestor: both chevrons may be hidden.

### Customizing separators

PHP icon aliases do not apply to JavaScript components. To replace the chevrons, supply both `separator` and `separatorRtl` as React nodes, Vue named slots, or Svelte snippets. Each replacement is wrapped in the same decorative, direction-aware icon container as a Blade HTML icon alias. For example, in Vue:

```vue
<Breadcrumbs :breadcrumbs="breadcrumbs">
    <template #separator>/</template>
    <template #separatorRtl>\</template>
</Breadcrumbs>
```

### Integrating SPA navigation

Links use normal browser navigation by default, including modifier-clicks, downloads, and new tabs. Unlike Blade's server-side `generate_href_html()`, JavaScript breadcrumbs do not read panel SPA settings, URL exceptions, or prefetch configuration and do not emit `wire:navigate` directives.

Your host can attach a native click handler to eligible entries and call its router, or `Livewire.navigate()` when Livewire is available. The host must decide which URLs support SPA navigation, respecting panel exceptions. Only prevent the default action for unmodified primary-button clicks that the host will handle; preserve downloads, external URLs, and non-default targets. For an entry already selected by the host as SPA-compatible:

```js
const onClick = (event) => {
    const link = event.currentTarget

    if (
        event.defaultPrevented || event.button !== 0 ||
        event.altKey || event.ctrlKey || event.metaKey || event.shiftKey ||
        link.hasAttribute('download') ||
        (link.target && link.target !== '_self') ||
        link.origin !== window.location.origin ||
        !window.Livewire?.navigate
    ) return

    event.preventDefault()
    window.Livewire.navigate(link.href)
}

const breadcrumbs = [{ label: 'Users', href: '/users', onClick }]
```

Use `onclick: onClick` instead for Svelte. Keep framework-mounted content inside the host's `wire:ignore` boundary when embedding it in a Livewire component, and unmount it through the host renderer's lifecycle.
