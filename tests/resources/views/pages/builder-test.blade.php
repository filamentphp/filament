<x-filament-panels::page>
    <button type="button" data-testid="outside-reordering">
        Outside the field
    </button>

    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit">Save</x-filament::button>
    </form>
</x-filament-panels::page>
