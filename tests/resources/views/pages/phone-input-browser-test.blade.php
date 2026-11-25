<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit" data-testid="save-phones">
            Save
        </x-filament::button>
    </form>

    <output data-testid="saved-phone">{{ $saved['phone'] ?? '' }}</output>
</x-filament-panels::page>
