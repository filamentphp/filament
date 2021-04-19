<?php

namespace Filament\Resources\RelationManager;

use Filament\Filament;
use Filament\Resources\Concerns\CanCallHooks;
use Filament\Resources\Forms\Actions;
use Filament\Resources\Forms\Form;
use Filament\Resources\Forms\HasForm;
use Livewire\Component;

class EditRecord extends Component
{
    use CanCallHooks;
    use HasForm;

    public $manager;

    public $owner;

    public $record = [];

    protected $listeners = [
        'switchRelationManagerEditRecord' => 'switchRecord',
    ];

    public function close()
    {
        $this->dispatchBrowserEvent('close', "{$this->manager}RelationManagerEditModal");
    }

    public function getQuery()
    {
        return $this->getRelationship();
    }

    public function getRelationship()
    {
        return $this->owner->{$this->getRelationshipName()}();
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
        return view('filament::resources.relation-manager.edit-record');
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

        $this->emit('refreshRelationManagerList', $manager);

        $this->dispatchBrowserEvent('close', "{$manager}RelationManagerEditModal");
        $this->dispatchBrowserEvent('notify', __($manager::$editModalSavedMessage));

        $this->record = [];
    }

    public function switchRecord($manager, $recordKey)
    {
        if ($manager !== $this->manager) {
            return;
        }

        $this->callHook('beforeFill');

        $this->record = $this->getQuery()->find($recordKey);
        $this->resetTemporaryUploadedFiles();

        $this->callHook('afterFill');
    }

    protected function actions()
    {
        $manager = $this->manager;

        return [
            Actions\Button::make($manager::$editModalSaveButtonLabel)
                ->primary()
                ->submit(),
            Actions\Button::make($manager::$editModalCancelButtonLabel)
                ->action('close'),
        ];
    }

    protected function fillRecord()
    {
        $this->fillWithFormDefaults();
    }

    protected function form(Form $form)
    {
        return $this->manager::form($form->model(get_class($this->owner->{$this->getRelationshipName()}()->getModel())));
    }
}
