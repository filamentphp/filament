<?php

namespace Filament\Resources\RelationManager;

use Filament\Filament;
use Filament\Resources\Tables\HasTable;
use Filament\Resources\Tables\RecordActions;
use Filament\Resources\Tables\Table;
use Livewire\Component;

class ListRecords extends Component
{
    use HasTable;

    public $canAttach;

    public $canCreate;

    public $canDelete;

    public $canDetach;

    public $manager;

    public $model;

    public $owner;

    protected $listeners = [
        'refreshRelationManagerList' => 'refreshList',
    ];

    public function canAttach()
    {
        return $this->canAttach;
    }

    public function canCreate()
    {
        return $this->canCreate;
    }

    public function canDelete()
    {
        return $this->canDelete;
    }

    public function canDetach()
    {
        return $this->canDetach;
    }

    public function canDeleteSelected()
    {
        return $this->getModel()::find($this->selected)
            ->contains(function ($record) {
                return Filament::can('delete', $record);
            });
    }

    public function deleteSelected()
    {
        abort_unless($this->canDelete(), 403);

        $this->getModel()::destroy(
            $this->getModel()::find($this->selected)
                ->filter(function ($record) {
                    return Filament::can('delete', $record);
                })
                ->map(fn ($record) => $record->getKey())
                ->toArray(),
        );

        $this->selected = [];
    }

    public function detachSelected()
    {
        $relationship = $this->getRelationship();

        $relationship->detach($this->selected);

        $this->dispatchBrowserEvent('close', $this->getManager() . 'RelationManagerDetachModal');
        $this->dispatchBrowserEvent('notify', __($this->getManager()::$detachModalDetachedMessage));

        $this->selected = [];
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

    public function getRelationship()
    {
        return $this->getOwner()->{$this->getRelationshipName()}();
    }

    public function getRelationshipName()
    {
        return $this->getManager()::getRelationshipName();
    }

    public function getPrimaryColumnAction($record)
    {
        if (! Filament::can('update', $record)) {
            return;
        }

        return 'openEdit';
    }

    public function getQuery()
    {
        return $this->getRelationship();
    }

    public function getRecordActions()
    {
        $manager = $this->getManager();

        return [
            RecordActions\Link::make('edit')
                ->label($manager::$editRecordActionLabel)
                ->action('openEdit')
                ->when(fn ($record) => Filament::can('update', $record)),
        ];
    }

    public function hasPagination()
    {
        return false;
    }

    protected function table(Table $table)
    {
        return $this->getManager()::table(
            $table
                ->primaryColumnAction(function ($record) {
                    return $this->getPrimaryColumnAction($record);
                })
                ->recordActions($this->getRecordActions()),
        );
    }

    public function openAttach()
    {
        $this->dispatchBrowserEvent('open', $this->getManager() . 'RelationManagerAttachModal');
    }

    public function openCreate()
    {
        $this->dispatchBrowserEvent('open', $this->getManager() . 'RelationManagerCreateModal');
    }

    public function openDetach()
    {
        $this->dispatchBrowserEvent('open', $this->getManager() . 'RelationManagerDetachModal');
    }

    public function openEdit($record)
    {
        $this->emit('switchRelationManagerEditRecord', $this->getManager(), $record);

        $this->dispatchBrowserEvent('open', $this->getManager() . 'RelationManagerEditModal');
    }

    public function refreshList($manager = null)
    {
        if ($manager !== null && $manager !== $this->getManager()) {
            return;
        }

        $this->callMethod('$refresh');
    }

    public function render()
    {
        return view('filament::resources.relation-manager.list-records', [
            'records' => $this->getRecords(),
        ]);
    }
}
