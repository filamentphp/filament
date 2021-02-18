<?php

namespace Filament\Resources\Actions;

use Filament\Components\Concerns;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Filament\Page;
use Livewire\Component;

class EditRecord extends Page
{
    use Concerns\UsesResource;
    use HasForm;

    public $record;

    public $indexRoute = 'index';

    protected static $view = 'filament::resources.actions.edit-record';

    public function delete()
    {
        $this->record->delete();

        $this->redirect($this->getResource()::generateUrl($this->indexRoute));
    }

    public function getForm()
    {
        return Form::make($this->fields())
            ->context(static::class)
            ->record($this->record);
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
}
