<x-filament-panels::page>
    {{ $this->form }}
    <x-filament::button color="gray" wire:click="$toggle('locked')">
        Toggle locked
    </x-filament::button>
    <pre id="js-field-server-state">{{ json_encode($data) }}</pre>
</x-filament-panels::page>
