<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit">Save</x-filament::button>
    </form>

    <x-filament::button data-testid="outside-picker">
        Outside picker
    </x-filament::button>
</x-filament-panels::page>
