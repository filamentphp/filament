<?php

namespace Filament\Resources\RelationManager;

use Filament\Resources\Forms\HasForm;
use Filament\Resources\Forms\Form;
use Livewire\Component;

class CreateRecord extends Component
{
    use HasForm;

    public $cancelButtonLabel;

    public $createAnotherButtonLabel;

    public $createButtonLabel;

    public $createdMessage;

    public $manager;

    public $owner;

    public $record = [];

    public function create($another = false)
    {
        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->owner->{$this->getRelationship()}()->create($this->record);

        $this->emit('refreshRelationManagerList', $this->manager);

        if (! $another) {
            $this->dispatchBrowserEvent('close', "{$this->manager}RelationManagerCreateModal");
        }

        $this->dispatchBrowserEvent('notify', __($this->createdMessage));

        $this->record = [];
        $this->fillWithFormDefaults();
    }

    protected function form(Form $form)
    {
        return $this->manager::form(
            $form->model(get_class($this->owner->{$this->getRelationship()}()->getModel())),
        );
    }

    public function getRelationship()
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
