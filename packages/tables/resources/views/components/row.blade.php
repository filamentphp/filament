@props([
    'recordUrl' => null,
])

<tr
    {{ $attributes->class([
        'hover:bg-gray-50' => $recordUrl,
        'dark:hover:bg-gray-500/10' => $recordUrl && config('tables.dark_mode'),
        'filament-tables-row',
    ]) }}
>
    {{ $slot }}
</tr>
