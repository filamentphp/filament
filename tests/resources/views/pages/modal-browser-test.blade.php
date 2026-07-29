<x-filament-panels::page>
    <button
        type="button"
        data-testid="behind-button"
        style="position: fixed; bottom: 2rem; right: 2rem; z-index: 1"
        x-data="{ label: 'Behind: not clicked' }"
        x-text="label"
        x-on:click="label = 'Behind: clicked'"
    ></button>

    <x-filament::modal
        id="standalone-browser-test-modal"
        :extra-modal-window-attribute-bag="new \Illuminate\View\ComponentAttributeBag(['data-testid' => 'standalone-modal'])"
    >
        <x-slot name="trigger">
            <x-filament::button data-testid="standalone-trigger">
                Standalone modal
            </x-filament::button>
        </x-slot>

        <p>Standalone modal content.</p>

        <x-filament::button
            data-testid="standalone-close"
            x-on:click="$dispatch('close-modal', { id: 'standalone-browser-test-modal' })"
        >
            Close
        </x-filament::button>
    </x-filament::modal>

    <x-filament::modal
        id="standalone-browser-test-no-focus-restore-modal"
        :restores-focus="false"
        :extra-modal-window-attribute-bag="new \Illuminate\View\ComponentAttributeBag(['data-testid' => 'no-focus-restore-modal'])"
    >
        <x-slot name="trigger">
            <x-filament::button data-testid="no-focus-restore-trigger">
                No focus restore modal
            </x-filament::button>
        </x-slot>

        <p>Standalone modal content.</p>

        <x-filament::button
            data-testid="no-focus-restore-close"
            x-on:click="$dispatch('close-modal', { id: 'standalone-browser-test-no-focus-restore-modal' })"
        >
            Close
        </x-filament::button>
    </x-filament::modal>
</x-filament-panels::page>
