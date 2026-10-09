<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit" data-testid="save-timed">
            Save
        </x-filament::button>
        <x-filament::button
            type="button"
            wire:click="reloadForm"
            data-testid="reload-timed"
        >
            Reload saved time
        </x-filament::button>
        <x-filament::button
            type="button"
            wire:click="$toggle('hasDisabledDates')"
            data-testid="replace-calendar"
        >
            Toggle unavailable date
        </x-filament::button>
    </form>

    <output data-testid="saved-timed">{{ $saved['field'] ?? '' }}</output>
    <span
        data-testid="timed-save-count"
        data-reload-count="{{ $reloadCount }}"
    >
        {{ $saveCount }}
    </span>
</x-filament-panels::page>
