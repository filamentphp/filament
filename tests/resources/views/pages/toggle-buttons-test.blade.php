<x-filament-panels::page>
    <form wire:submit="save(true)">
        {{ $this->form }}

        <x-filament::button type="submit" data-testid="save">
            Save
        </x-filament::button>
    </form>
</x-filament-panels::page>
