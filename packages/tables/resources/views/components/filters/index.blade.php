@props([
    'form',
    'width' => 'sm',
])

<div
    x-data="{ isOpen: false }"
    x-cloak
    {{ $attributes->class(['relative inline-block']) }}
>
    <x-tables::filters.trigger />

    <div
        x-show="isOpen"
        x-on:click.away="isOpen = false"
        x-transition:enter="transition ease duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        @class([
            'absolute right-0 rtl:right-auto rtl:left-0 z-10 w-screen pl-12 rtl:pr-12 mt-2 top-full',
            match ($width) {
                'xs' => 'max-w-xs',
                'md' => 'max-w-md',
                'lg' => 'max-w-lg',
                'xl' => 'max-w-xl',
                '2xl' => 'max-w-2xl',
                '3xl' => 'max-w-3xl',
                '4xl' => 'max-w-4xl',
                '5xl' => 'max-w-5xl',
                '6xl' => 'max-w-6xl',
                '7xl' => 'max-w-7xl',
                default => 'max-w-sm',
            },
        ])
    >
        <div class="px-6 py-4 bg-white shadow-xl rounded-xl">
            {{ $form }}
        </div>
    </div>
</div>
