<x-filament-panels::page>
    <form wire:submit="save">
        <x-filament::button type="submit">Validate</x-filament::button>

        {{ $this->form }}
    </form>
</x-filament-panels::page>
