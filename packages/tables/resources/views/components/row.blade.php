@props([
    'recordUrl' => null,
    'striped' => false,
])

<tr
    {{ $attributes->class([
        'filament-tables-row',
        'hover:bg-gray-50' => $recordUrl,
        'dark:hover:bg-gray-500/10' => $recordUrl && config('tables.dark_mode'),
        'even:bg-gray-100' => $striped,
        'dark:even:bg-gray-900' => $striped && config('tables.dark_mode'),
    ]) }}
>
    {{ $slot }}
</tr>
