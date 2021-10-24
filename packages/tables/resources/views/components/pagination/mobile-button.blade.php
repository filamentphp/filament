@props([
    'icon',
    'label',
])

<button
    type="button"
    {{ $attributes->class([
        'flex items-center justify-center w-10 h-10 text-primary-500 transition rounded-full hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none',
    ]) }}
>
    <x-dynamic-component :component="$icon" class="w-7 h-7" />

    <span class="sr-only">
        {{ $label }}
    </span>
</button>
