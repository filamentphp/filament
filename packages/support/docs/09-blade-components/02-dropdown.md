---
title: Dropdown Blade component
---

## Overview

The dropdown component allows you to render a dropdown menu with a button that triggers it:

```blade
<x-filament::dropdown>
    <x-slot name="trigger">
        <x-filament::button>
            More actions
        </x-filament::button>
    </x-slot>
    
    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item wire:click="openViewModal">
            View
        </x-filament::dropdown.list.item>
        
        <x-filament::dropdown.list.item wire:click="openEditModal">
            Edit
        </x-filament::dropdown.list.item>
        
        <x-filament::dropdown.list.item wire:click="openDeleteModal">
            Delete
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>
```

## Using a dropdown item as an anchor link

By default, a dropdown item's underlying HTML tag is `<button>`. You can change it to be an `<a>` tag by using the `tag` attribute:

```blade
<x-filament::dropdown.list.item
    href="https://filamentphp.com"
    tag="a"
>
    Filament
</x-filament::dropdown.list.item>
```

## Changing the color of a dropdown item

By default, the color of a dropdown item is "gray". You can change it to be `danger`, `info`, `primary`, `success` or `warning` by using the `color` attribute:

```blade
<x-filament::dropdown.list.item color="danger">
    Edit
</x-filament::button>

<x-filament::dropdown.list.item color="info">
    Edit
</x-filament::button>

<x-filament::dropdown.list.item color="primary">
    Edit
</x-filament::button>

<x-filament::dropdown.list.item color="success">
    Edit
</x-filament::button>

<x-filament::dropdown.list.item color="warning">
    Edit
</x-filament::button>
```

## Adding an icon to a dropdown item

You can add an [icon](https://blade-ui-kit.com/blade-icons?set=1#search) to a dropdown item by using the `icon` attribute:

```blade
<x-filament::dropdown.list.item icon="heroicon-m-pencil">
    Edit
</x-filament::dropdown.list.item>
```

## Adding an image to a dropdown item

You can add a circular image to a dropdown item by using the `image` attribute:

```blade
<x-filament::dropdown.list.item image="https://filamentphp.com/dan.jpg">
    Dan Harrin
</x-filament::dropdown.list.item>
```

## Adding a badge to a dropdown item

You can render a [badge](badge) on top of a dropdown item by using the `badge` slot:

```blade
<x-filament::dropdown.list.item>
    Mark notifications as read
    
    <x-slot name="badge">
        3
    </x-slot>
</x-filament::dropdown.list.item>
```

You can [change the color](badge#changing-the-color-of-the-badge) of the badge using the `badge-color` attribute:

```blade
<x-filament::dropdown.list.item badge-color="danger">
    Mark notifications as read
    
    <x-slot name="badge">
        3
    </x-slot>
</x-filament::dropdown.list.item>
```

## Setting the placement of a dropdown

The dropdown may be positioned relative to the trigger button by using the `placement` attribute:

```blade
<x-filament::dropdown placement="top-start">
    {{-- Dropdown items --}}
</x-filament::dropdown>
```

## Setting the width of a dropdown

The dropdown may be set to a width by using the `width` attribute. Options correspond to [Tailwind's max-width scale](https://tailwindcss.com/docs/max-width). The options are `xs`, `sm`, `md`, `lg`, `xl`, `2xl`, `3xl`, `4xl`, `5xl`, `6xl` and `7xl`:

```blade
<x-filament::dropdown width="xs">
    {{-- Dropdown items --}}
</x-filament::dropdown>
```

## Controlling the maximum height of a dropdown

The dropdown content can have a maximum height using the `max-height` attribute, so that it scrolls. You can pass a [CSS length](https://developer.mozilla.org/en-US/docs/Web/CSS/length):

```blade
<x-filament::dropdown max-height="400px">
    {{-- Dropdown items --}}
</x-filament::dropdown>
```
