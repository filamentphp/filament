---
title: Input wrapper Blade component
---

import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

The input wrapper component should be used as a wrapper around the [input](input) or [select](select) components. It provides a border and other elements such as a prefix or suffix.

```blade
<x-filament::input.wrapper>
    <x-filament::input
        type="text"
        wire:model="name"
    />
</x-filament::input.wrapper>

<x-filament::input.wrapper>
    <x-filament::input.select wire:model="status">
        <option value="draft">Draft</option>
        <option value="reviewing">Reviewing</option>
        <option value="published">Published</option>
    </x-filament::input.select>
</x-filament::input.wrapper>
```

## Triggering the error state of the input

The component has special styling that you can use if it is invalid. To trigger this styling, you can use either Blade or Alpine.js.

To trigger the error state using Blade, you can pass the `valid` attribute to the component, which contains either true or false based on if the input is valid or not:

```blade
<x-filament::input.wrapper :valid="! $errors->has('name')">
    <x-filament::input
        type="text"
        wire:model="name"
    />
</x-filament::input.wrapper>
```

Alternatively, you can use an Alpine.js expression to trigger the error state, based on if it evaluates to `true` or `false`:

```blade
<div x-data="{ errors: ['name'] }">
    <x-filament::input.wrapper alpine-valid="! errors.includes('name')">
        <x-filament::input
            type="text"
            wire:model="name"
        />
    </x-filament::input.wrapper>
</div>
```

## Disabling the input

To disable the input, you must also pass the `disabled` attribute to the wrapper component:

```blade
<x-filament::input.wrapper disabled>
    <x-filament::input
        type="text"
        wire:model="name"
        disabled
    />
</x-filament::input.wrapper>
```

<AutoScreenshot name="components/input/disabled" alt="A disabled input" version="4.x" />

## Adding affix text aside the input

You may place text before and after the input using the `prefix` and `suffix` slots:

```blade
<x-filament::input.wrapper>
    <x-slot name="prefix">
        https://
    </x-slot>

    <x-filament::input
        type="text"
        wire:model="domain"
    />

    <x-slot name="suffix">
        .com
    </x-slot>
</x-filament::input.wrapper>
```

<AutoScreenshot name="components/input/prefix" alt="An input with a prefix" version="4.x" />

### Using icons as affixes

You may place an [icon](../styling/icons) before and after the input using the `prefix-icon` and `suffix-icon` attributes:

```blade
<x-filament::input.wrapper suffix-icon="heroicon-m-globe-alt">
    <x-filament::input
        type="url"
        wire:model="domain"
    />
</x-filament::input.wrapper>
```

<AutoScreenshot name="components/input/icon" alt="An input with a prefix icon" version="4.x" />

#### Setting the affix icon's color

Affix icons are gray by default, but you may set a different color using the `prefix-icon-color` and `affix-icon-color` attributes:

```blade
<x-filament::input.wrapper
    suffix-icon="heroicon-m-check-circle"
    suffix-icon-color="success"
>
    <x-filament::input
        type="url"
        wire:model="domain"
    />
</x-filament::input.wrapper>
```

<AutoScreenshot name="components/input/suffix-icon-color" alt="An input with a colored suffix icon" version="4.x" />

## Using the wrapper in JavaScript

You can import `InputWrapper` directly into a React, Vue, or Svelte renderer. These typed components also work in plain JavaScript projects. Load your Filament theme as usual; the adapters use the same `fi-input-wrp` markup and CSS hooks as Blade. These examples assume your file is in `resources/js/`; adjust the vendor path for other directories.

```jsx
import InputWrapper from '../../vendor/filament/support/resources/js/react/InputWrapper'
import Icon from '../../vendor/filament/support/resources/js/react/Icon'

<InputWrapper
    prefix="£"
    suffix="GBP"
    inlinePrefix
    disabled={disabled}
    valid={valid}
    suffixIcon={<Icon src="/icons/currency.svg" />}
>
    <input className="fi-input" aria-label="Price" type="number" required disabled={disabled} aria-invalid={!valid} />
</InputWrapper>
```

```vue
<script setup>
import InputWrapper from '../../vendor/filament/support/resources/js/vue/InputWrapper.vue'
import Icon from '../../vendor/filament/support/resources/js/vue/Icon.vue'
</script>

<template>
    <InputWrapper prefix="£" suffix="GBP" inline-prefix>
        <template #suffixIcon><Icon src="/icons/currency.svg" /></template>
        <input class="fi-input" aria-label="Price" type="number" required />
    </InputWrapper>
</template>
```

```svelte
<script>
    import InputWrapper from '../../vendor/filament/support/resources/js/svelte/InputWrapper.svelte'
    import Icon from '../../vendor/filament/support/resources/js/svelte/Icon.svelte'
</script>

<InputWrapper prefix="£" suffix="GBP" inlinePrefix>
    {#snippet suffixIcon()}<Icon src="/icons/currency.svg" />{/snippet}
    <input class="fi-input" aria-label="Price" type="number" required />
</InputWrapper>
```

### Composing affix content

All adapters support `disabled` (default `false`), `valid` (default `true`), `inlinePrefix`, and `inlineSuffix` (both default `false`). React accepts nodes for `prefix`, `suffix`, `prefixIcon`, `suffixIcon`, `prefixActions`, and `suffixActions`. Vue accepts text props for `prefix` and `suffix`, or named slots with those six names. Svelte accepts text or snippets for `prefix` and `suffix`, and snippets for icons and actions. Use the default slot, React children, or Svelte children for your native input or other control.

Text affixes are escaped and whitespace-only strings do not create labels. Rich affixes are wrapped in `fi-input-wrp-label`. Icon slots render directly: supply the [Icon](../styling/icons#using-javascript-components) adapter to retain the icon theme hooks, or a `LoadingIndicator` while your host is busy. Use action slots for your own buttons; the prefix order is actions, icon, label, and the suffix order is label, icon, actions. Remove a conditional slot or snippet itself when an affix should disappear; a supplied slot that renders nothing still reserves its container.

Native attributes and events apply to the outer `div`. React forwards `ref`, Vue exposes its root through the component ref's `$el`, and Svelte supports `bind:element`. Unlike Blade, a supplied native `tabindex` is preserved. No wrapper click automatically focuses the input; wire an explicit handler or label in your host if needed.

### Owning input semantics and server behavior

`disabled` and `valid` only change the wrapper's appearance. You must separately supply the input's `disabled`, `required`, value bindings, `aria-invalid`, label, and error description. The wrapper does not mutate descendant inputs, cancel their events, or change native validation. The examples use a native input, not a JavaScript Input component.

PHP `Action` objects, visibility checks, icon names and aliases, and Livewire `wire:target` loading and delay behavior remain host-owned. There are no browser equivalents of `prefixIconAlias`, `suffixIconAlias`, `alpineDisabled`, or `alpineValid`; pass reactive booleans and rendered content instead. Icon colors belong to the supplied icon's classes or styles. If you compose a loading indicator, you also own its conditional rendering and accessible status announcement; adding a `wire:target` attribute alone does not start loading behavior in these adapters.
