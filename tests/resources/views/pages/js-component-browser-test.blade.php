<x-filament-panels::page>
    @include('renderer-readiness')

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
        <output data-testid="server-caption">{{ $data['caption'] }}</output>

        {{ $this->form }}
    @endif
</x-filament-panels::page>
