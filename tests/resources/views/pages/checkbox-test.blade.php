<x-filament-panels::page>
    <form
        wire:submit="save"
        data-testid="checkbox-form"
        data-update-count="{{ $this->updateCount }}"
    >
        {{ $this->form }}

        <x-filament::button type="submit">Save</x-filament::button>
    </form>
</x-filament-panels::page>
