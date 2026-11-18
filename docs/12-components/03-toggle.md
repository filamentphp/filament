---
title: Toggle Blade component
---

## Introduction

You can use the toggle component to render a button with switch semantics. Provide an accessible name using `aria-label` or `aria-labelledby`:

```blade
<x-filament::toggle
    :state="0"
    aria-label="Enable notifications"
/>
```

Pass the initial `state` as `0` (off) or `1` (on), for example `:state="(int) $isEnabled"`. The component inserts this value directly into an Alpine.js expression, so passing the PHP boolean `false` produces an invalid expression.

The toggle manages its state locally in Alpine.js and updates `aria-checked` when clicked. It does not submit a native form value or provide `wire:model` / `x-model` binding. For a toggle bound to form state, use the [Toggle form field](../forms/toggle) instead.

## Changing the colors of the toggle

The toggle uses `primary` when on and `gray` when off. You can change these using the `on-color` and `off-color` attributes:

```blade
<x-filament::toggle
    :state="1"
    on-color="success"
    off-color="danger"
    aria-label="Enable notifications"
/>
```

## Adding icons to the toggle

You can use the `on-icon` and `off-icon` attributes to display [icons](../styling/icons) for each state:

```blade
<x-filament::toggle
    :state="0"
    on-icon="heroicon-m-check"
    off-icon="heroicon-m-x-mark"
    aria-label="Enable notifications"
/>
```

## Disabling the toggle

You can use the native `disabled` attribute to prevent interaction:

```blade
<x-filament::toggle
    :state="1"
    disabled
    aria-label="Enable notifications"
/>
```
