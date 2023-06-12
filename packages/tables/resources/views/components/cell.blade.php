<td
    {{
        $attributes->class([
            'filament-tables-cell',
            'dark:text-white' => config('tables.dark_mode'),
        ])
    }}
>
    {{ $slot }}
</td>
