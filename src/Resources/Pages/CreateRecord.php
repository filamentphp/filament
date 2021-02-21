<?php

namespace Filament\Resources\Pages;

use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Filament\Resources\Page;

class CreateRecord extends Page
{
    use HasForm;

    public static $createButtonLabel = 'Create';

    public static $showRoute = 'edit';

    protected static $view = 'filament::resources.pages.create-record';

    public $record;

    public function getForm()
    {
        return Form::make($this->fields())
            ->context(static::class)
            ->model(static::getModel())
            ->submitMethod('create');
    }

    public function mount()
    {
        $this->record = [];

        $this->fillWithFormDefaults();
    }

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
}
