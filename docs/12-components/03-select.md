---
title: Select Blade component
---

import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

The select component is a wrapper around the native `<select>` element. It provides a simple interface for selecting a single value from a list of options:

```blade
<x-filament::input.wrapper>
    <x-filament::input.select wire:model="status">
        <option value="draft">Draft</option>
        <option value="reviewing">Reviewing</option>
        <option value="published">Published</option>
    </x-filament::input.select>
</x-filament::input.wrapper>
```

<AutoScreenshot name="components/select/simple" alt="A select input" version="4.x" />

To use the select component, you must wrap it in an "input wrapper" component, which provides a border and other elements such as a prefix or suffix. You can learn more about customizing the input wrapper component [here](input-wrapper).

## Using the select in JavaScript

You can import the typed source adapters from plain JavaScript or TypeScript using your framework's build plugin and the Filament theme. These render the native Support select, not the searchable Forms `Select`. Options, labels, validation messages, submission, and state belong to your renderer; there is no option registry, search, relationship saving, or Livewire binding.

Compose `Select` inside `InputWrapper`. Set `inlinePrefix` on both for an inline prefix. The Blade select has no inline suffix styling flag. Set `disabled` on the select for native behavior and on its wrapper for matching styling. Wrapper `valid` does not set native validity or `aria-invalid`.

### Using React

```jsx
import Select from '../../vendor/filament/support/resources/js/react/Select'
import InputWrapper from '../../vendor/filament/support/resources/js/react/InputWrapper'

<InputWrapper>
    <Select name="workshop" aria-label="Workshop" defaultValue="drawing">
        <option value="">Choose a workshop</option>
        <option value="drawing">Botanical drawing</option>
        <option value="ceramics">Studio ceramics</option>
    </Select>
</InputWrapper>
```

Native attributes, events, children, and `ref` are forwarded. Use `defaultValue` for uncontrolled state or `value` and `onChange` for controlled state. For `multiple`, use an array and read all values with `[...event.currentTarget.selectedOptions].map(option => option.value)`. DOM values are strings. Use `''` with an empty option for an empty single selection and `[]` for an empty multiple selection. Do not switch between controlled and uncontrolled modes. Native reset restores uncontrolled defaults; reset controlled state in your form's `onReset` handler.

### Using Vue

```vue
<script setup>
import { ref } from 'vue'
import Select from '../../vendor/filament/support/resources/js/vue/Select.vue'
import InputWrapper from '../../vendor/filament/support/resources/js/vue/InputWrapper.vue'
const workshops = ref(['drawing'])
</script>
<template>
    <InputWrapper>
        <Select v-model="workshops" multiple name="workshops" aria-label="Workshops">
            <option value="drawing">Botanical drawing</option>
            <option value="ceramics">Studio ceramics</option>
        </Select>
    </InputWrapper>
</template>
```

The default slot accepts native options and option groups. `v-model` uses Vue's native select directive, including array or `Set` multiple selections and non-string option values supplied with `:value`. Use typed option values instead of component model modifiers (`.number`, `.trim`, and `.lazy` are not supported). Native `@change` runs after the model assignment. A template ref exposes the select as `.element`.

For uncontrolled selection, omit `v-model` and set `selected` on the initial option(s). Do not add or remove `v-model` during the component's lifetime. Vue has no native select `defaultValue` API. Native reset restores selected attributes but does not update `v-model`; reset host state as well, or cancel reset and restore all fields explicitly.

### Using Svelte

```svelte
<script>
    import Select from '../../vendor/filament/support/resources/js/svelte/Select.svelte'
    import InputWrapper from '../../vendor/filament/support/resources/js/svelte/InputWrapper.svelte'
    let workshop = $state('drawing')
</script>
<InputWrapper>
    <Select bind:value={workshop} defaultValue="drawing" name="workshop" aria-label="Workshop">
        <option value="">Choose a workshop</option>
        <option value="drawing">Botanical drawing</option>
        <option value="ceramics">Studio ceramics</option>
    </Select>
</InputWrapper>
```

Use `bind:value`, `bind:element`, and a children snippet containing native options. Multiple selections use initialized arrays (for example, `$state([])`), not sets. Option values retain their Svelte types; they need not be strings. In the tested Svelte 5.57 version, `defaultValue` sets native reset defaults and uncancelled reset synchronizes the bound model. The adapter also seeds an initially undefined value from `defaultValue`, preserving the whole array for multiple selections. Omit `value` / `bind:value` for internally managed selection. Supply an explicit array `defaultValue` for internally managed multiple selects. Native events such as `onchange` are forwarded; the binding updates before the forwarded change callback. Svelte's native option matching and object identity rules apply.

### Updating options and submitting forms

Use stable option keys when changing lists. Removing the selected option does not clear host state automatically: Vue and Svelte display no selection for an unmatched single value, while React can display the first enabled option. Restoring a matching option restores the displayed selection. Keep the model in sync explicitly when your application removes choices.

Use an empty option and `required` for native single-select validation. `multiple`, `size`, `name`, `form`, and `disabled` keep their browser semantics; selects have no `readonly` behavior. `FormData` contains strings and repeated entries for multiple selection, so use `getAll()` or iterate entries rather than `Object.fromEntries()` to preserve all selected values. Disabled selects and disabled selected options are omitted from submission. A disabled selected option can still satisfy native `required` validation—validation and successful form controls are separate browser rules. The adapter does not normalize these differences.
