<?php

namespace Filament\Actions;

use Filament\ComponentConcerns;
use Livewire\Component;

class CreateResourceRecord extends Component
{
    use ComponentConcerns\HasForm;
    use ComponentConcerns\HasTitle;
    use ComponentConcerns\SendsToastNotifications;
    use ComponentConcerns\UsesResource;

    public $record;

    public $showRoute = 'edit';

    public function mount()
    {
        $this->record = [];

        $this->fillWithFormDefaults();
    }

    public function submit()
    {
        $this->validate();

        $record = static::getModel()::create($this->record);

        $this->redirect($this->getResource()::route($this->showRoute, [
            'record' => $record,
        ]));
    }

    public function render()
    {
        return view('filament::actions.create-resource-record')
            ->layout('filament::components.layouts.app', ['title' => static::getTitle()]);
    }
}
