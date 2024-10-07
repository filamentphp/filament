---
title: Checkbox Blade component
---

## Overview

You can use the checkbox component to render a checkbox input that can be used to toggle a boolean value:

```blade
<label>
    <x-filament::input.checkbox wire:model="isAdmin" />

    <span>
        Is Admin
    </span>
</label>
```

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
