<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit" data-testid="save-relationship">
            Save
        </x-filament::button>
    </form>

    @if ($isSaved)
        <output data-testid="relationship-saved">Saved</output>
    @endif
</x-filament-panels::page>
