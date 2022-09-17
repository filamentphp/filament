@props([
    'colspan',
])

<div
    wire:key="{{ $this->id }}.table.reorder.indicator"
    x-cloak
    {{ $attributes->class(['filament-tables-reorder-indicator bg-primary-500/10 px-4 py-2 whitespace-nowrap text-sm']) }}
>
    <x-filament-support::loading-indicator
        wire:loading.delay
        wire:target="reorderTable"
        class="animate-spin w-4 h-4 mr-3 text-primary-500"
    />

    <span>
        {{ __('tables::table.reorder_indicator') }}
    </span>
</div>
