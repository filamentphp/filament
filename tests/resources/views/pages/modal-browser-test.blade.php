@php
    use Illuminate\View\ComponentAttributeBag;
@endphp

<x-filament-panels::page>
    <span data-testid="action-after-child-result">
        {{ $didRunActionAfterClosingChild ? 'ran' : 'not-ran' }}
    </span>

    <button
        type="button"
        wire:click="mountAction('modalLessParentWithChild')"
        data-testid="modal-less-parent-trigger"
    >
        Open child from action without modal
    </button>

    <button
        type="button"
        wire:click="mountAction('runAfterClosingChild')"
        data-testid="action-after-child-trigger"
    >
        Run after closing child
    </button>

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
        :extra-modal-window-attribute-bag="new ComponentAttributeBag(['data-testid' => 'standalone-modal'])"
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
        :extra-modal-window-attribute-bag="new ComponentAttributeBag(['data-testid' => 'no-focus-restore-modal'])"
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
