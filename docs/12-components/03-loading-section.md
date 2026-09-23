---
title: Loading section component
---

## Introduction

You can reserve space for a loading section using a pulsing placeholder:

```blade
<x-filament::loading-section />
```

This is an empty section with a screen-reader-only loading label, not a spinner. It uses the existing section theme and only pulses when the user has not requested reduced motion.

## Setting the height and label

The height defaults to `8rem`. Set `height` to a CSS length and `loadingLabel` to your translated loading message (`loading-label` in Blade or Vue). `null` uses the default; an empty label stays empty. Blade resolves the label through Laravel translations; the JavaScript default is `Loading...`.

## Positioning the placeholder

Use `columnSpan` and `columnStart` (`column-span` and `column-start` in Blade or Vue) with Filament's existing grid. Scalars apply at `lg`; objects map breakpoints such as `default`, `sm`, `md`, `lg`, `xl`, and `2xl` to values. The adapters emit the same grid classes and CSS variables as Blade; they do not create a grid or additional CSS. Only breakpoints present in your theme take effect. Container breakpoints such as `@sm` require an ancestor with `container-type: inline-size`.

Column spans accept `full`. Only `columnSpan={{ default: 'hidden' }}` hides the placeholder. A scalar `hidden` applies at `lg` and does **not** hide it. Column starts accept numbers and decimal numeric strings, including exponent notation: `'1e1'` becomes `10`, and `'2.9'` truncates to `2`, matching PHP's integer coercion. Nonnumeric starts such as `full` and `hidden` throw `TypeError`; they are not start keywords. Values must fit a PHP integer, and JavaScript numbers remain subject to its numeric precision limits.

## Using loading sections in React, Vue, and Svelte

Import from your Composer-installed Support package using your application's Vite build and the corresponding Vue or Svelte plugin. React 18 or 19, Vue 3.3.2 or later, and Svelte 5 are supported in plain JavaScript and strict TypeScript. These examples assume a file in `resources/js/`.

### Using React

```jsx
import React from 'react'
import LoadingSection from '../../vendor/filament/support/resources/js/react/LoadingSection'

export default function Projects({ loading, children }) {
    return loading ? <LoadingSection loadingLabel="Loading projects" /> : children
}
```

### Using Vue

```vue
<script setup>
import LoadingSection from '../../vendor/filament/support/resources/js/vue/LoadingSection.vue'
defineProps({ loading: Boolean })
</script>

<template>
    <LoadingSection v-if="loading" loading-label="Loading projects" />
    <slot v-else />
</template>
```

### Using Svelte

```svelte
<script>
    import LoadingSection from '../../vendor/filament/support/resources/js/svelte/LoadingSection.svelte'
    let { loading, children } = $props()
</script>

{#if loading}
    <LoadingSection loadingLabel="Loading projects" />
{:else}
    {@render children?.()}
{/if}
```

### Owning loading and accessibility

The placeholder defaults to Blade's `role="status"` and `aria-busy="true"`. Native attributes can override these when the host owns a containing busy region or status. The adapters do not add announcements, completion messages, or error behavior. The host must decide when to mount the placeholder, replace it with content, clear its busy state, disable actions, and announce success or errors if appropriate. There is no `loading` prop, asynchronous service, content slot, or automatic spinner. Use the separate `LoadingIndicator` adapter if your host needs a spinner elsewhere.

Livewire targets, delays, PHP lazy-loading, translation resolution, requests, cancellation, and stale-result protection remain host-owned. Abort or guard asynchronous work on unmount. The adapters themselves allocate no timers, listeners on global objects, or observers.

### Forwarding native attributes

Native attributes and events target the root div. Use `className`, a style object, and `onClick` in React; `class`, native Vue styles, and `@click` in Vue; or string `class` and `style` and `onclick` in Svelte. Native styles can override the generated height and grid variables. React's `ref` targets the div; Vue component refs expose `$el`; Svelte supports `bind:element`. Updates preserve the root until the host unmounts it.

Use Filament's existing Support section, loading-section, grid, and screen-reader CSS, already included in panel themes. No adapter stylesheet, Alpine, or Livewire is required. Inside Livewire, mount within `wire:ignore` and unmount through your renderer lifecycle.
