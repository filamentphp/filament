<?php

namespace Filament\Resources\Pages;

use Filament\Filament;
use Filament\Resources\Forms\Actions;
use Filament\Resources\Forms\Form;
use Filament\Resources\Forms\HasForm;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class EditRecord extends Page
{
    use HasForm;

    public static $cancelButtonLabel = 'filament::resources/pages/edit-record.buttons.cancel.label';

    public static $deleteButtonLabel = 'filament::resources/pages/edit-record.buttons.delete.label';

    public static $deleteModalCancelButtonLabel = 'filament::resources/pages/edit-record.modals.delete.buttons.cancel.label';

    public static $deleteModalConfirmButtonLabel = 'filament::resources/pages/edit-record.modals.delete.buttons.confirm.label';

    public static $deleteModalDescription = 'filament::resources/pages/edit-record.modals.delete.description';

    public static $deleteModalHeading = 'filament::resources/pages/edit-record.modals.delete.heading';

    public static $indexRoute = 'index';

    public $record;

    public static $saveButtonLabel = 'filament::resources/pages/edit-record.buttons.save.label';

    public static $savedMessage = 'filament::resources/pages/edit-record.messages.saved';

    public static $view = 'filament::resources.pages.edit-record';

    public function canDelete()
    {
        return Filament::can('delete', $this->record);
    }

    public function delete()
    {
        $this->authorize('delete');

        $this->callHook('beforeDelete');

        $this->record->delete();

        $this->callHook('afterDelete');

        $this->redirect($this->getResource()::generateUrl(static::$indexRoute));
    }

    public static function getBreadcrumbs()
    {
        return [
            static::getResource()::generateUrl() => (string) Str::title(static::getResource()::getPluralLabel()),
        ];
    }

    public function isAuthorized()
    {
        return Filament::can('update', $this->record);
    }

    public function mount($record)
    {
        $this->fillRecord($record);

        $this->abortIfForbidden();
    }

    public function save()
    {
        $this->callHook('beforeValidate');

        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->callHook('afterValidate');

        $this->callHook('beforeSave');

        $this->record->save();

        $this->callHook('afterSave');

        $this->notify(__(static::$savedMessage));
    }

    protected function actions()
    {
        return [
            Actions\Button::make(static::$saveButtonLabel)
                ->primary()
                ->submit(),
            Actions\Button::make(static::$cancelButtonLabel)
                ->url($this->getResource()::generateUrl(static::$indexRoute)),
        ];
    }

    protected function fillRecord($key)
    {
        $this->callHook('beforeFill');

        $this->record = $this->resolveRecord($key);

        $this->callHook('afterFill');
    }

    protected function form(Form $form)
    {
        return static::getResource()::form(
            $form->model(static::getModel()),
        );
    }

    protected function resolveRecord($key)
    {
        $model = static::getModel();

        $record = (new $model())->resolveRouteBinding($key);

        if ($record === null) {
            throw (new ModelNotFoundException())->setModel($model, [$key]);
        }

        return $record;
    }
}
