<x-filament-support::modal.subheading
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    :dark-mode="config('forms.dark_mode')"
>
    {{ $slot }}
</x-filament-support::modal.subheading>
