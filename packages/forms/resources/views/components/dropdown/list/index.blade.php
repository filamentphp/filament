<x-filament-support::dropdown.list
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    :dark-mode="config('forms.dark_mode')"
>
    {{ $slot }}
</x-filament-support::dropdown.list>
