<x-filament-tables::cell
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-ta-selection-cell w-1'])
    "
>
    <div class="px-3 py-4">
        {{ $slot }}
    </div>
</x-filament-tables::cell>
