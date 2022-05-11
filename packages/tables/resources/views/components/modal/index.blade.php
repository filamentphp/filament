@captureSlots([
    'actions',
    'footer',
    'header',
    'heading',
    'subheading',
    'displayClasses',
])

<x-filament-support::modal
    :attributes="$attributes->merge($slots)"
    :dark-mode="config('tables.dark_mode')"
    heading-component="tables::modal.heading"
    hr-component="tables::hr"
    subheading-component="tables::modal.subheading"
>
    {{ $slot }}
</x-filament-support::modal>
