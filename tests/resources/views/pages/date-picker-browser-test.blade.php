<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit" data-testid="save-dates">
            Save
        </x-filament::button>
        <x-filament::button
            type="button"
            wire:click="reloadForm"
            data-testid="reload-dates"
        >
            Reload saved dates
        </x-filament::button>
    </form>

    <output data-testid="saved-native-date">{{ $saved['date'] ?? '' }}</output>
    <span data-testid="save-count" data-reload-count="{{ $reloadCount }}">
        {{ $saveCount }}
    </span>
</x-filament-panels::page>
