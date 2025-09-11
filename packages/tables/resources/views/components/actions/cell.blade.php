<x-filament-tables::cell
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-ta-actions-cell'])
    "
>
    <div class="px-3 py-4 whitespace-nowrap">
        {{ $slot }}
    </div>
</x-filament-tables::cell>
