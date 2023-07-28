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

    @if (filled($label))
        <span class="sr-only">
            {{ $label }}
        </span>
    @endif
</label>
