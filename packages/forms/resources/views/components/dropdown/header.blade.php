@captureSlots([
    'detail',
])

<x-filament-support::dropdown.header
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($slots)"
    :dark-mode="config('forms.dark_mode')"
>
    {{ $slot }}
</x-filament-support::dropdown.header>
