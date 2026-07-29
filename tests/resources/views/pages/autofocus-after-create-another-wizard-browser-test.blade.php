<x-filament-panels::page>
    {{ $this->form }}

    <button
        type="button"
        wire:click="simulateCreateAnother"
        data-testid="simulate-create-another"
    >
        Create &amp; create another
    </button>
</x-filament-panels::page>
