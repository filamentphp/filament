<?php

namespace Filament\Resources\Actions;

use Filament\Components\Concerns;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Filament\Page;
use Livewire\Component;

class CreateRecord extends Page
{
    use Concerns\HasTitle;
    use Concerns\SendsToastNotifications;
    use Concerns\UsesResource;
    use HasForm;

    public $record;

    public $showRoute = 'edit';

    public function getForm()
    {
        return Form::make($this->fields())
            ->context(static::class);
    }

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
        return view('filament::resources.actions.create-record', [
            'title' => static::getTitle(),
        ])->layout('filament::components.layouts.app');
    }
}
