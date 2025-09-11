@props([
    'prefix' => null,
    'suffix' => null,
])

<dt
    {{ $attributes->class(['fi-in-entry-wrp-label inline-flex items-center gap-x-3']) }}
>
    {{ $prefix }}

    <span class="text-sm leading-6 font-medium text-gray-950 dark:text-white">
        {{ $slot }}
    </span>

    {{ $suffix }}
</dt>
