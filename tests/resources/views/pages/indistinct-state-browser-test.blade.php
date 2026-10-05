<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit" data-testid="save">
            Save
        </x-filament::button>

        @if ($this->isSaved)
            <p role="status" data-testid="saved">Saved</p>
        @endif
    </form>
</x-filament-panels::page>
