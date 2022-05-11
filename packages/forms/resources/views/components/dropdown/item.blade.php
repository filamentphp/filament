@captureSlots([
    'detail',
])

<x-filament-support::dropdown.item
    :attributes="$attributes->merge($slots)"
    :dark-mode="config('forms.dark_mode')"
>
    {{ $slot }}
</x-filament-support::dropdown.item>
