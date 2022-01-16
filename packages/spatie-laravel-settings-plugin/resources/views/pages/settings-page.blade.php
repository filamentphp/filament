<x-filament::page>
    <x-filament::form wire:submit.prevent="save">
        {{ $this->form }}

        <x-filament::pages.actions :actions="$this->getFormActions()" />
    </x-filament::form>
</x-filament::page>
