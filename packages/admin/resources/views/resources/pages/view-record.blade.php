<x-filament::page>
    <x-filament::header :actions="$this->getActions()">
        <x-slot name="heading">
            {{ $title }}
        </x-slot>
    </x-filament::header>

    {{ $this->form }}
</x-filament::page>
