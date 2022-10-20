<x-filament-support::modal.actions
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    :alignment="config('filament-forms.components.actions.modal.actions.alignment')"
>
    {{ $slot }}
</x-filament-support::modal.actions>
