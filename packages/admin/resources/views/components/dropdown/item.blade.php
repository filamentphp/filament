@captureSlots([
    'detail',
])

<x-filament::dropdown.list.item :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($slots)">
    {{ $slot }}
</x-filament::dropdown.list.item>
