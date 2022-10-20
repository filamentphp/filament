@captureSlots([
    'detail',
])

<x-filament-support::dropdown.header :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($slots)">
    {{ $slot }}
</x-filament-support::dropdown.header>
