<x-filament-tables::cell
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
>
    <div class="px-3 py-4">
        {{ $slot }}
    </div>
</x-filament-tables::cell>
