<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit" data-testid="save">
            Save
        </x-filament::button>
    </form>

    @if (count($saved))
        <output data-testid="saved">
            {{ json_encode($saved['accessible_data']) }}
        </output>
    @endif
</x-filament-panels::page>
