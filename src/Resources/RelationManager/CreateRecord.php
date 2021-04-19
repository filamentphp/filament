<?php

namespace Filament\Resources\RelationManager;

use Filament\Resources\Forms\Actions;
use Filament\Resources\Forms\Form;
use Filament\Resources\Forms\HasForm;
use Livewire\Component;

class CreateRecord extends Component
{
    use Concerns\CanCallHooks;
    use HasForm;

    public $manager;

    public $model;

    public $owner;

    public $record = [];

    public function close()
    {
        $this->dispatchBrowserEvent('close', "{$this->manager}RelationManagerCreateModal");
    }

    public function create($another = false)
    {
        $manager = $this->manager;

        $this->callHook('beforeValidate');

        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->callHook('afterValidate');

        $this->callHook('beforeCreate');

        $this->record = $this->owner->{$this->getRelationshipName()}()->create($this->record);

        $this->callHook('afterCreate');

        $this->emit('refreshRelationManagerList', $manager);

        $this->fillRecord();

        if ($another) {
            $this->dispatchBrowserEvent('notify', __($manager::$createModalCreatedMessage));

            return;
        }

        $this->dispatchBrowserEvent('close', "{$manager}RelationManagerCreateModal");
    }

    public function getRelationshipName()
    {
        $manager = $this->manager;

        return $manager::$relationship;
    }

    public function mount()
    {
        $this->fillRecord();
    }

    public function render()
    {
        return view('filament::resources.relation-manager.create-record');
    }

    protected function actions()
    {
        $manager = $this->manager;

        return [
            Actions\Button::make($manager::$createModalCreateButtonLabel)
                ->primary()
                ->submit(),
            Actions\Button::make($manager::$createModalCreateAnotherButtonLabel)
                ->action('create(true)')
                ->primary(),
            Actions\Button::make($manager::$createModalCancelButtonLabel)
                ->action('close'),
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
        return $this->manager::form(
            $form->model(get_class($this->owner->{$this->getRelationshipName()}()->getModel())),
        );
    }
}
