<x-filament-panels::page>
    @include('renderer-readiness')

    {{ $this->filtersForm }}

    <x-filament::button wire:click="$toggle('showWidget')">
        Toggle widget
    </x-filament::button>
</x-filament-panels::page>
