<x-filament-panels::page>
    {{ $this->filtersForm }}

    <x-filament::button wire:click="$toggle('showWidget')">
        Toggle widget
    </x-filament::button>
</x-filament-panels::page>
