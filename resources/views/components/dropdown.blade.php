@props([
    'id',
])

<div class="relative"
    x-data="{ menuIsOpen: false, popper: null }"
    x-init="popper = createPopper($refs.btn, $refs.menu, { modifiers: [flip, preventOverflow] })"
    @click.away="menuIsOpen = false"
    wire:ignore.self>
    <button type="button"
        x-ref="btn"
        @click="menuIsOpen = !menuIsOpen; $nextTick(() => popper.update())"
        aria-haspopup="true"
        aria-controls="{{ $id }}"
        :aria-expanded="menuIsOpen"
        {{ $attributes }}>
        {{ $slot }}
    </button>
    <div x-show="menuIsOpen" 
        x-cloak
        x-ref="menu"
        role="menu"
        id="{{ $id }}"
        tabindex="-1">
        {{ $content }}
    </div>
</div>