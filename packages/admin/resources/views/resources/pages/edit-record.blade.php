<x-filament::page
    :widget-data="['record' => $record]"
    class="filament-resources-edit-record-page filament-resource-{{ str_replace('/', '-', $this->getResource()::getSlug()) }} filament-record-{{ $this->getRecord()->getKey() }}"
>
    <x-filament::form wire:submit.prevent="save">
        {{ $this->form }}

        <x-filament::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament::form>

    @if (count($relationManagers = $this->getRelationManagers()))
        <x-filament::hr />

        <x-filament::resources.relation-managers :active-manager="$activeRelationManager" :managers="$relationManagers" :owner-record="$record" :page-class="static::class" />
    @endif
</x-filament::page>
