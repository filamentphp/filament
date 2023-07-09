<x-filament::page
    @class([
        'filament-resources-create-record-page',
        'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
    ])
>
    <x-filament::form
        wire:submit="create"
        :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
    >
        {{ $this->form }}

        <x-filament::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament::form>
</x-filament::page>
