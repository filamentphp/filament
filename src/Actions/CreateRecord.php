<?php

namespace Filament\Actions;

use Filament\Actions\Concerns;
use Filament\Forms\HasForm;
use Livewire\Component;

class CreateRecord extends Component
{
    use Concerns\HasTitle;
    use Concerns\SendsToastNotifications;
    use Concerns\UsesResource;
    use HasForm;

    public $record;

    public $showRoute = 'edit';

    public function mount()
    {
        $this->record = [];

        $this->fillWithFormDefaults();
    }

    public function submit()
    {
        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $record = static::getModel()::create($this->record);

        $this->redirect($this->getResource()::route($this->showRoute, [
            'record' => $record,
        ]));
    }

    public function render()
    {
        return view('filament::actions.create-record', [
            'title' => static::getTitle(),
        ])->layout('filament::components.layouts.app');
    }
}
