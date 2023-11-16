<x-filament::icon-button
    color="gray"
    icon="heroicon-m-bars-2"
    {{-- @deprecated Use `tables::reorder.handle` instead of `tables::reorder.button`. --}}
    :icon-alias="['tables::reorder.handle', 'tables::reorder.button']"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['cursor-move'])
    "
/>
