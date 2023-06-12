@captureSlots([
    'detail',
])

<x-notifications::dropdown.list.item
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($slots)"
>
    {{ $slot }}
</x-notifications::dropdown.list.item>
