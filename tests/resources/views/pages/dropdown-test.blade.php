<x-filament-panels::page>
    <x-filament::dropdown>
        <x-slot name="trigger">
            <x-filament::button data-testid="dropdown-trigger">
                Filters
            </x-filament::button>
        </x-slot>

        {{ $this->form }}
    </x-filament::dropdown>
</x-filament-panels::page>
