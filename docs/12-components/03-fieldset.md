---
title: Fieldset Blade component
---

import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

You can use a fieldset to group multiple form fields together, optionally with a label:

```blade
<x-filament::fieldset>
    <x-slot name="label">
        Address
    </x-slot>

    {{-- Form fields --}}
</x-filament::fieldset>
```

<AutoScreenshot name="components/fieldset/simple" alt="A fieldset with form fields" version="4.x" />

## Using fieldsets in JavaScript components

You can use fieldsets in React 18 or 19, Vue 3.3.2 or later, and Svelte 5. Import the component from your Composer-installed Support package using your application's Vite build. Vue and Svelte require their respective Vite plugins. JavaScript applications can import these TypeScript components without converting their own code to TypeScript.

These components use the same markup and existing Filament theme CSS as Blade. They do not require Alpine or Livewire. The examples assume your component is in `resources/js/`; adjust the import path for other directories.

### Using React

```jsx
import React from 'react'
import Fieldset from '../../vendor/filament/support/resources/js/react/Fieldset'

export default function AddressFields() {
    return (
        <Fieldset label="Address">
            <label>
                Street
                <input name="street" />
            </label>
        </Fieldset>
    )
}
```

### Using Vue

```vue
<script setup>
import Fieldset from '../../vendor/filament/support/resources/js/vue/Fieldset.vue'
</script>

<template>
    <Fieldset label="Address">
        <label>
            Street
            <input name="street" />
        </label>
    </Fieldset>
</template>
```

### Using Svelte

```svelte
<script>
    import Fieldset from '../../vendor/filament/support/resources/js/svelte/Fieldset.svelte'
</script>

<Fieldset label="Address">
    <label>
        Street
        <input name="street" />
    </label>
</Fieldset>
```

### Configuring fieldsets

All three components accept these props:

| Prop | Default | Purpose |
| --- | --- | --- |
| `contained` | `true` | Displays the border and padding. Set to `false` for an uncontained group. |
| `label` | `null` | Supplies the optional legend. Empty or whitespace-only text omits the legend. |
| `labelHidden` | `false` | Visually hides the legend while keeping it accessible to assistive technology. Use `label-hidden` in Vue templates. |
| `required` | `false` | Adds an asterisk to the legend. This is only a visual indicator; set `required` on individual form controls for native validation. |

Pass form controls as React children, a Vue default slot, or Svelte children. Text labels are escaped. For a rich legend, pass a React node to `label`, a Vue `label` slot, or a Svelte `label` snippet. A supplied slot or snippet takes precedence over a text label and should contain meaningful content.

Native attributes such as `disabled`, `name`, `form`, `id`, and `aria-*`, and native events, are forwarded to the `<fieldset>`. Event handlers use `onInput` in React, `@input` in Vue, and `oninput` in Svelte. React also forwards `ref` to the native `HTMLFieldSetElement`. Disabling the fieldset uses native browser behavior to disable its descendant controls; it does not change their values.

Use `className` in React and `class` in Vue or Svelte to add classes alongside the Filament theme hooks. Svelte classes accept space-separated strings. Changing props updates the legend and native attributes without replacing the child controls.

Keep framework-mounted content inside the host's `wire:ignore` boundary when embedding it in a Livewire component, and unmount it through the host renderer's lifecycle.

### Rendering rich legends

In React, pass a node to `label`:

```jsx
<Fieldset label={<strong>Delivery preferences</strong>}>
    {/* Form controls */}
</Fieldset>
```

In Vue, use the named `label` slot:

```vue
<Fieldset>
    <template #label><strong>Delivery preferences</strong></template>
    <!-- Form controls -->
</Fieldset>
```

In Svelte, use a `label` snippet:

```svelte
<Fieldset>
    {#snippet label()}<strong>Delivery preferences</strong>{/snippet}
    <!-- Form controls -->
</Fieldset>
```
