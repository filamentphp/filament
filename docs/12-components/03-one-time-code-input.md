---
title: One-time code input Blade component
---

## Introduction

You can use the one-time code input component to enter a numeric code. Bind the combined value using Alpine.js and `x-model`:

```blade
<div x-data="{ code: null }">
    <label for="verification-code">Verification code</label>

    <x-filament::input.one-time-code
        x-model="code"
        aria-label="Verification code"
    >
        <x-slot name="input" id="verification-code"></x-slot>
    </x-filament::input.one-time-code>
</div>
```

The component renders six inputs by default. It moves focus as you type and distributes pasted or autofilled digits across the inputs. The combined value is a string, preserving leading zeroes, or `null` when empty.

The outer attributes, including `x-model` and the accessible group label, are applied to a wrapping `<div>`. The `input` slot's attributes are applied to the first input, so you can associate it with a visible label. Subsequent inputs receive translated positional labels automatically. The slot's content is not rendered.

The component does not generate, send, or verify codes. You must implement those operations and validate the code on the server. It does not submit the combined value as a native form input; submit the bound state through your application, or add a hidden input bound to `code` if using a native form.

## Changing the number of digits

You can use the `length` attribute to change the number of inputs. Pass a positive integer:

```blade
<x-filament::input.one-time-code
    :length="4"
    x-model="code"
    aria-label="Verification code"
>
    <x-slot name="input" aria-label="First digit"></x-slot>
</x-filament::input.one-time-code>
```

## Disabling or making the input read-only

Pass `disabled` or `readonly` on the `input` slot, not the outer component. These attributes are also applied to the remaining digit inputs:

```blade
<x-filament::input.one-time-code
    x-model="code"
    aria-label="Verification code"
>
    <x-slot name="input" aria-label="First digit" disabled></x-slot>
</x-filament::input.one-time-code>
```
