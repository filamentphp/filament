@props([
    'actions',
])

<div
    x-data
    x-cloak
    {{ $attributes->class(['relative filament-tables-bulk-actions']) }}
>
    <x-tables::bulk-actions.trigger />

    <div
        x-ref="panel"
        x-float.placement.bottom-start.flip.offset="{offset: 8}"
        x-transition:enter="transition"
        x-transition:enter-start="-translate-y-1 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-1 opacity-0"
        class="absolute z-10 hidden shadow-xl rounded-xl w-52"
    >
        <ul @class([
            'py-1 space-y-1 overflow-hidden bg-white shadow rounded-xl',
            'dark:border-gray-600 dark:bg-gray-700' => config('tables.dark_mode'),
        ])>
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </ul>
    </div>
</div>
