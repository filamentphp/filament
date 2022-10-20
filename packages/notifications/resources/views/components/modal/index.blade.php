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
    :dark-mode="config('filament-notifications.dark_mode')"
    heading-component="filament-notifications::modal.heading"
    hr-component="filament-notifications::hr"
    subheading-component="filament-notifications::modal.subheading"
>
    {{ $slot }}
</x-filament-support::modal>
