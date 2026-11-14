<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit">Save</x-filament::button>
        <x-filament::button
            type="button"
            wire:click="$toggle('isSelectDisabled')"
            data-testid="toggle-select-disabled"
        >
            Toggle channel availability
        </x-filament::button>
    </form>
</x-filament-panels::page>
