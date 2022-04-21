<x-filament::page :widget-record="$record" class="filament-resources-edit-record-page">
    <x-filament::form wire:submit.prevent="save">
        {{ $this->form }}

        <x-filament::form.actions :actions="$this->getCachedFormActions()" />
    </x-filament::form>

    @if (count($relationManagers = $this->getRelationManagers()))
        <x-filament::hr />

        <x-filament::resources.relation-managers :active-manager="$activeRelationManager" :managers="$relationManagers" :owner-record="$record" />
    @endif

    @if (config('filament.shortcuts.enabled'))
        <div x-data
             x-init="
            Mousetrap.bindGlobal(@js(config('filament.shortcuts.bindings.save')), $event => {
                $event.preventDefault()

                $wire.save()
            })
        "
        ></div>
    @endif
</x-filament::page>
