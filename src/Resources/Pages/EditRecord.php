<?php

namespace Filament\Resources\Pages;

use Filament\Forms\HasForm;
use Filament\Resources\Forms\Form;

class EditRecord extends Page
{
    use HasForm;

    public static $deleteButtonLabel = 'Delete';

    public static $deleteModalCancelButtonLabel = 'Cancel';

    public static $deleteModalConfirmButtonLabel = 'Delete';

    public static $deleteModalDescription = 'Are you sure you would like to delete this record? This action cannot be undone.';

    public static $deleteModalHeading = 'Delete this record?';

    public static $saveButtonLabel = 'Save';

    public static $savedMessage = 'Saved!';

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
        return static::getResource()::form(Form::make())
            ->context(static::class)
            ->model(static::getModel())
            ->record($this->record)
            ->submitMethod('save');
    }

    public function mount($record)
    {
        $this->record = static::getModel()::findOrFail($record);
    }

    public function save()
    {
        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->record->save();

        $this->notify(__(static::$savedMessage));
    }
}
