<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit">Save</x-filament::button>
    </form>

    <p>
        Submitted code:
        <span>{{ data_get($data, 'code') }}</span>
    </p>
</x-filament-panels::page>
