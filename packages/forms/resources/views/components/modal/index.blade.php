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
    heading-component="filament-forms::modal.heading"
    hr-component="filament-forms::hr"
    subheading-component="filament-forms::modal.subheading"
>
    {{ $slot }}
</x-filament-support::modal>
