@props([
    'prefix' => null,
    'suffix' => null,
])

<dt
    {{ $attributes->class(['filament-infolists-entry-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse']) }}
>
    {{ $prefix }}

    <span
        class="text-sm font-medium leading-4 text-gray-500 dark:text-gray-400"
    >
        {{ $slot }}
    </span>

    {{ $suffix }}
</dt>
