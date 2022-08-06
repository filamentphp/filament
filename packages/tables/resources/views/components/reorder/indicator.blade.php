@props([
    'colspan',
])

<tr
    wire:key="{{ $this->id }}.table.reorder.indicator"
    x-cloak
    {{ $attributes->class(['bg-primary-500/10 filament-tables-selection-indicator']) }}
>
    <td class="px-4 py-2 whitespace-nowrap text-sm" colspan="{{ $colspan }}">
        <div>
            <x-filament-support::loading-indicator
                wire:loading.delay
                wire:target="reorderTable"
                class="animate-spin w-4 h-4 mr-3 text-primary-600"
            />

            <span>
                {{ __('tables::table.reorder_indicator') }}
            </span>
        </div>
    </td>
</tr>
