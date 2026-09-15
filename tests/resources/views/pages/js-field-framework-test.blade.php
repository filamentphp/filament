<x-filament-panels::page>
    <div>
        <x-filament::button color="gray" wire:click="showValidationError">
            Show validation error
        </x-filament::button>
        <x-filament::button color="gray" wire:click="$refresh">
            Sync
        </x-filament::button>
        <x-filament::button color="gray" wire:click="replaceFields">
            Replace from server
        </x-filament::button>
        <x-filament::button color="gray" wire:click="resetFields">
            Reset
        </x-filament::button>
        <x-filament::button color="gray" wire:click="$toggle('locked')">
            Toggle locked
        </x-filament::button>
        <x-filament::button color="gray" wire:click="$toggle('mounted')">
            Toggle mounted
        </x-filament::button>
        <x-filament::button color="gray" wire:click="removeFirst">
            Remove first
        </x-filament::button>
        <x-filament::button color="gray" wire:click="$toggle('blurIsLive')">
            Toggle blur binding
        </x-filament::button>
    </div>
    @if ($mounted)
        {{ $this->form }}
    @endif

    <pre id="framework-server-state">{{ json_encode($data) }}</pre>
</x-filament-panels::page>
