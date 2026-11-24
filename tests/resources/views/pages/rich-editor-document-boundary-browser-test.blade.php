<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        <x-filament::button type="submit" data-testid="save-boundary-editors">
            Save
        </x-filament::button>
        <output data-testid="boundary-save-count">{{ $saveCount }}</output>
    </form>
</x-filament-panels::page>
