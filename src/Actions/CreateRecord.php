<?php

namespace Filament\Actions;

use Filament\Actions\Concerns;
use Livewire\Component;

class CreateRecord extends Component
{
    use Concerns\HasForm;
    use Concerns\HasTitle;
    use Concerns\SendsToastNotifications;
    use Concerns\UsesResource;

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
        return view('filament::actions.create-resource-record', [
            'title' => static::getTitle(),
        ])->layout('filament::components.layouts.app');
    }
}
