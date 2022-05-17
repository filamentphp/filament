<x-filament-support::modal.actions
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    :align="config('forms.components.actions.modal.actions.alignment')"
    :dark-mode="config('forms.dark_mode')"
>
    {{ $slot }}
</x-filament-support::modal.actions>
