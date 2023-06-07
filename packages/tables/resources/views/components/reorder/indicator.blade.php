@props([
    'colspan',
])

<div
    wire:key="{{ $this->id }}.table.reorder.indicator"
    x-cloak
    {{ $attributes->class(['filament-tables-reorder-indicator whitespace-nowrap bg-primary-500/10 px-4 py-2 text-sm']) }}
>
    <x-filament-support::loading-indicator
        wire:loading.delay
        wire:target="reorderTable"
        class="mr-3 h-4 w-4 text-primary-500 rtl:ml-3 rtl:mr-0"
    />

    <span>
        {{ __('tables::table.reorder_indicator') }}
    </span>
</div>
