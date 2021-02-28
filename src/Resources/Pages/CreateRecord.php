<?php

namespace Filament\Resources\Pages;

use Filament\Forms\HasForm;
use Filament\Resources\Forms\Form;

class CreateRecord extends Page
{
    use HasForm;

    public static $createButtonLabel = 'Create';

    public static $showRoute = 'edit';

    public static $view = 'filament::resources.pages.create-record';

    public $record;

    public function create()
    {
        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $record = static::getModel()::create($this->record);

        $this->redirect($this->getResource()::generateUrl(static::$showRoute, [
            'record' => $record,
        ]));
    }

    public function getForm()
    {
        return static::getResource()::form(Form::make())
            ->context(static::class)
            ->model(static::getModel())
            ->submitMethod('create');
    }

    public function mount()
    {
        $this->record = [];

        $this->fillWithFormDefaults();
    }
}
