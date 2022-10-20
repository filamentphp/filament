<td
    {{ $attributes->class([
        'filament-tables-cell',
        'dark:text-white' => config('filament-tables.dark_mode'),
    ]) }}
>
    {{ $slot }}
</td>
