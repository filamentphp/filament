@props([
    'record',
])

<td
    wire:loading.remove.delay
    {{
        $attributes
            ->merge([
                'wire:target' => implode(',', \Filament\Tables\Table::LOADING_TARGETS),
            ], escape: false)
            ->class(['filament-tables-actions-cell whitespace-nowrap px-4 py-3'])
    }}
>
    {{ $slot }}
</td>
