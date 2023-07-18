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
            ->class(['fi-ta-actions-cell whitespace-nowrap px-4 py-3'])
    }}
>
    {{ $slot }}
</td>
