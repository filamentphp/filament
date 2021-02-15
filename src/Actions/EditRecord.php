<?php

namespace Filament\Actions;

use Filament\Actions\Concerns;
use Filament\Forms\HasForm;
use Livewire\Component;

class EditRecord extends Component
{
    use Concerns\HasTitle;
    use Concerns\SendsToastNotifications;
    use Concerns\UsesResource;
    use HasForm;

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
        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->record->save();

        $this->notify('Saved!');
    }

    public function render()
    {
        return view('filament::actions.edit-record', [
            'title' => static::getTitle(),
        ])->layout('filament::components.layouts.app');
    }
}
