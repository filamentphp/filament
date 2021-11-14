<x-filament::page>
    <x-filament::header :actions="$this->getActions()">
        <x-slot name="heading">
            {{ $title }}
        </x-slot>
    </x-filament::header>

    <x-filament::form wire:submit.prevent="create">
        {{ $this->form }}

        <x-filament::actions :actions="$this->getFormActions()" />
    </x-filament::form>
</x-filament::page>
