---
title: Input Blade component
---

import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

The input component is a wrapper around the native `<input>` element. It provides a simple interface for entering a single line of text.

```blade
<x-filament::input.wrapper>
    <x-filament::input
        type="text"
        wire:model="name"
    />
</x-filament::input.wrapper>
```

<AutoScreenshot name="components/input/simple" alt="A simple input" version="4.x" />

To use the input component, you must wrap it in an "input wrapper" component, which provides a border and other elements such as a prefix or suffix. You can learn more about customizing the input wrapper component [here](input-wrapper).

## Using the input in JavaScript

You can import the typed source adapters into plain JavaScript or TypeScript renderers for custom fields, schema components, widgets, and pages. Use your framework's build plugin and load the Filament theme. These components render a native input, not a Forms `TextInput`: they do not provide Livewire binding, masking, relationship saving, PHP validation, or automatic error messages.

Compose `Input` inside `InputWrapper`. Set `inlinePrefix` and `inlineSuffix` on both components when using inline affixes. Set `disabled` on both for matching styling and semantics; wrapper `valid` does not set input `aria-invalid` or native validity. Labels, descriptions, errors, and state belong to your renderer.

### Using React

```jsx
import Input from '../../vendor/filament/support/resources/js/react/Input'
import InputWrapper from '../../vendor/filament/support/resources/js/react/InputWrapper'

<InputWrapper prefix="£" inlinePrefix>
    <Input aria-label="Price" name="price" type="number" min="0"
        defaultValue={0} inlinePrefix />
</InputWrapper>
```

Native attributes, events, and `ref` are forwarded. Use `defaultValue` for an uncontrolled initial value, or `value` with `onChange` for controlled state. `event.currentTarget.value` is a string even for numbers; read `valueAsNumber` explicitly if desired (it is `NaN` when empty). Keep controlled values defined, using `''` for empty. Native form reset restores uncontrolled defaults; reset controlled state in your form's `onReset` handler.

### Using Vue

```vue
<script setup>
import { ref } from 'vue'
import Input from '../../vendor/filament/support/resources/js/vue/Input.vue'
import InputWrapper from '../../vendor/filament/support/resources/js/vue/InputWrapper.vue'
const price = ref('0')
</script>
<template>
    <InputWrapper prefix="£" inline-prefix>
        <Input v-model="price" aria-label="Price" name="price" type="number"
            min="0" inline-prefix />
    </InputWrapper>
</template>
```

`v-model` accepts strings or numbers and emits the DOM string on `input`, including `''` when empty. Numeric input types alone do not enable coercion. Vue applies `.number` and `.trim` to the component's model events: `.number` converts parseable values to numbers but keeps empty or nonnumeric strings, and `.trim` removes surrounding whitespace. `.lazy` is not supported. Model updates also include intermediate IME composition input; this is not the composition buffering of Vue's native `v-model` directive. Native `@input`, `@change`, and other listeners still receive DOM events. A component template ref exposes the native input as `.element`. For uncontrolled inputs omit `v-model` / `value` and use `:defaultValue="initialValue"`. `modelValue` takes precedence over `value` when supplied. Native reset does not emit `input` or update your Vue model: reset host state too, or cancel reset and restore all fields explicitly.

### Using Svelte

```svelte
<script>
    import Input from '../../vendor/filament/support/resources/js/svelte/Input.svelte'
    import InputWrapper from '../../vendor/filament/support/resources/js/svelte/InputWrapper.svelte'
    let price = $state(0)
</script>
<InputWrapper prefix="£" inlinePrefix>
    <Input bind:value={price} aria-label="Price" name="price" type="number"
        min="0" defaultValue={0} inlinePrefix />
</InputWrapper>
```

Use `bind:value` and optionally `bind:element` for the native input. In the tested Svelte 5.57 versions, native value binding converts `number` and `range` inputs to numbers and converts an empty numeric value to `null`. Other input types produce strings. Include `null` in typed numeric models. `defaultValue` supplies the native reset value, and Svelte synchronizes bound state on uncancelled reset. Omit `value` / `bind:value` for an internally managed input initialized by `defaultValue`. Native events such as `oninput` are forwarded.

### Choosing native types and form behavior

Use text-like inputs (`text`, `email`, `number`, `password`, `search`, `tel`, `url`, date/time types), or native `color` / `range` controls. Native attributes and browser sanitization determine their values and validation; there is no cross-framework numeric or date conversion layer. Although native type attributes are forwarded, use `Checkbox` / `Radio` for checked or group bindings and native file/button controls for files and actions: `Input`'s value API is not a checked, files, or group API.

Supply valid explicit defaults for `color`, `range`, and date/time controls when you need predictable reset state. Browsers sanitize missing or invalid defaults; Svelte's reset binding can read the raw `defaultValue`, so host state may differ from the sanitized DOM value. The adapter does not normalize these differences. Changing `type` preserves the element, but does not promise immediate host-state conversion: subsequent input follows the new type's native framework binding.

`name`, `form`, `required`, `min`, `max`, `step`, `pattern`, `disabled`, and `readOnly` (React) / `readonly` (Vue and Svelte) retain native semantics. Disabled fields are excluded from `FormData`; read-only fields are included. Submission values are strings, including `'0'`; empty is not zero. Updating props does not replace the input node. The host owns external state synchronization, submission, reset policy, and validation messages.
