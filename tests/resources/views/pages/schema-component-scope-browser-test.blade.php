<x-filament-panels::page>
    {{ $this->form }}

    @if ($scenario === 'flex')
        <x-filament::button
            wire:click="$toggle('reversed')"
            data-testid="reverse-fields"
        >
            Reverse fields
        </x-filament::button>
    @elseif ($scenario === 'layouts')
        <x-filament::button wire:click="removeFirst" data-testid="remove-first">
            Remove first
        </x-filament::button>
    @endif

    <output data-testid="incorrect-step-calls">
        {{ $incorrectStepCalls }}
    </output>
    <pre data-testid="server-state">{{ json_encode($data) }}</pre>
</x-filament-panels::page>
