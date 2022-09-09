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
    :dark-mode="config('notifications.dark_mode')"
    heading-component="notifications::modal.heading"
    hr-component="notifications::hr"
    subheading-component="notifications::modal.subheading"
>
    {{ $slot }}
</x-filament-support::modal>
