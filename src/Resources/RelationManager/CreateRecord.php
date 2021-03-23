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
        $manager = $this->manager;

        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->owner->{$this->getRelationshipName()}()->create($this->record);

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
        return $this->manager::form(
            $form->model(get_class($this->owner->{$this->getRelationshipName()}()->getModel())),
        );
    }

    public function getRelationshipName()
    {
        $manager = $this->manager;

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
