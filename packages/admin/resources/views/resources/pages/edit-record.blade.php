<x-filament::page>
    <x-filament::form wire:submit.prevent="save">
        {{ $this->form }}

        <x-filament::actions :actions="$this->getFormActions()" />
    </x-filament::form>

    @if (count($relationManagers = $this->getResource()::getRelations()))
        <x-filament::hr />

        <x-filament::resources.relation-managers :active-manager="$activeRelationManager" :managers="$relationManagers" :owner-record="$record" />
    @endif

    <x-slot name="modals">
        <x-filament::modal id="delete">
            <x-slot name="heading">
                {{ __('filament::resources/pages/edit-record.modals.delete.heading', ['label' => $this->getRecordTitle() ?? static::getResource()::getLabel()]) }}
            </x-slot>

            <x-slot name="subheading">
                {{ __('filament::resources/pages/edit-record.modals.delete.subheading') }}
            </x-slot>

            <x-slot name="actions">
                <x-filament::modal.actions full-width>
                    <x-filament::button x-on:click="isOpen = false" color="secondary">
                        {{ __('filament::resources/pages/edit-record.modals.delete.buttons.cancel.label') }}
                    </x-filament::button>

                    <x-filament::button wire:click="delete" color="danger">
                        {{ __('filament::resources/pages/edit-record.modals.delete.buttons.delete.label') }}
                    </x-filament::button>
                </x-filament::modal.actions>
            </x-slot>
        </x-filament::modal>
    </x-slot>
</x-filament::page>
