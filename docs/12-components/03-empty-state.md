---
title: Empty State Blade component
---

## Introduction

An empty state can be used to communicate that there is no content to display yet, and to guide the user towards the next action. A heading is required:

```blade
<x-filament::empty-state>
    <x-slot name="heading">
        No users yet
    </x-slot>
</x-filament::empty-state>
```

## Adding a description to the empty state

You can add a description below the heading to the empty state by using the `description` slot:

```blade
<x-filament::empty-state>
    <x-slot name="heading">
        No users yet
    </x-slot>

    <x-slot name="description">
        Get started by creating a new user.
    </x-slot>
</x-filament::empty-state>
```

## Adding an icon to the empty state

You can add an [icon](../styling/icons) to an empty state by using the `icon` attribute:

```blade
<x-filament::empty-state
    icon="heroicon-o-user"
>
    <x-slot name="heading">
        No users yet
    </x-slot>
</x-filament::empty-state>
```

### Changing the color of the empty state icon

By default, the color of the empty state icon is `primary`. You can change it to be `gray`, `danger`, `info`, `success` or `warning` by using the `icon-color` attribute:

```blade
<x-filament::empty-state
    icon="heroicon-o-user"
    icon-color="info"
>
    <x-slot name="heading">
        No users yet
    </x-slot>
</x-filament::empty-state>
```

### Changing the size of the empty state icon

By default, the size of the empty state icon is "large". You can change it to be "small" or "medium" by using the `icon-size` attribute:

```blade
<x-filament::empty-state
    icon="heroicon-m-user"
    icon-size="sm"
>
    <x-slot name="heading">
        No users yet
    </x-slot>
</x-filament::empty-state>

<x-filament::empty-state
    icon="heroicon-m-user"
    icon-size="md"
>
    <x-slot name="heading">
        No users yet
    </x-slot>
</x-filament::empty-state>
```


## Adding footer actions to the empty state

You can add actions below the description by using the `footer` slot. This is useful for placing buttons, like the `[<x-filament::button>`](button) component:

```blade
<x-filament::empty-state>
    <x-slot name="heading">
        No users yet
    </x-slot>
    
    <x-slot name="footer">
        <x-filament::button icon="heroicon-m-plus">
            Create user
        </x-filament::button>
    </x-slot>
</x-filament::empty-state>
```
