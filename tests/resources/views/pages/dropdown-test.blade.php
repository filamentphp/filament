<x-filament-panels::page>
    <x-filament::dropdown>
        <x-slot name="trigger">
            <x-filament::button data-testid="dropdown-trigger">
                Filters
            </x-filament::button>
        </x-slot>

        {{ $this->form }}
    </x-filament::dropdown>

    <x-filament::button data-testid="refresh" wire:click="refresh">
        Refreshed {{ $refreshCount }} times
    </x-filament::button>
</x-filament-panels::page>
