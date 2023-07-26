@props([
    'label' => null,
])

<label class="flex">
    <x-filament::input.checkbox
        :attributes="
            \Filament\Support\prepare_inherited_attributes($attributes)
                ->merge([
                    'wire:loading.attr' => 'disabled',
                    'wire:target' => implode(',', \Filament\Tables\Table::LOADING_TARGETS),
                ], escape: false)
        "
    />

    <span class="sr-only">
        {{ $label }}
    </span>
</label>
