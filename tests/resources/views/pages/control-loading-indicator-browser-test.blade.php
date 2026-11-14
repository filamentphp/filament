<x-filament-panels::page>
    <div>
        <x-filament::button
            wire:click="slow"
            icon="heroicon-m-bolt"
            data-testid="blade-button"
        >
            Blade button
        </x-filament::button>

        <div role="menu" aria-label="Blade dropdown">
            <x-filament::dropdown.list.item
                wire:click="slow"
                icon="heroicon-m-bolt"
                data-testid="blade-dropdown-item"
            >
                Blade dropdown item
            </x-filament::dropdown.list.item>
        </div>
    </div>
</x-filament-panels::page>
