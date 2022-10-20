@props([
    'recordAction' => null,
    'recordUrl' => null,
    'striped' => false,
])

<tr
    {{ $attributes->class([
        'filament-tables-row transition',
        'hover:bg-gray-50' => $recordUrl || $recordAction,
        'dark:hover:bg-gray-500/10' => ($recordUrl || $recordAction) && config('filament-tables.dark_mode'),
        'even:bg-gray-100' => $striped,
        'dark:even:bg-gray-900' => $striped && config('filament-tables.dark_mode'),
    ]) }}
>
    {{ $slot }}
</tr>
