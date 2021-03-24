<?php

namespace Filament\Resources\RelationManager;

use Filament\Filament;
use Filament\Resources\Tables\HasTable;
use Filament\Resources\Tables\RecordActions;
use Filament\Resources\Tables\Table;
use Livewire\Component;

class ListRecords extends Component
{
    use Concerns\CanCallHooks;
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

    public function canDeleteSelected()
    {
        return $this->model::find($this->selected)
            ->contains(function ($record) {
                return Filament::can('delete', $record);
            });
    }

    public function deleteSelected()
    {
        abort_unless($this->canDelete, 403);

        $this->callHook('beforeDelete');

        $this->model::destroy(
            $this->model::find($this->selected)
                ->filter(function ($record) {
                    return Filament::can('delete', $record);
                })
                ->map(fn ($record) => $record->getKey())
                ->toArray(),
        );

        $this->callHook('afterDelete');

        $this->selected = [];
    }

    public function detachSelected()
    {
        $manager = $this->manager;

        $relationship = $this->getRelationship();

        $this->callHook('beforeDetach');

        $relationship->detach($this->selected);

        $this->callHook('afterDetach');

        $this->dispatchBrowserEvent('close', $manager . 'RelationManagerDetachModal');
        $this->dispatchBrowserEvent('notify', __($manager::$detachModalDetachedMessage));

        $this->selected = [];
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
        $manager = $this->manager;

        return [
            RecordActions\Link::make('edit')
                ->label($manager::$editRecordActionLabel)
                ->action('openEdit')
                ->when(fn ($record) => Filament::can('update', $record)),
        ];
    }

    public function getRelationship()
    {
        return $this->owner->{$this->getRelationshipName()}();
    }

    public function getRelationshipName()
    {
        return $this->manager::getRelationshipName();
    }

    public function hasPagination()
    {
        return false;
    }

    public function openAttach()
    {
        $this->dispatchBrowserEvent('open', $this->manager . 'RelationManagerAttachModal');
    }

    public function openCreate()
    {
        $this->dispatchBrowserEvent('open', $this->manager . 'RelationManagerCreateModal');
    }

    public function openDetach()
    {
        $this->dispatchBrowserEvent('open', $this->manager . 'RelationManagerDetachModal');
    }

    public function openEdit($record)
    {
        $this->emit('switchRelationManagerEditRecord', $this->manager, $record);

        $this->dispatchBrowserEvent('open', $this->manager . 'RelationManagerEditModal');
    }

    public function refreshList($manager = null)
    {
        if ($manager !== null && $manager !== $this->manager) {
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

    protected function table(Table $table)
    {
        return $this->manager::table(
            $table
                ->primaryColumnAction(function ($record) {
                    return $this->getPrimaryColumnAction($record);
                })
                ->recordActions($this->getRecordActions()),
        );
    }
}
