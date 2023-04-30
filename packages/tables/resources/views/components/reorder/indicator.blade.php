@props([
    'colspan',
])

<div
    x-cloak
    {{
        $attributes
            ->merge([
                'wire:key' => "{$this->id}.table.reorder.indicator",
            ], escape: false)
            ->class(['filament-tables-reorder-indicator bg-primary-500/10 px-4 py-2 whitespace-nowrap text-sm'])
    }}
>
    <x-filament::loading-indicator
        wire:loading.delay=""
        wire:target="reorderTable"
        class="w-4 h-4 me-3 text-primary-500"
    />

    <span>
        {{ __('filament-tables::table.reorder_indicator') }}
    </span>
</div>
