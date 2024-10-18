<x-filament::empty-state.description
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-ta-empty-state-description'])
    "
>
    {{ $slot }}
</x-filament::empty-state.description>
