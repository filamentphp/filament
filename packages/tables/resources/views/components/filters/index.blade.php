@props([
    'form',
])

<div
    x-data="{ isOpen: false }"
    x-cloak
    {{ $attributes->class(['relative inline-block']) }}
>
    <x-tables::trigger />

    <div
        x-show="isOpen"
        x-on:click.away="isOpen = false"
        x-transition:enter="transition ease duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="absolute right-0 z-10 max-w-xs w-screen mt-2 shadow-xl top-full rounded-xl"
    >
        <div class="px-6 py-4 bg-white shadow rounded-xl">
            {{ $form }}
        </div>
    </div>
</div>
