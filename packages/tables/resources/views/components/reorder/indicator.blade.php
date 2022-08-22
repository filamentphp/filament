@props([
    'colspan',
])

<div
    wire:key="{{ $this->id }}.table.reorder.indicator"
    x-cloak
    {{ $attributes->class(['bg-primary-500/10 px-4 py-2 whitespace-nowrap text-sm filament-tables-reorder-indicator']) }}
>
    <x-filament-support::loading-indicator
        wire:loading.delay
        wire:target="reorderTable"
        class="animate-spin w-4 h-4 mr-3 text-primary-600"
    />

    <span>
        {{ __('tables::table.reorder_indicator') }}
    </span>
</div>
