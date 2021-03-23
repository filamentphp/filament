<?php

namespace Filament\Resources\RelationManager;

use Filament\Filament;
use Filament\Resources\Forms\HasForm;
use Filament\Resources\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class EditRecord extends Component
{
    use HasForm;

    public $manager;

    public $owner;

    public $record = [];

    protected $listeners = [
        'switchRelationManagerEditRecord' => 'switchRecord',
    ];

    protected function form(Form $form)
    {
        return $this->getManager()::form($form->model(get_class($this->getOwner()->{$this->getRelationshipName()}()->getModel())));
    }

    public function getQuery()
    {
        return $this->getRelationship();
    }

    public function getRelationship()
    {
        return $this->getOwner()->{$this->getRelationshipName()}();
    }

    public function getManager()
    {
        return $this->manager;
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
        return view('filament::resources.relation-manager.edit-record');
    }

    public function save()
    {
        $manager = $this->getManager();

        abort_unless(Filament::can('update', $this->record), 403);

        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->record->save();

        $this->emit('refreshRelationManagerList', $manager);

        $this->dispatchBrowserEvent('close', "{$manager}RelationManagerEditModal");
        $this->dispatchBrowserEvent('notify', __($manager::$editModalSavedMessage));

        $this->record = [];
    }

    public function switchRecord($manager, $record)
    {
        if ($manager !== $this->getManager()) {
            return;
        }

        $this->record = $this->getQuery()->find($record);
        $this->resetTemporaryUploadedFiles();
    }
}
