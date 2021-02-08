<?php

namespace Filament\Actions;

use Filament\ComponentConcerns;
use Livewire\Component;

class EditResourceRecord extends Component
{
    use ComponentConcerns\HasForm;
    use ComponentConcerns\HasTitle;
    use ComponentConcerns\SendsToastNotifications;
    use ComponentConcerns\UsesResource;

    public $indexRoute = 'index';

    public function delete()
    {
        $this->record->delete();

        $this->redirect($this->getResource()::route($this->indexRoute));
    }

    public function mount($record)
    {
        $this->record = static::getModel()::findOrFail($record);
    }

    public function submit()
    {
        $this->validate();

        $this->record->save();

        $this->notify('Saved!');
    }

    public function render()
    {
        return view('filament::actions.edit-resource-record', [
            'title' => static::getTitle(),
        ])->layout('filament::components.layouts.app');
    }
}
