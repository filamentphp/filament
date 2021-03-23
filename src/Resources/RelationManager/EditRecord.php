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

    public $cancelButtonLabel;

    public $manager;

    public $owner;

    public $record = [];

    public $saveButtonLabel;

    public $savedMessage;

    protected $listeners = [
        'switchRelationManagerEditRecord' => 'switchRecord',
    ];

    public function getForm()
    {
        $form = Form::for($this)->model(get_class($this->owner->{$this->getRelationship()}()->getModel()));

        return $this->manager::form($form);
    }

    public function getQuery()
    {
        return $this->owner->{$this->getRelationship()}();
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
        return view('filament::resources.relation-manager.edit-record');
    }

    public function save()
    {
        abort_unless(Filament::can('update', $this->record), 403);

        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->record->save();

        $this->emit('refreshRelationManagerList', $this->manager);

        $this->dispatchBrowserEvent('close', "{$this->manager}RelationManagerEditModal");
        $this->dispatchBrowserEvent('notify', __($this->savedMessage));

        $this->record = [];
    }

    public function switchRecord($manager, $record)
    {
        if ($manager !== $this->manager) {
            return;
        }

        $this->record = $this->getQuery()->find($record);
        $this->resetTemporaryUploadedFiles();
    }
}
