@props([
    'extraAttributes' => [],
])

<td
    {{
        $attributes
            ->merge($extraAttributes)
            ->class([
                'filament-tables-cell',
                'dark:text-white' => config('tables.dark_mode'),
            ])
    }}
>
    {{ $slot }}
</td>
