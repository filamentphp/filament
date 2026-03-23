---
title: Section Blade component
---

import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

A section can be used to group content together, with an optional heading:

```blade
<x-filament::section>
    <x-slot name="heading">
        User details
    </x-slot>

    {{-- Content --}}
</x-filament::section>
```

<AutoScreenshot name="components/section/simple" alt="A simple section" version="5.x" />

## Adding a description to the section

You can add a description below the heading to the section by using the `description` slot:

```blade
<x-filament::section>
    <x-slot name="heading">
        User details
    </x-slot>

    <x-slot name="description">
        This is all the information we hold about the user.
    </x-slot>

    {{-- Content --}}
</x-filament::section>
```

<AutoScreenshot name="components/section/description" alt="A section with a description and icon" version="5.x" />

## Adding an icon to the section header

You can add an [icon](../styling/icons) to a section by using the `icon` attribute:

```blade
<x-filament::section icon="heroicon-o-user">
    <x-slot name="heading">
        User details
    </x-slot>

    {{-- Content --}}
</x-filament::section>
```

<AutoScreenshot name="components/section/icon" alt="A section with an icon" version="5.x" />

### Changing the color of the section icon

By default, the color of the section icon is "gray". You can change it to be `danger`, `info`, `primary`, `success` or `warning` by using the `icon-color` attribute:

```blade
<x-filament::section
    icon="heroicon-o-user"
    icon-color="info"
>
    <x-slot name="heading">
        User details
    </x-slot>

    {{-- Content --}}
</x-filament::section>
```

<AutoScreenshot name="components/section/icon-color" alt="A section with a colored icon" version="5.x" />

### Changing the size of the section icon

By default, the size of the section icon is "large". You can change it to be "small" or "medium" by using the `icon-size` attribute:

```blade
<x-filament::section
    icon="heroicon-m-user"
    icon-size="sm"
>
    <x-slot name="heading">
        User details
    </x-slot>

    {{-- Content --}}
</x-filament::section>

<x-filament::section
    icon="heroicon-m-user"
    icon-size="md"
>
    <x-slot name="heading">
        User details
    </x-slot>

    {{-- Content --}}
</x-filament::section>
```

<AutoScreenshot name="components/section/icon-sizes" alt="Sections with different icon sizes" version="5.x" />

## Adding content to the end of the header

You may render additional content at the end of the header, next to the heading and description, using the `afterHeader` slot:

```blade
<x-filament::section>
    <x-slot name="heading">
        User details
    </x-slot>

    <x-slot name="afterHeader">
        {{-- Input to select the user's ID --}}
    </x-slot>

    {{-- Content --}}
</x-filament::section>
```

<AutoScreenshot name="components/section/after-header" alt="A section with content after the header" version="5.x" />

## Making a section collapsible

You can make the content of a section collapsible by using the `collapsible` attribute:

```blade
<x-filament::section collapsible>
    <x-slot name="heading">
        User details
    </x-slot>

    {{-- Content --}}
</x-filament::section>
```

<AutoScreenshot name="components/section/collapsible" alt="A collapsible section" version="5.x" />

### Making a section collapsed by default

You can make a section collapsed by default by using the `collapsed` attribute:

```blade
<x-filament::section
    collapsible
    collapsed
>
    <x-slot name="heading">
        User details
    </x-slot>

    {{-- Content --}}
</x-filament::section>
```

<AutoScreenshot name="components/section/collapsed" alt="A collapsed section" version="5.x" />

### Persisting collapsed sections

You can persist whether a section is collapsed in local storage using the `persist-collapsed` attribute, so it will remain collapsed when the user refreshes the page. You will also need a unique `id` attribute to identify the section from others, so that each section can persist its own collapse state:

```blade
<x-filament::section
    collapsible
    collapsed
    persist-collapsed
    id="user-details"
>
    <x-slot name="heading">
        User details
    </x-slot>

    {{-- Content --}}
</x-filament::section>
```

## Adding the section header aside the content instead of above it

You can change the position of the section header to be aside the content instead of above it by using the `aside` attribute:

```blade
<x-filament::section aside>
    <x-slot name="heading">
        User details
    </x-slot>

    {{-- Content --}}
</x-filament::section>
```

<AutoScreenshot name="components/section/aside" alt="A section with the header aside the content" version="5.x" />

### Positioning the content before the header

You can change the position of the content to be before the header instead of after it by using the `content-before` attribute:

```blade
<x-filament::section
    aside
    content-before
>
    <x-slot name="heading">
        User details
    </x-slot>

    {{-- Content --}}
</x-filament::section>
```

<AutoScreenshot name="components/section/content-before" alt="A section with content before the header" version="5.x" />
