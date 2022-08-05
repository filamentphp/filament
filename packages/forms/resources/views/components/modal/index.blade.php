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
    :dark-mode="config('forms.dark_mode')"
    heading-component="forms::modal.heading"
    hr-component="forms::hr"
    subheading-component="forms::modal.subheading"
>
    {{ $slot }}
</x-filament-support::modal>
