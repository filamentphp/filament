<x-filament::page class="filament-resources-create-record-page filament-resource-{{ str_replace('/', '-', $this->getResource()::getSlug()) }}">
    <x-filament::form wire:submit.prevent="create">
        {{ $this->form }}

        <x-filament::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament::form>
</x-filament::page>
