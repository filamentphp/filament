---
title: Modal Blade component
---

## Overview

The modal component is able to open a dialog window or slide-over with any content:

```blade
<x-filament::modal>
    <x-slot name="trigger">
        <x-filament::button>
            Open modal
        </x-filament::button>
    </x-slot>

    {{-- Modal content --}}
</x-filament::modal>
```

## Controlling a modal from JavaScript

You can use the `trigger` slot to render a button that opens the modal. However, this is not required. You have complete control over when the modal opens and closes through JavaScript. First, give the modal an ID so that you can reference it:

```blade
<x-filament::modal id="edit-user">
    {{-- Modal content --}}
</x-filament::modal>
```

Now, you can dispatch an `open-modal` or `close-modal` browser event, passing the modal's ID, which will open or close the modal. For example, from a Livewire component:

```php
$this->dispatch('open-modal', id: 'edit-user');
```

Or from Alpine.js:

```php
$dispatch('open-modal', { id: 'edit-user' })
```

## Adding a heading to a modal

You can add a heading to a modal by using the `heading` slot:

```blade
<x-filament::modal>
    <x-slot name="heading">
        Modal heading
    </x-slot>

    {{-- Modal content --}}
</x-filament::modal>
```

## Adding a description to a modal

You can add a description, below the heading, to a modal by using the `description` slot:

```blade
<x-filament::modal>
    <x-slot name="heading">
        Modal heading
    </x-slot>

    <x-slot name="description">
        Modal description
    </x-slot>

    {{-- Modal content --}}
</x-filament::modal>
```

## Adding an icon to a modal

You can add an [icon](https://blade-ui-kit.com/blade-icons?set=1#search) to a modal by using the `icon` attribute:

```blade
<x-filament::modal icon="heroicon-o-information-circle">
    <x-slot name="heading">
        Modal heading
    </x-slot>

    {{-- Modal content --}}
</x-filament::modal>
```

By default, the color of an icon is "primary". You can change it to be `danger`, `gray`, `info`, `success` or `warning` by using the `icon-color` attribute:

```blade
<x-filament::modal
    icon="heroicon-o-exclamation-triangle"
    icon-color="danger"
>
    <x-slot name="heading">
        Modal heading
    </x-slot>

    {{-- Modal content --}}
</x-filament::modal>
```

## Adding a footer to a modal

You can add a footer to a modal by using the `footer` slot:

```blade
<x-filament::modal>
    {{-- Modal content --}}
    
    <x-slot name="footer">
        {{-- Modal footer content --}}
    </x-slot>
</x-filament::modal>
```

Alternatively, you can add actions into the footer by using the `footerActions` slot:

```blade
<x-filament::modal>
    {{-- Modal content --}}
    
    <x-slot name="footerActions">
        {{-- Modal footer actions --}}
    </x-slot>
</x-filament::modal>
```

## Changing the modal's alignment

By default, modal content will be aligned to the start, or centered if the modal is `xs` or `sm` in [width](#changing-the-modal-width). If you wish to change the alignment of content in a modal, you can use the `alignment` attribute and pass it `start` or `center`:

```blade
<x-filament::modal alignment="center">
    {{-- Modal content --}}
</x-filament::modal>
```

## Using a slide-over instead of a modal

You can open a "slide-over" dialog instead of a modal by using the `slide-over` attribute:

```blade
<x-filament::modal slide-over>
    {{-- Slide-over content --}}
</x-filament::modal>
```

## Making the modal header sticky

The header of a modal scrolls out of view with the modal content when it overflows the modal size. However, slide-overs have a sticky modal that's always visible. You may control this behavior using the `sticky-header` attribute:

```blade
<x-filament::modal sticky-header>
    <x-slot name="heading">
        Modal heading
    </x-slot>

    {{-- Modal content --}}
</x-filament::modal>
```

## Making the modal footer sticky

The footer of a modal is rendered inline after the content by default. Slide-overs, however, have a sticky footer that always shows when scrolling the content. You may enable this for a modal too using the `sticky-footer` attribute:

```blade
<x-filament::modal sticky-footer>
    {{-- Modal content --}}
    
    <x-slot name="footer">
        {{-- Modal footer content --}}
    </x-slot>
</x-filament::modal>
```

## Changing the modal width

You can change the width of the modal by using the `width` attribute. Options correspond to [Tailwind's max-width scale](https://tailwindcss.com/docs/max-width). The options are `xs`, `sm`, `md`, `lg`, `xl`, `2xl`, `3xl`, `4xl`, `5xl`, `6xl`, `7xl`, and `screen`:

```blade
<x-filament::modal width="5xl">
    {{-- Modal content --}}
</x-filament::modal>
```

## Closing the modal by clicking away

By default, when you click away from a modal, it will close itself. If you wish to disable this behavior for a specific action, you can use the `close-by-clicking-away` attribute:

```blade
<x-filament::modal :close-by-clicking-away="false">
    {{-- Modal content --}}
</x-filament::modal>
```

## Hiding the modal close button

By default, modals have a close button in the top right corner. You can remove the close button from the modal by using the `close-button` attribute:

```blade
<x-filament::modal :close-button="false">
    {{-- Modal content --}}
</x-filament::modal>
```
