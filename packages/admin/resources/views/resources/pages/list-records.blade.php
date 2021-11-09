<x-filament::page>
    <x-filament::header :actions="$this->getActions()">
        <x-slot name="heading">
            {{ static::getTitle() }}
        </x-slot>
    </x-filament::header>

    {{ $this->table }}
</x-filament::page>
