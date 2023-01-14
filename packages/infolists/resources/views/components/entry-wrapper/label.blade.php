@props([
    'prefix' => null,
    'suffix' => null,
])

<dt {{ $attributes->class(['filament-infolists-entry-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse']) }}>
    {{ $prefix }}

    <span class="text-sm text-gray-500 font-medium leading-4 dark:text-gray-400">
        {{ $slot }}
    </span>

    {{ $suffix }}
</dt>
