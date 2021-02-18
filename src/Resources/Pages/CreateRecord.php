<?php

namespace Filament\Resources\Pages;

use Filament\Components\Concerns;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Filament\Page;

class CreateRecord extends Page
{
    use Concerns\UsesResource;
    use HasForm;

    protected static $view = 'filament::resources.pages.create-record';

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

        $this->redirect($this->getResource()::generateUrl($this->showRoute, [
            'record' => $record,
        ]));
    }
}
