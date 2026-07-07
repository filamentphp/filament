<x-filament-panels::page>
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
</x-filament-panels::page>
