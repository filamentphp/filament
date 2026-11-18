<x-filament-panels::page>
    <form id="upload-form" wire:submit="save">
        {{ $this->form }}
        <x-filament::button type="submit" form="save" form-id="upload-form">
            Save
        </x-filament::button>
    </form>
    <output data-testid="saved-data">{{ json_encode($savedData) }}</output>
</x-filament-panels::page>
