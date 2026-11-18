<x-filament-panels::page>
    <form id="upload-form" wire:submit="save">
        {{ $this->form }}
        <x-filament::button type="submit" form="save" form-id="upload-form">
            Save
        </x-filament::button>
    </form>
</x-filament-panels::page>
