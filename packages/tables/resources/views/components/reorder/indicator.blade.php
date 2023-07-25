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
                'fi-ta-reorder-indicator flex gap-x-3 whitespace-nowrap bg-primary-50 px-6 py-2 text-sm font-medium text-primary-600 dark:bg-primary-400/10 dark:text-primary-400',
            ])
    }}
>
    <x-filament::loading-indicator
        wire:target="reorderTable"
        wire:loading.delay=""
        class="h-5 w-5 text-primary-500 dark:text-primary-400"
    />

    <span>
        {{ __('filament-tables::table.reorder_indicator') }}
    </span>
</div>
