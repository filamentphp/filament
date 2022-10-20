@captureSlots([
    'trigger',
])

<x-filament-support::dropdown :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($slots)">
    {{ $slot }}
</x-filament-support::dropdown>
