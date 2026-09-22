---
title: Avatar Blade component
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

The avatar component is used to render a circular or square image, often used to represent a user or entity as their "profile picture":

```blade
<x-filament::avatar
    src="https://filamentphp.com/dan.jpg"
    alt="Dan Harrin"
/>
```

<AutoScreenshot name="components/avatar/simple" alt="Avatars" version="4.x" />

## Setting the rounding of an avatar

Avatars are fully rounded by default, but you may make them square by setting the `circular` attribute to `false`:

```blade
<x-filament::avatar
    src="https://filamentphp.com/dan.jpg"
    alt="Dan Harrin"
    :circular="false"
/>
```

<AutoScreenshot name="components/avatar/square" alt="Square avatars" version="4.x" />

## Setting the size of an avatar

By default, the avatar will be "medium" size. You can set the size to either `sm`, `md`, or `lg` using the `size` attribute:

```blade
<x-filament::avatar
    src="https://filamentphp.com/dan.jpg"
    alt="Dan Harrin"
    size="lg"
/>
```

<AutoScreenshot name="components/avatar/sizes" alt="Avatars in different sizes" version="4.x" />

You can also pass your own custom size classes into the `size` attribute:

```blade
<x-filament::avatar
    src="https://filamentphp.com/dan.jpg"
    alt="Dan Harrin"
    size="w-12 h-12"
/>
```

## Using avatars in JavaScript components

You can use the same avatar markup and theme in React, Vue, and Svelte 5 custom fields, schema components, and widgets. Import the component from your Composer-installed Filament Support package using your application's Vite build. The examples below assume your component is in `resources/js/`; adjust the relative import path if it is in a subdirectory.

React 18 or 19, Vue 3.3.2 or later, and Svelte 5 are supported. Vue and Svelte require their respective Vite compiler plugins. The JavaScript widget, field, and schema component generators configure these for you. The components work in both JavaScript and TypeScript applications: your build compiles their TypeScript source, even if your application uses JavaScript.

These components use the panel's existing stylesheet. You do not need to import another stylesheet or initialize Alpine for the avatar.

The examples use an image at `public/images/avatar.png`. Replace the `src` with your own image URL.

### Using React

```jsx
import React from 'react'
import Avatar from '../../vendor/filament/support/resources/js/react/Avatar'

export default function Profile() {
    return (
        <Avatar
            src="/images/avatar.png"
            alt="Dan Harrin"
            circular={false}
            size="lg"
        />
    )
}
```

### Using Vue

```vue
<script setup>
import Avatar from '../../vendor/filament/support/resources/js/vue/Avatar.vue'
</script>

<template>
    <Avatar
        src="/images/avatar.png"
        alt="Dan Harrin"
        :circular="false"
        size="lg"
    />
</template>
```

### Using Svelte

```svelte
<script>
    import Avatar from '../../vendor/filament/support/resources/js/svelte/Avatar.svelte'
</script>

<Avatar
    src="/images/avatar.png"
    alt="Dan Harrin"
    circular={false}
    size="lg"
/>
```

### Configuring the avatar

All three components default to `circular: true`, `size: 'md'`, and `alt: ''`, just like Blade. Set descriptive alternative text when the image conveys information; leave it empty when the image is decorative.

You can pass `sm`, `md`, `lg`, or custom CSS classes to `size`. Custom classes must be included in your theme. Native image attributes and event handlers are forwarded to the `<img>` element using your framework's conventions. Use `className` in React and `class` in Vue or Svelte to add classes alongside Filament's classes. Svelte's `class` prop accepts a space-separated string. Reactive changes to these props update the same image element.
