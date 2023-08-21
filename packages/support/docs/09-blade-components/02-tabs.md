---
title: Tabs Blade component
---

## Overview

The tabs component allows you to render a set of tabs, which can be used to toggle between multiple sections of content:

```blade
<x-filament::tabs label="Content tabs">
    <x-filament::tabs.item>
        Tab 1
    </x-filament::tabs.item>

    <x-filament::tabs.item>
        Tab 2
    </x-filament::tabs.item>

    <x-filament::tabs.item>
        Tab 2
    </x-filament::tabs.item>
</x-filament::tabs>
```

## Triggering the active state of the tab

By default, tabs do not appear "active". To make a tab appear active, you can use the `active` attribute:

```blade
<x-filament::tabs>
    <x-filament::tabs.item active>
        Tab 1
    </x-filament::tabs.item>

    {{-- Other tabs --}}
</x-filament::tabs>
```

You can also use the `active` attribute to make a tab appear active conditionally:

```blade
<x-filament::tabs>
    <x-filament::tabs.item
        :active="$activeTab === 'tab1'"
        wire:click="$set('activeTab', 'tab1')"
    >
        Tab 1
    </x-filament::tabs.item>

    {{-- Other tabs --}}
</x-filament::tabs>
```

Or you can use the `alpine-active` attribute to make a tab appear active conditionally using Alpine.js:

```blade
<x-filament::tabs x-data="{ activeTab: 'tab1' }">
    <x-filament::tabs.item
        alpine-active="activeTab === 'tab1'"
        x-on:click="activeTab = 'tab1'"
    >
        Tab 1
    </x-filament::tabs.item>

    {{-- Other tabs --}}
</x-filament::tabs>
```

## Setting a tab icon

Tabs may have an [icon](https://blade-ui-kit.com/blade-icons?set=1#search), which you can set using the `icon` attribute:

```blade
<x-filament::tabs>
    <x-filament::tabs.item icon="heroicon-m-bell">
        Notifications
    </x-filament::tabs.item>

    {{-- Other tabs --}}
</x-filament::tabs>
```

### Setting the tab icon position

The icon of the tab may be positioned before or after the label using the `icon-position` attribute:

```blade
<x-filament::tabs>
    <x-filament::tabs.item
        icon="heroicon-m-bell"
        icon-position="after"
    >
        Notifications
    </x-filament::tabs.item>

    {{-- Other tabs --}}
</x-filament::tabs>
```

## Setting a tab badge

Tabs may have a [badge](badge), which you can set using the `badge` slot:

```blade
<x-filament::tabs>
    <x-filament::tabs.item>
        Notifications

        <x-slot name="badge">
            5
        </x-slot>
    </x-filament::tabs.item>

    {{-- Other tabs --}}
</x-filament::tabs>
```

## Using a tab as an anchor link

By default, a tab's underlying HTML tag is `<button>`. You can change it to be an `<a>` tag by using the `tag` attribute:

```blade
<x-filament::tabs>
    <x-filament::tabs.item
        :href="route('notifications')"
        tag="a"
    >
        Notifications
    </x-filament::tabs.item>

    {{-- Other tabs --}}
</x-filament::tabs>
```
