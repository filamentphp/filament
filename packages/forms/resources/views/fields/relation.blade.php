@php
    $managerLivewireComponentName = \Livewire\Livewire::getAlias($field->manager, $field->manager::getName());
@endphp

@livewire($managerLivewireComponentName, ['owner' => $field->record])
