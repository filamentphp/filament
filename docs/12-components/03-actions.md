---
title: Actions layout component
---

## Introduction

You can group host-rendered buttons and links using the Support actions layout:

```blade
<x-filament::actions alignment="end">
    <x-filament::button type="submit">Save changes</x-filament::button>
    <x-filament::link href="/projects">Cancel</x-filament::link>
</x-filament::actions>
```

This is a layout container, not the [PHP Action system](action).

## Setting the alignment

`alignment` defaults to `start`. You can use `start`, `left`, `center`, `end`, `right`, `between`, or `justify`. `left` and `right` are aliases for `start` and `end`, not physical directions in RTL. Like Blade, `end` and `right` reverse the visual order of children without changing their DOM or keyboard order. `between` and `justify` distribute the children across the row.

You can also pass custom CSS classes as the alignment string. An empty string adds no alignment class; `null` uses `start`.

## Making actions full width

Set `fullWidth` (`full-width` in Vue or Blade) to use equal-width grid columns instead of wrapping flex items. This overrides alignment. The existing theme controls spacing and wrapping; there is no breakpoint or responsive alignment prop. Ensure your children fit the available width, especially in the full-width grid.

## Using actions in React, Vue, and Svelte

Import `Actions` from the Composer-installed Support package with your application's Vite build and the respective Vue or Svelte plugin. React 18 or 19, Vue 3.3.2 or later, and Svelte 5 are supported. Plain JavaScript and strict TypeScript consumers use the same API. These examples assume a file in `resources/js/`.

### Using React

```jsx
import React from 'react'
import Actions from '../../vendor/filament/support/resources/js/react/Actions'

export default function ProjectActions({ onSave }) {
    return (
        <Actions alignment="end">
            <button type="button" onClick={onSave}>Save changes</button>
            <a href="/projects">Cancel</a>
        </Actions>
    )
}
```

### Using Vue

```vue
<script setup>
import Actions from '../../vendor/filament/support/resources/js/vue/Actions.vue'
const emit = defineEmits(['save'])
</script>

<template>
    <Actions alignment="end">
        <button type="button" @click="emit('save')">Save changes</button>
        <a href="/projects">Cancel</a>
    </Actions>
</template>
```

### Using Svelte

```svelte
<script>
    import Actions from '../../vendor/filament/support/resources/js/svelte/Actions.svelte'
    let { onSave } = $props()
</script>

<Actions alignment="end">
    <button type="button" onclick={onSave}>Save changes</button>
    <a href="/projects">Cancel</a>
</Actions>
```

### Owning content and behavior

React children, Vue's default slot, and Svelte's children snippet supply the content. The adapters always render a stable `<div>`; unlike Blade's server-side empty-content detection, they do not inspect rendered children or filter PHP Actions. Conditionally render the container yourself when no visible actions remain. Removing it unmounts its children. Updating only `alignment`, `fullWidth`, or native attributes preserves child state and focus.

Native attributes and events target the root div. Use `className` and `onClick` in React, `class` and `@click` in Vue, or a string `class` and `onclick` in Svelte. React's `ref` targets the div; Vue's component ref exposes `$el`; Svelte supports `bind:element`.

The layout does not style your buttons, add roles, intercept clicks or form submissions, resolve PHP actions, or implement loading, confirmation, authorization, or visibility logic. Supply native buttons, links, or your own action components. Their behavior and server calls remain host-owned.

The adapters reuse `.fi-ac` CSS from Filament's Actions package, already included in panel themes. Outside panels, include the existing Actions stylesheet in your Filament theme. No adapter-specific CSS, Alpine, or Livewire is required. When embedding in Livewire, mount inside `wire:ignore` and unmount through your renderer's lifecycle.
