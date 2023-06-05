@captureSlots([
    'detail',
])

<x-tables::dropdown.list.item
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($slots)"
>
    {{ $slot }}
</x-tables::dropdown.list.item>
