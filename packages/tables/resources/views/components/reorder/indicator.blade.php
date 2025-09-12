<div
    x-cloak
    {{
        $attributes
            ->merge([
                'wire:key' => "{$this->getId()}.table.reorder.indicator",
            ], escape: false)
            ->class([
                'fi-ta-reorder-indicator flex gap-x-3 bg-gray-50 px-3 py-1.5 dark:bg-white/5 sm:px-6',
            ])
    }}
>
    <x-filament::loading-indicator
        :attributes="
            \Filament\Support\prepare_inherited_attributes(
                new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                    'wire:target' => 'reorderTable',
                ])
            )->class(['h-5 w-5 text-gray-400 dark:text-gray-500'])
        "
    />

    <span
        class="text-sm font-medium leading-6 text-gray-700 dark:text-gray-200"
    >
        {{ __('filament-tables::table.reorder_indicator') }}
    </span>
</div>
