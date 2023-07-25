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
            ->class([
                'fi-ta-reorder-indicator flex gap-x-3 bg-primary-50 px-3 py-2 dark:bg-primary-400/10 sm:px-6',
            ])
    }}
>
    <x-filament::loading-indicator
        wire:target="reorderTable"
        wire:loading.delay=""
        class="h-5 w-5 text-primary-500 dark:text-primary-400"
    />

    <span class="text-sm font-medium text-primary-600 dark:text-primary-400">
        {{ __('filament-tables::table.reorder_indicator') }}
    </span>
</div>
