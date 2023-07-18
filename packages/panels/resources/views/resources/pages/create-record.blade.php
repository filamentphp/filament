<x-filament::page
    @class([
        'fi-resources-create-record-page',
        'fi-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
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
