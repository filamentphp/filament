<x-filament::page>
    <x-filament::form wire:submit.prevent="save">
        {{ $this->form }}

        <x-filament::form.actions
            :actions="$this->getCachedFormActions()"
            :alignment="static::getFormActionsAlignment()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament::form>
</x-filament::page>
