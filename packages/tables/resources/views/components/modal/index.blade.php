@captureSlots([
    'actions',
    'content',
    'footer',
    'header',
    'heading',
    'subheading',
    'trigger',
])

<x-filament-support::modal
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($slots)"
    heading-component="filament-tables::modal.heading"
    hr-component="filament-tables::hr"
    subheading-component="filament-tables::modal.subheading"
>
    {{ $slot }}
</x-filament-support::modal>
