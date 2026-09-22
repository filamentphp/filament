---
title: Radio component
---

## Introduction

You can use the radio input to select one value from a named group. Give each option a label and a distinct value:

```blade
<fieldset>
    <legend>Delivery</legend>
    <label><x-filament::input.radio name="delivery" value="standard" checked /> Standard</label>
    <label><x-filament::input.radio name="delivery" value="express" /> Express</label>
</fieldset>
```

Pass `:valid="false"` to show the error styling. This adds `fi-invalid` to `fi-radio-input`; valid radios do not have a `fi-valid` class. Styling does not validate the input: set `aria-invalid` and associate error text with `aria-describedby` as appropriate.

## Using JavaScript components

You can import the native radio primitive into React, Vue, or Svelte renderers using your existing framework build and Filament theme CSS. You do not need to convert your application to TypeScript. These examples live in `resources/js/`; adjust the import path for other directories.

```jsx
import React, { useState } from 'react'
import Radio from '../../vendor/filament/support/resources/js/react/Radio'

export default function Delivery() {
    const [delivery, setDelivery] = useState('standard')

    return (
        <form onReset={() => setDelivery('standard')}>
            <fieldset>
                <legend>Delivery</legend>
                {['standard', 'express'].map(value => (
                    <label key={value}>
                        <Radio name="delivery" value={value} checked={delivery === value}
                            onChange={(event) => setDelivery(event.currentTarget.value)} />
                        {value}
                    </label>
                ))}
            </fieldset>
            <button type="reset">Reset</button>
        </form>
    )
}
```

```vue
<script setup>
import { ref } from 'vue'
import Radio from '../../vendor/filament/support/resources/js/vue/Radio.vue'

const delivery = ref('standard')
</script>

<template>
    <form @reset="delivery = 'standard'">
        <fieldset>
            <legend>Delivery</legend>
            <label v-for="value in ['standard', 'express']" :key="value">
                <Radio v-model="delivery" name="delivery" :value="value" />
                {{ value }}
            </label>
        </fieldset>
        <button type="reset">Reset</button>
    </form>
</template>
```

```svelte
<script>
    import Radio from '../../vendor/filament/support/resources/js/svelte/Radio.svelte'

    let delivery = $state('standard')
</script>

<form onreset={() => delivery = 'standard'}>
    <fieldset>
        <legend>Delivery</legend>
        {#each ['standard', 'express'] as value (value)}
            <label>
                <Radio bind:group={delivery} name="delivery" {value} />
                {value}
            </label>
        {/each}
    </fieldset>
    <button type="reset">Reset</button>
</form>
```

Pass a reactive boolean `valid` prop (default `true`) for error styling. Native attributes and events, including `name`, `form`, `disabled`, and `required`, pass through. Add classes using `className` in React or `class` in Vue and Svelte. React forwards its ref to the native `HTMLInputElement`. The type is always `radio`; children, slots, and raw HTML are unsupported. These components do not emulate Forms validation or Livewire state.

### Binding the selected value

React supports native controlled `checked` / `onChange` and uncontrolled `defaultChecked`. Vue's `v-model` and Svelte's `bind:group` share a selected **string value**, not a boolean, array, or object. Use `null` for no selection. An omitted / `undefined` model leaves checked state under native attribute control. The value defaults to `on`.

Initialize a bound Svelte group to `null`, not `undefined`, if it should capture user selections. An `undefined` Svelte group does not update when an option is selected. Vue emits the selected value even when its previous model was `undefined`. Model updates happen before Vue `@change` and, when the group is defined, Svelte `onchange` callbacks, so those callbacks can read the new selected value synchronously.

Use the same model and native `name` for every option in a group, and distinct names and models for independent groups. The browser handles exclusivity and arrow-key navigation. Svelte's component `bind:group` shares the value through props; it does not register inputs globally or use a cross-component native `bind:group` directive.

### Submitting and resetting a form

Only the checked, enabled option contributes its string value to native `FormData`. Unchecked and disabled inputs contribute nothing. Use native `required` for browser validation; `valid` only changes presentation.

Without a model, use `defaultChecked` to set the initial selection and native reset default. For controlled React radios, Vue `v-model`, and Svelte `bind:group`, reset the shared model in the form's reset handler, as shown above. Do not combine a model with an independent `defaultChecked`: model-driven DOM updates do not provide a separate reset model. Resetting a form does not fire input change events.
