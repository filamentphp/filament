<x-filament::page.simple>
    <x-slot name="subheading">
        {{ $this->backAction }}
    </x-slot>

    <x-filament::form wire:submit="save">
        {{ $this->form }}

        <x-filament::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament::form>
</x-filament::page.simple>
