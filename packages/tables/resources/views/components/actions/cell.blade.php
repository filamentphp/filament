@props([
    'record',
])

<td
    wire:loading.remove.delay
    wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
    {{ $attributes->class(['filament-tables-actions-cell whitespace-nowrap px-4 py-3']) }}
>
    {{ $slot }}
</td>
