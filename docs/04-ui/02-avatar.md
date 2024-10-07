---
title: Avatar Blade component
---

## Overview

The avatar component is used to render a circular or square image, often used to represent a user or entity as their "profile picture":

```blade
<x-filament::avatar
    src="https://filamentphp.com/dan.jpg"
    alt="Dan Harrin"
/>
```

## Setting the rounding of an avatar

Avatars are fully rounded by default, but you may make them square by setting the `circular` attribute to `false`:

```blade
<x-filament::avatar
    src="https://filamentphp.com/dan.jpg"
    alt="Dan Harrin"
    :circular="false"
/>
```

## Setting the size of an avatar

By default, the avatar will be "medium" size. You can set the size to either `sm`, `md`, or `lg` using the `size` attribute:

```blade
<x-filament::avatar
    src="https://filamentphp.com/dan.jpg"
    alt="Dan Harrin"
    size="lg"
/>
```

You can also pass your own custom size classes into the `size` attribute:

```blade
<x-filament::avatar
    src="https://filamentphp.com/dan.jpg"
    alt="Dan Harrin"
    size="w-12 h-12"
/>
