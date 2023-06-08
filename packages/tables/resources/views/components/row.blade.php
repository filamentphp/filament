@props([
    'recordAction' => null,
    'recordUrl' => null,
    'striped' => false,
])

<tr
    {{
        $attributes->class([
            'filament-tables-row transition',
            'hover:bg-gray-50 dark:hover:bg-gray-500/10' => $recordUrl || $recordAction,
            'even:bg-gray-100 dark:even:bg-gray-900' => $striped,
        ])
    }}
>
    {{ $slot }}
</tr>
