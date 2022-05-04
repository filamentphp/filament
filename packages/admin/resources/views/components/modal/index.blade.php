<x-filament-support::modal
    :attributes="$attributes"
    :dark-mode="config('filament.dark_mode')"
    heading-component="filament::modal.heading"
    hr-component="filament::hr"
    subheading-component="filament::modal.subheading"
>
    {{ $slot }}
</x-filament-support::modal>
