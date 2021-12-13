<x-filament::page>
    <x-filament::form wire:submit.prevent="create">
        {{ $this->form }}

        <x-filament::form.actions :actions="$this->getFormActions()" />
    </x-filament::form>
</x-filament::page>
