<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit">Save</x-filament::button>
    </form>

    <pre data-testid="saved-code">{{ $savedCode }}</pre>
</x-filament-panels::page>
