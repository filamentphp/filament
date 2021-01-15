@props([
    'id' => Str::uuid(),
])

<div 
    class="relative"
    x-data="{ menuIsOpen: false, popper: null }"
    x-cloak
    x-init="popper = createPopper($refs.btn, $refs.menu, { modifiers: [flip, preventOverflow] })"
    @click.away="menuIsOpen = false"
    wire:ignore.self
>
    <button 
        type="button"
        x-ref="btn"
        @click="menuIsOpen = !menuIsOpen; $nextTick(() => popper.update())"
        aria-haspopup="true"
        aria-controls="{{ $id }}"
        :aria-expanded="menuIsOpen"
        {{ $attributes }}
    >
        {{ $button }}
    </button>
    <div 
        x-show="menuIsOpen" 
        x-ref="menu"
        role="menu"
        id="{{ $id }}"
        tabindex="-1"
        class="bg-white shadow-sm rounded text-sm leading-tight text-left overflow-hidden divide-y divide-gray-200"
    >
        {{ $slot }}
    </div>
</div>