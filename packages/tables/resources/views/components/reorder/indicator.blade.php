@props([
    'colspan',
])

<div
    x-cloak
    {{
        $attributes
            ->merge([
                'wire:key' => "{$this->getId()}.table.reorder.indicator",
            ], escape: false)
            ->class(['filament-tables-reorder-indicator whitespace-nowrap bg-primary-500/10 px-4 py-2 text-sm'])
    }}
>
    <x-filament::loading-indicator
        wire:loading.delay=""
        wire:target="reorderTable"
        class="me-3 h-4 w-4 text-primary-500"
    />

    <span>
        {{ __('filament-tables::table.reorder_indicator') }}
    </span>
</div>
