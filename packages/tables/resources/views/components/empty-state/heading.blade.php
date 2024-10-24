<x-filament::empty-state.heading
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-ta-empty-state-heading'])
    "
>
    {{ $slot }}
</x-filament::empty-state.heading>
