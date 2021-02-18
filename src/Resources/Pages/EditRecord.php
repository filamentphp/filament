<?php

namespace Filament\Resources\Pages;

use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Filament\Resources\Page;

class EditRecord extends Page
{
    use HasForm;

    protected static $view = 'filament::resources.pages.edit-record';

    public $record;

    public $indexRoute = 'index';

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
