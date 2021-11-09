<x-filament::page>
    <x-filament::header>
        <x-slot name="heading">
            {{ static::getTitle() }}
        </x-slot>
    </x-filament::header>

    {{ $this->form }}

    <x-filament::actions :actions="$this->getFormActions()" />
</x-filament::page>
