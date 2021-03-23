<?php

namespace Filament\Resources\RelationManager;

use Filament\Resources\Forms\HasForm;
use Filament\Resources\Forms\Form;
use Livewire\Component;

class CreateRecord extends Component
{
    use HasForm;

    public $manager;

    public $model;

    public $owner;

    public $record = [];

    public function create($another = false)
    {
        $manager = $this->getManager();

        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->getOwner()->{$this->getRelationshipName()}()->create($this->record);

        $this->emit('refreshRelationManagerList', $manager);

        if (! $another) {
            $this->dispatchBrowserEvent('close', "{$manager}RelationManagerCreateModal");
        }

        $this->dispatchBrowserEvent('notify', __($manager::$createModalCreatedMessage));

        $this->record = [];
        $this->fillWithFormDefaults();
    }

    protected function form(Form $form)
    {
        return $this->getManager()::form(
            $form->model(get_class($this->getOwner()->{$this->getRelationshipName()}()->getModel())),
        );
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function getRelationshipName()
    {
        $manager = $this->getManager();

        return $manager::$relationship;
    }

    public function mount()
    {
        $this->fillWithFormDefaults();
    }

    public function render()
    {
        return view('filament::resources.relation-manager.create-record');
    }
}
