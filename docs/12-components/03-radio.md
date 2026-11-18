---
title: Radio Blade component
---

## Introduction

You can use the radio component to render a native radio input. Give related inputs the same `name` and a different `value`, and provide a label for each option:

```blade
<fieldset>
    <legend>Status</legend>

    <label>
        <x-filament::input.radio name="status" value="draft" wire:model="status" />

        <span>Draft</span>
    </label>

    <label>
        <x-filament::input.radio name="status" value="published" wire:model="status" />

        <span>Published</span>
    </label>
</fieldset>
```

## Triggering the error state of the radio

You can pass the `valid` attribute to control the error styling using Blade:

```blade
<x-filament::input.radio
    name="status"
    value="draft"
    wire:model="status"
    :valid="! $errors->has('status')"
/>
```

The `valid` attribute defaults to `true`. It only controls styling; you must validate the selected value in your application. This component does not have an `alpine-valid` prop.

## Disabling a radio

You can pass native input attributes, such as `disabled`, to the component:

```blade
<x-filament::input.radio name="status" value="published" disabled />
```
