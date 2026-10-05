<x-filament-panels::page>
    <x-filament::dropdown>
        <x-slot name="trigger">
            <x-filament::button data-testid="dropdown-trigger">
                Filters
            </x-filament::button>
        </x-slot>

        {{ $this->form }}

        <x-filament::button data-testid="outer-dropdown-action">
            Outer action
        </x-filament::button>

        <div
            x-data="{ isSecondaryDropdownShown: false }"
            data-testid="secondary-dropdown-container"
        >
            <template x-if="isSecondaryDropdownShown">
                <x-filament::dropdown>
                    <x-slot name="trigger">
                        <x-filament::button
                            data-testid="secondary-dropdown-trigger"
                        >
                            Secondary filters
                        </x-filament::button>
                    </x-slot>

                    <div data-testid="secondary-dropdown-content">
                        <x-filament::dropdown>
                            <x-slot name="trigger">
                                <x-filament::button
                                    data-testid="secondary-dropdown-content-button"
                                    tooltip="Secondary content tooltip"
                                >
                                    Secondary content
                                </x-filament::button>
                            </x-slot>

                            <div data-testid="tertiary-dropdown-content">
                                Tertiary content
                            </div>
                        </x-filament::dropdown>
                    </div>
                </x-filament::dropdown>
            </template>
        </div>
    </x-filament::dropdown>

    <x-filament::button data-testid="refresh" wire:click="refresh">
        Refreshed {{ $refreshCount }} times
    </x-filament::button>
</x-filament-panels::page>
