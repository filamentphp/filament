<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit" data-testid="save-ranges">
            Save
        </x-filament::button>
    </form>

    <output data-testid="saved-period">
        {{ implode(',', $saved['period'] ?? []) }}
    </output>
    <output data-testid="saved-stay">
        {{ implode(',', $saved['stay'] ?? []) }}
    </output>
    <span data-testid="save-count">{{ $saveCount }}</span>
</x-filament-panels::page>
