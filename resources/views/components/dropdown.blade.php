@props([
    'id' => Str::uuid(),
    'asButton' => false,
])

<div
    class="relative"
    x-data="{ open: false, popper: null }"
    x-cloak
    x-init="popper = createPopper($refs.btn, $refs.menu, { 
        modifiers: [
            flip, 
            preventOverflow,
        ]
    })"
    @click.away="open = false"
    wire:ignore
>
    @if ($asButton)
        <x-filament::button 
            x-ref="btn"
            x-on:click="open = !open; $nextTick(() => popper.update())"
            aria-haspopup="true"
            :aria-controls="$id"
            x-bind:aria-expanded="open"
            :attributes="$attributes"
        >
            {{ $button }}
        </x-filament::button>
    @else 
        <button
            type="button"
            aria-haspopup="true"
            aria-controls="{{ $id }}"
            x-ref="btn"
            @click="open = !open; $nextTick(() => popper.update())"
            :aria-expanded="open"
            {{ $attributes }}
        >
            {{ $button }}
        </button>
    @endif

    <div
        x-show="open"
        x-ref="menu"
        role="menu"
        id="{{ $id }}"
        tabindex="-1"
        class="absolute z-50 bg-gray-700 text-white shadow-md rounded overflow-hidden p-1.5"
    >
        {{ $slot }}
    </div>
</div>
