@php
    $managerLivewireComponentName = \Livewire\Livewire::getAlias($formComponent->manager, $formComponent->manager::getName());
@endphp

@livewire($managerLivewireComponentName, ['owner' => $formComponent->record])
