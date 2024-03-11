<x-filament-tables::cell
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-ta-actions-cell'])
    "
>
    <div class="whitespace-nowrap px-3 py-4">
        {{ $slot }}
    </div>
</x-filament-tables::cell>
