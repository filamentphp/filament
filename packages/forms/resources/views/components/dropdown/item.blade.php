@captureSlots([
    'detail',
])

<x-forms::dropdown.list.item :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($slots)">
    {{ $slot }}
</x-forms::dropdown.list.item>
