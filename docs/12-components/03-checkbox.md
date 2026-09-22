---
title: Checkbox Blade component
---

import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

You can use the checkbox component to render a checkbox input that can be used to toggle a boolean value:

```blade
<label>
    <x-filament::input.checkbox wire:model="isAdmin" />

    <span>
        Is Admin
    </span>
</label>
```

<AutoScreenshot name="components/checkbox/simple" alt="Checkboxes with labels" version="4.x" />

## Triggering the error state of the checkbox

The checkbox has special styling that you can use if it is invalid. To trigger this styling, you can use either Blade or Alpine.js.

To trigger the error state using Blade, you can pass the `valid` attribute to the component, which contains either true or false based on if the checkbox is valid or not:

```blade
<x-filament::input.checkbox
    wire:model="isAdmin"
    :valid="! $errors->has('isAdmin')"
/>
```

Alternatively, you can use an Alpine.js expression to trigger the error state, based on if it evaluates to `true` or `false`:

```blade
<div x-data="{ errors: ['isAdmin'] }">
    <x-filament::input.checkbox
        x-model="isAdmin"
        alpine-valid="! errors.includes('isAdmin')"
    />
</div>
```

## Using JavaScript components

You can import this native checkbox primitive into React, Vue, or Svelte renderers. These source components use your application's existing framework build and Filament theme CSS; you do not need to convert your application to TypeScript. The examples assume your component is in `resources/js/`; adjust the import path for other directories.

```jsx
import React, { useState } from 'react'
import Checkbox from '../../vendor/filament/support/resources/js/react/Checkbox'

export default function Preferences() {
    const [subscribed, setSubscribed] = useState(false)

    return (
        <label>
            <Checkbox
                name="subscribed"
                value="yes"
                checked={subscribed}
                onChange={(event) => setSubscribed(event.currentTarget.checked)}
            />
            Receive updates
        </label>
    )
}
```

```vue
<script setup>
import { ref } from 'vue'
import Checkbox from '../../vendor/filament/support/resources/js/vue/Checkbox.vue'

const subscribed = ref(false)
</script>

<template>
    <label>
        <Checkbox v-model="subscribed" name="subscribed" value="yes" />
        Receive updates
    </label>
</template>
```

```svelte
<script>
    import Checkbox from '../../vendor/filament/support/resources/js/svelte/Checkbox.svelte'

    let subscribed = $state(false)
</script>

<label>
    <Checkbox bind:checked={subscribed} name="subscribed" value="yes" />
    Receive updates
</label>
```

Pass a reactive boolean `valid` prop (default `true`) to switch between `fi-valid` and `fi-invalid`. This only changes styling: set `aria-invalid` and associate error text with `aria-describedby` when appropriate. JavaScript components do not evaluate `alpine-valid` expressions or implement Forms validation or Livewire state.

Native attributes and events, including `name`, `value`, `form`, `disabled`, and `required`, pass through to the input. Add classes with `className` in React or `class` in Vue and Svelte. The type is always `checkbox`; children, slots, and raw HTML are unsupported. Provide an accessible label. Only checked, enabled inputs submit their `value` (or `on` when omitted); the component does not add a hidden unchecked value.

React supports both controlled `checked` / `onChange` and uncontrolled `defaultChecked`. Its ref points to the native `HTMLInputElement`, so you can focus it or set its `indeterminate` property. Vue supports a boolean `v-model`, not array / Set membership or custom true / false values. Without `v-model`, you can use native `checked` or `defaultChecked` attributes; `defaultChecked` also sets the browser's reset default. Vue forwards the native `indeterminate` DOM property. Svelte supports `bind:checked`, `bind:indeterminate`, and native `defaultChecked`.

An indeterminate checkbox is visually mixed, but its `checked` state still determines submission; clicking it clears the mixed state. Native form resets restore `defaultChecked`. For React controlled inputs or Vue `v-model`, reset your application state in the form's reset handler as well. Svelte's native checked binding follows form resets. `checked` and the reset default are distinct: set `defaultChecked` if a Svelte checkbox should reset to checked.
