<x-filament::actions
    :alignment="Alignment::Center"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-ta-empty-state-actions mt-6'])
    "
/>
