---
title: Callout Blade component
---

import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

A callout can be used to draw attention to important information or messages:

```blade
<x-filament::callout
    icon="heroicon-o-information-circle"
    color="info"
>
    <x-slot name="heading">
        Important Notice
    </x-slot>

    <x-slot name="description">
        Please read this information carefully before proceeding.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/simple" alt="An info callout" version="4.x" />

## Using status colors

You can set the `color` attribute to `danger`, `info`, `success`, or `warning` to create status callouts:

```blade
<x-filament::callout
    icon="heroicon-o-x-circle"
    color="danger"
>
    <x-slot name="heading">
        Error
    </x-slot>

    <x-slot name="description">
        Something went wrong. Please try again.
    </x-slot>
</x-filament::callout>

<x-filament::callout
    icon="heroicon-o-information-circle"
    color="info"
>
    <x-slot name="heading">
        Information
    </x-slot>

    <x-slot name="description">
        Here is some helpful information.
    </x-slot>
</x-filament::callout>

<x-filament::callout
    icon="heroicon-o-check-circle"
    color="success"
>
    <x-slot name="heading">
        Success
    </x-slot>

    <x-slot name="description">
        Your changes have been saved.
    </x-slot>
</x-filament::callout>

<x-filament::callout
    icon="heroicon-o-exclamation-circle"
    color="warning"
>
    <x-slot name="heading">
        Warning
    </x-slot>

    <x-slot name="description">
        Please review the following items.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/colors" alt="Callouts in different colors" version="4.x" />

## Adding an icon to the callout

You can add an [icon](../styling/icons) to a callout using the `icon` attribute:

```blade
<x-filament::callout icon="heroicon-o-sparkles">
    <x-slot name="heading">
        Tip
    </x-slot>

    <x-slot name="description">
        You can use custom icons for your callouts.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/custom-icon" alt="A callout with a custom icon" version="4.x" />

### Changing the color of the callout icon

By default, the icon color inherits from the callout's `color`. You can override it using the `icon-color` attribute:

```blade
<x-filament::callout
    icon="heroicon-o-shield-check"
    icon-color="success"
>
    <x-slot name="heading">
        Custom Icon Color
    </x-slot>

    <x-slot name="description">
        The icon color is independent of the background color.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/icon-color" alt="A callout with a custom icon color" version="4.x" />

### Changing the size of the callout icon

By default, the size of the callout icon is "large". You can change it to "small" or "medium" using the `icon-size` attribute:

```blade
<x-filament::callout
    icon="heroicon-m-information-circle"
    icon-size="sm"
    color="info"
>
    <x-slot name="heading">
        Small Icon
    </x-slot>

    <x-slot name="description">
        This callout has a smaller icon.
    </x-slot>
</x-filament::callout>

<x-filament::callout
    icon="heroicon-m-information-circle"
    icon-size="md"
    color="info"
>
    <x-slot name="heading">
        Medium Icon
    </x-slot>

    <x-slot name="description">
        This callout has a medium icon.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/icon-sizes" alt="Callouts with different icon sizes" version="4.x" />

## Using a custom background color

You can set a custom background color using the `color` attribute with any supported color:

```blade
<x-filament::callout
    icon="heroicon-o-star"
    color="primary"
>
    <x-slot name="heading">
        Announcement
    </x-slot>

    <x-slot name="description">
        A special announcement with a custom color.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/primary-color" alt="A callout with a primary color" version="4.x" />

## Adding content to the footer

You can add custom content to the callout footer using the `footer` slot:

```blade
<x-filament::callout
    icon="heroicon-o-check-circle"
    color="success"
>
    <x-slot name="heading">
        System Status
    </x-slot>

    <x-slot name="description">
        All systems are operational.
    </x-slot>

    <x-slot name="footer">
        <span class="text-sm text-gray-500">Last updated: January 15, 2025</span>
    </x-slot>
</x-filament::callout>
```

You can also include buttons or other interactive elements in the footer:

```blade
<x-filament::callout
    icon="heroicon-o-exclamation-circle"
    color="warning"
>
    <x-slot name="heading">
        Subscription Expiring
    </x-slot>

    <x-slot name="description">
        Your subscription will expire in 7 days.
    </x-slot>

    <x-slot name="footer">
        <x-filament::button size="sm">
            Renew Now
        </x-filament::button>
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/footer" alt="A callout with a footer action" version="4.x" />

## Adding content to the controls

You can add custom content to the callout controls (top-right corner) using the `controls` slot:

```blade
<x-filament::callout
    icon="heroicon-o-information-circle"
    color="info"
>
    <x-slot name="heading">
        Dismissible Callout
    </x-slot>

    <x-slot name="description">
        This callout can be dismissed using the control in the top-right corner.
    </x-slot>

    <x-slot name="controls">
        <x-filament::icon-button
            icon="heroicon-m-x-mark"
            color="gray"
            label="Dismiss"
        />
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/controls" alt="A callout with a dismiss control" version="4.x" />

## Callouts without an icon

Callouts can be rendered without an icon if needed:

```blade
<x-filament::callout>
    <x-slot name="heading">
        No Icon
    </x-slot>

    <x-slot name="description">
        This callout has no icon.
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/no-icon" alt="A callout without an icon" version="4.x" />

## Callouts with only a heading

Callouts can be used with just a heading, without a description:

```blade
<x-filament::callout
    icon="heroicon-o-information-circle"
    color="info"
>
    <x-slot name="heading">
        Simple Notice
    </x-slot>
</x-filament::callout>
```

<AutoScreenshot name="components/callout/heading-only" alt="A callout with only a heading" version="4.x" />

## Using callouts in React, Vue, and Svelte

You can import `Callout` from your Composer-installed Support package in React 18 or 19, Vue 3.3.2 or later, and Svelte 5. Use your application's Vite build with the respective Vue or Svelte plugin. Plain JavaScript consumers do not need to adopt TypeScript. These examples assume your component is in `resources/js/`.

### Using React

```jsx
import React from 'react'
import Callout from '../../vendor/filament/support/resources/js/react/Callout'

export default function Notice({ onReview }) {
    return (
        <Callout color="info" heading="Your next release" description="Review the changes before publishing.">
            <button type="button" onClick={onReview}>Review changes</button>
        </Callout>
    )
}
```

### Using Vue

```vue
<script setup>
import Callout from '../../vendor/filament/support/resources/js/vue/Callout.vue'
const emit = defineEmits(['review'])
</script>

<template>
    <Callout color="info" heading="Your next release" description="Review the changes before publishing.">
        <button type="button" @click="emit('review')">Review changes</button>
    </Callout>
</template>
```

### Using Svelte

```svelte
<script>
    import Callout from '../../vendor/filament/support/resources/js/svelte/Callout.svelte'
    let { onReview } = $props()
</script>

<Callout color="info" heading="Your next release" description="Review the changes before publishing.">
    <button type="button" onclick={onReview}>Review changes</button>
</Callout>
```

### Configuring callouts

| Prop | Default | Purpose |
| --- | --- | --- |
| `color` | `gray` | Uses `gray`, a semantic color such as `info`, or a registered Filament color name. |
| `heading` | — | Supplies text or rich content inside an `h4`, matching Blade. |
| `description` | — | Supplies text or rich inline content inside a paragraph. |
| `footer` | — | Supplies actions or footer content. Children are a shorthand for this region. |
| `controls` | — | Supplies content in the top-right controls region. |
| `iconColor` | Same as `color` | Overrides the decorative icon's color independently. |
| `iconSize` | `lg` | Accepts `xs`, `sm`, `md`, `lg`, `xl`, or `2xl`. |

In React, `heading`, `description`, `footer`, `controls`, and `icon` accept nodes. In Vue, use named slots for rich content, including the `icon` slot. In Svelte, use named snippets. For example, use `heading={<strong>Your next release</strong>}` in React, `<template #heading><strong>Your next release</strong></template>` in Vue, or `{#snippet heading()}<strong>Your next release</strong>{/snippet}` in Svelte. Use kebab-case props such as `icon-color` in Vue templates.

Whitespace-only text omits its region; `"0"` is meaningful. Supplied slots and snippets represent intentional content, so omit them to remove their region. In React and Svelte, a non-null `footer` takes precedence over children. In Vue, slots take precedence over text props, and the `footer` slot takes precedence over the default slot. Text is escaped; no raw HTML is required.

Like Blade, callouts have no icon by default. Supply actual SVG artwork or a framework icon component to the `icon` region. Support's `Icon` wrapper applies size, inherited color, and `aria-hidden="true"`. Browser components do not resolve PHP icon aliases. Named colors use your existing Filament theme, including custom colors registered by the host; PHP palette arrays are not accepted as props.

Native attributes and events target the root `<div>`. Use `className` and `onClick` in React, `class` and `@click` in Vue, or a string `class` and `onclick` in Svelte. React's `ref` targets that div; Vue refs expose it through `$el`; Svelte supports `bind:element`. Content and color updates preserve mounted children. Removing a region unmounts its children.

No announcement role or dismissal behavior is added automatically. Choose `role="status"` or `role="alert"` only when the message warrants live announcements. To dismiss a callout, supply your own button in `controls` and remove the component in its handler. PHP Actions, Livewire calls, and state remain host-owned.

These components use the existing Filament CSS without Alpine or Livewire. When embedding them in Livewire, mount within a `wire:ignore` boundary and unmount through your host renderer's lifecycle.
