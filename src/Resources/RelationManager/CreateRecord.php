<?php

namespace Filament\Resources\RelationManager;

use Filament\Forms\HasForm;
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
        $this->callHook('beforeValidate');

        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->callHook('afterValidate');

        $this->callHook('beforeCreate');

        $this->owner->{$this->getRelationship()}()->create($this->record);

        $this->callHook('afterCreate');

        $this->emit('refreshRelationManagerList', $this->manager);

        if (! $another) {
            $this->dispatchBrowserEvent('close', "{$this->manager}RelationManagerCreateModal");
        }

        $this->dispatchBrowserEvent('notify', __($this->createdMessage));

        $this->record = [];
        $this->fillWithFormDefaults();
    }

    public function getForm()
    {
        return $this->manager::form(
            Form::make()
                ->context(static::class)
                ->model(get_class($this->owner->{$this->getRelationship()}()->getModel()))
                ->record($this->record)
                ->submitMethod('create'),
        );
    }

    public function getRelationship()
    {
        $manager = $this->manager;

        return $manager::$relationship;
    }

    public function mount()
    {
        $this->callHook('beforeFill');

        $this->fillWithFormDefaults();

        $this->callHook('afterFill');
    }

    public function render()
    {
        return view('filament::resources.relation-manager.create-record');
    }

    protected function callHook($hook)
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
    }
}
