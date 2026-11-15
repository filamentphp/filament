<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit">Save</x-filament::button>
    </form>

    <x-filament::button data-testid="outside-picker">
        Outside picker
    </x-filament::button>

    <x-filament::button
        data-testid="update-blocks"
        wire:click="$toggle('hasUpdatedBlocks')"
    >
        Change available blocks
    </x-filament::button>

    <x-filament::button
        data-testid="enable-blocks"
        wire:click="$set('hasNoBlocks', false)"
    >
        Enable blocks
    </x-filament::button>

    <x-filament::button
        data-testid="widen-picker"
        wire:click="$set('hasWidePicker', true)"
    >
        Widen block picker
    </x-filament::button>
</x-filament-panels::page>
