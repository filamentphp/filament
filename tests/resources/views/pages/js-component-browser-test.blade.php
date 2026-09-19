<x-filament-panels::page>
    <div>
        <x-filament::button wire:click="$toggle('mounted')">
            Toggle mounted
        </x-filament::button>
        <x-filament::button wire:click="$toggle('cleared')">
            Toggle configuration
        </x-filament::button>
        <x-filament::button wire:click="$toggle('failed')">
            Toggle failure
        </x-filament::button>
    </div>

    @if ($mounted)
        {{ $this->form }}
    @endif
</x-filament-panels::page>
