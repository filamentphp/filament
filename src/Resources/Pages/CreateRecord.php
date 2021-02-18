<?php

namespace Filament\Resources\Pages;

use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Filament\Resources\Page;

class CreateRecord extends Page
{
    use HasForm;

    protected static $view = 'filament::resources.pages.create-record';

    public $record;

    public $showRoute = 'edit';

    public function getForm()
    {
        return Form::make($this->fields())
            ->context(static::class)
            ->model(static::getModel());
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

        $this->redirect($this->getResource()::generateUrl($this->showRoute, [
            'record' => $record,
        ]));
    }
}
