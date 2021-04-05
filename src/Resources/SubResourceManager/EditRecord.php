<?php

namespace Filament\Resources\SubResourceManager;

use Filament\Filament;
use Filament\Resources\Forms\Actions;
use Filament\Resources\Forms\Form;
use Filament\Resources\Forms\HasForm;
use Filament\Resources\Concerns\CanCallHooks;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;

class EditRecord extends Component
{
    use HasForm;
    use CanCallHooks;

    public $manager;

    public $record;

    public function mount($model)
    {
        $this->fillRecord($model);
    }

    public function render()
    {
        return view('filament::resources.subResource-manager.edit-record');
    }

    public function save()
    {
        $manager = $this->manager;

        abort_unless(Filament::can('update', $this->record), 403);

        $this->callHook('beforeValidate');

        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->callHook('afterValidate');

        $this->callHook('beforeSave');

        $this->record->save();

        $this->callHook('afterSave');

        //$this->emit('refreshRelationManagerList', $manager);

        $this->dispatchBrowserEvent('notify', __($manager::$savedMessage));
    }

    protected function actions()
    {
        $manager = $this->manager;

        return [
            Actions\Button::make($manager::$saveButtonLabel)
                ->primary()
                ->submit(),
            Actions\Button::make($manager::$cancelButtonLabel)
                ->url("#TODO"),
            // TODO: Make this generate proper URLs
        ];
    }

    protected function fillRecord($record)
    {
        $this->callHook('beforeFill');

        $this->record = $record;

        if ($this->record === null) {
            throw (new ModelNotFoundException())->setModel(get_class($record), [$record]);
        }

        $this->callHook('afterFill');
    }

    protected function form(Form $form)
    {
        return $this->manager::form(
            $form->model($this->record)
        );
    }
}
