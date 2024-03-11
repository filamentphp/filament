<x-filament-tables::cell
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-ta-group-selection-cell bg-gray-50 dark:bg-white/5 w-1'])
    "
>
    <div class="px-3">
        {{ $slot }}
    </div>
</x-filament-tables::cell>
