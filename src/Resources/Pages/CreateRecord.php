<?php

namespace Filament\Resources\Pages;

use Filament\Filament;
use Filament\Resources\Forms\Actions;
use Filament\Resources\Forms\Form;
use Filament\Resources\Forms\HasForm;
use Illuminate\Support\Str;

class CreateRecord extends Page
{
    use HasForm;

    public static $cancelButtonLabel = 'filament::resources/pages/create-record.buttons.cancel.label';

    public static $createAnotherButtonLabel = 'filament::resources/pages/create-record.buttons.createAnother.label';

    public static $createButtonLabel = 'filament::resources/pages/create-record.buttons.create.label';

    public static $createdMessage = 'filament::resources/pages/create-record.messages.created';

    public static $indexRoute = 'index';

    public $record;

    public static $showRoute = 'edit';

    public static $view = 'filament::resources.pages.create-record';

    public function create($another = false)
    {
        $this->callHook('beforeValidate');

        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->callHook('afterValidate');

        $this->callHook('beforeCreate');

        $this->record = static::getModel()::create($this->record);

        $this->callHook('afterCreate');

        if ($another) {
            $this->fillRecord();

            $this->notify(__(static::$createdMessage));

            return;
        }

        $this->redirect($this->getResource()::generateUrl(static::$showRoute, [
            'record' => $this->record,
        ]));
    }

    public static function getBreadcrumbs()
    {
        return [
            static::getResource()::generateUrl() => (string) Str::title(static::getResource()::getPluralLabel()),
        ];
    }

    public function isAuthorized()
    {
        return Filament::can('create', static::getModel());
    }

    public function mount()
    {
        $this->fillRecord();

        $this->abortIfForbidden();
    }

    protected function actions()
    {
        return [
            Actions\Button::make(static::$createButtonLabel)
                ->primary()
                ->submit(),
            Actions\Button::make(static::$createAnotherButtonLabel)
                ->action('create(true)')
                ->primary(),
            Actions\Button::make(static::$cancelButtonLabel)
                ->url($this->getResource()::generateUrl(static::$indexRoute)),
        ];
    }

    protected function fillRecord()
    {
        $this->record = [];

        $this->callHook('beforeFill');

        $this->fillWithFormDefaults();

        $this->callHook('afterFill');
    }

    protected function form(Form $form)
    {
        return static::getResource()::form(
            $form->model(static::getModel()),
        );
    }
}
