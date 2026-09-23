---
title: Empty State Blade component
---

import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

An empty state can be used to communicate that there is no content to display yet, and to guide the user towards the next action. A heading is required:

```blade
<x-filament::empty-state>
    <x-slot name="heading">
        No users yet
    </x-slot>
</x-filament::empty-state>
```

<AutoScreenshot name="components/empty-state/simple" alt="An empty state with icon, description and action button" version="4.x" />

## Adding a description to the empty state

You can add a description below the heading to the empty state by using the `description` slot:

```blade
<x-filament::empty-state>
    <x-slot name="heading">
        No users yet
    </x-slot>

    <x-slot name="description">
        Get started by creating a new user.
    </x-slot>
</x-filament::empty-state>
```

<AutoScreenshot name="components/empty-state/description" alt="An empty state with a description" version="4.x" />

## Adding an icon to the empty state

You can add an [icon](../styling/icons) to an empty state by using the `icon` attribute:

```blade
<x-filament::empty-state
    icon="heroicon-o-user"
>
    <x-slot name="heading">
        No users yet
    </x-slot>
</x-filament::empty-state>
```

### Changing the color of the empty state icon

By default, the color of the empty state icon is `primary`. You can change it to be `gray`, `danger`, `info`, `success` or `warning` by using the `icon-color` attribute:

```blade
<x-filament::empty-state
    icon="heroicon-o-user"
    icon-color="info"
>
    <x-slot name="heading">
        No users yet
    </x-slot>
</x-filament::empty-state>
```

<AutoScreenshot name="components/empty-state/icon-color" alt="An empty state with a colored icon" version="4.x" />

### Changing the size of the empty state icon

By default, the size of the empty state icon is "large". You can change it to be "small" or "medium" by using the `icon-size` attribute:

```blade
<x-filament::empty-state
    icon="heroicon-m-user"
    icon-size="sm"
>
    <x-slot name="heading">
        No users yet
    </x-slot>
</x-filament::empty-state>

<x-filament::empty-state
    icon="heroicon-m-user"
    icon-size="md"
>
    <x-slot name="heading">
        No users yet
    </x-slot>
</x-filament::empty-state>
```

<AutoScreenshot name="components/empty-state/icon-sizes" alt="Empty states with different icon sizes" version="4.x" />

## Adding footer actions to the empty state

You can add actions below the description by using the `footer` slot. This is useful for placing buttons, like the [`<x-filament::button>`](button) component:

```blade
<x-filament::empty-state>
    <x-slot name="heading">
        No users yet
    </x-slot>

    <x-slot name="footer">
        <x-filament::button icon="heroicon-m-plus">
            Create user
        </x-filament::button>
    </x-slot>
</x-filament::empty-state>
```

<AutoScreenshot name="components/empty-state/actions" alt="An empty state with footer actions" version="4.x" />

## Removing the empty state container

By default, empty states have a background color, shadow and border. You can remove these styles and just render the content of the empty state without the container using the `:contained` attribute:

```blade
<x-filament::empty-state :contained="false">
    <x-slot name="heading">
        No users yet
    </x-slot>
</x-filament::empty-state>
```

<AutoScreenshot name="components/empty-state/not-contained" alt="An empty state without a container" version="4.x" />

## Using empty states in React, Vue, and Svelte

You can import `EmptyState` from your Composer-installed Support package in React 18 or 19, Vue 3.3.2 or later, and Svelte 5. Use your application's Vite build and the respective Vue or Svelte plugin. Plain JavaScript applications can import these components without adopting TypeScript. The examples assume your component is in `resources/js/`.

These components use the existing Filament theme and do not require Alpine or Livewire. Keep framework content within a `wire:ignore` boundary when embedding it in Livewire, and unmount it through your host renderer's lifecycle.

### Using React

```jsx
import React from 'react'
import EmptyState from '../../vendor/filament/support/resources/js/react/EmptyState'

export default function Projects({ onCreate }) {
    return (
        <EmptyState heading="No projects yet" description="Bring your next idea to life.">
            <button type="button" onClick={onCreate}>Create project</button>
        </EmptyState>
    )
}
```

### Using Vue

```vue
<script setup>
import EmptyState from '../../vendor/filament/support/resources/js/vue/EmptyState.vue'
const emit = defineEmits(['create'])
</script>

<template>
    <EmptyState heading="No projects yet" description="Bring your next idea to life.">
        <button type="button" @click="emit('create')">Create project</button>
    </EmptyState>
</template>
```

### Using Svelte

```svelte
<script>
    import EmptyState from '../../vendor/filament/support/resources/js/svelte/EmptyState.svelte'
    let { onCreate } = $props()
</script>

<EmptyState heading="No projects yet" description="Bring your next idea to life.">
    <button type="button" onclick={onCreate}>Create project</button>
</EmptyState>
```

### Configuring empty states

| Prop | Default | Purpose |
| --- | --- | --- |
| `heading` | — | Supplies the heading. Provide meaningful text or rich content. |
| `headingTag` | `h2` | Selects a heading element from `h1` through `h6`. |
| `description` | — | Supplies optional descriptive text or rich inline content. |
| `footer` | — | Supplies optional actions or footer content. Children are the shorthand for this region. |
| `compact` | `false` | Places the icon alongside the text with reduced padding. |
| `contained` | `true` | Displays the background, border, and shadow. |
| `iconColor` | `primary` | Uses a registered Filament theme color, or `gray`. |
| `iconSize` | `lg` | Accepts `xs`, `sm`, `md`, `lg`, `xl`, or `2xl`. |

Use kebab-case prop names such as `heading-tag` in Vue templates. Whitespace-only text descriptions and footers omit their wrappers; `"0"` is meaningful text. Text is escaped. In React, `heading`, `description`, `footer`, and `icon` accept nodes. In Vue, use named slots for rich content, including an `icon` slot. In Svelte, use named snippets. Supplied slots/snippets are intentional content: omit them to remove their wrappers instead of supplying an empty function. The description renders inside a paragraph, so use inline content there.

For example, replace a text heading with `<strong>No projects yet</strong>` using `heading={<strong>No projects yet</strong>}` in React, `<template #heading><strong>No projects yet</strong></template>` in Vue, or `{#snippet heading()}<strong>No projects yet</strong>{/snippet}` in Svelte.

Pass an SVG component to the `icon` region; the component uses Support's `Icon` wrapper to apply size and color. Mark decorative SVGs `aria-hidden="true"`. PHP icon aliases and PHP Actions remain host-owned: resolve icons in your application and pass actual links or buttons with your own handlers. No raw HTML is needed. In React and Svelte, a non-null `footer` value takes precedence over children. In Vue, slots take precedence over text props, and the named `footer` slot takes precedence over the default slot.

Native attributes and events are forwarded to the root `<div>`. Use `className` and `onClick` in React, `class` and `@click` in Vue, or a string `class` and `onclick` in Svelte. React's `ref` points to the div; Vue component refs expose it through `$el`; Svelte supports `bind:element`. Updating heading, icon, layout, or native attributes preserves mounted footer controls and their state. Removing a footer unmounts its contents.
