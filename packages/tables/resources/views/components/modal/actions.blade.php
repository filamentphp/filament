<x-filament-support::modal.actions
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    :align="config('tables.layout.actions.modal.actions.alignment')"
    :dark-mode="config('tables.dark_mode')"
>
    {{ $slot }}
</x-filament-support::modal.actions>
