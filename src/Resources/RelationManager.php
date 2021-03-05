<?php

namespace Filament\Resources;

use Filament\Resources\Tables\Table;
use Filament\Tables\HasTable;
use Illuminate\Support\Str;
use Livewire\Component;

class RelationManager extends Component
{
    use HasTable;

    public static $createButtonLabel = 'filament::resources/relation-manager.buttons.create.label';

    public static $createModalCancelButtonLabel = 'filament::resources/relation-manager.modals.create.buttons.cancel.label';

    public static $createModalCreateButtonLabel = 'filament::resources/relation-manager.modals.create.buttons.create.label';

    public static $createModalCreatedMessage = 'filament::resources/relation-manager.modals.create.messages.created';

    public static $createModalHeading = 'filament::resources/relation-manager.modals.create.heading';

    public static $addButtonLabel = 'filament::resources/relation-manager.buttons.add.label';

    public static $addModalCancelButtonLabel = 'filament::resources/relation-manager.modals.add.buttons.cancel.label';

    public static $addModalAddButtonLabel = 'filament::resources/relation-manager.modals.add.buttons.add.label';

    public static $addModalAddedMessage = 'filament::resources/relation-manager.modals.add.messages.added';

    public static $addModalHeading = 'filament::resources/relation-manager.modals.add.heading';

    public static $editModalCancelButtonLabel = 'filament::resources/relation-manager.modals.edit.buttons.cancel.label';

    public static $editModalHeading = 'filament::resources/relation-manager.modals.edit.heading';

    public static $editModalSaveButtonLabel = 'filament::resources/relation-manager.modals.edit.buttons.save.label';

    public static $editModalSavedMessage = 'filament::resources/relation-manager.modals.edit.messages.saved';

    public static $relationship;

    public $filterable = true;

    public $owner;

    public $searchable = true;

    public $sortable = true;

    protected $listeners = [
        'refreshRelationManagerList' => 'refreshList',
    ];

    public static function getRelationship()
    {
        return static::$relationship;
    }

    public static function getTitle()
    {
        if (property_exists(static::class, 'title')) return static::$title;

        return (string) Str::of(static::$relationship)
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public function getTable()
    {
        return static::table(Table::make())
            ->filterable($this->filterable)
            ->pagination(false)
            ->recordAction('openEdit')
            ->searchable($this->searchable)
            ->sortable($this->sortable);
    }

    public function getModel()
    {
        return $this->getQuery()->getModel();
    }

    public function getQuery()
    {
        return $this->owner->{static::$relationship}();
    }

    public function openCreate()
    {
        $this->dispatchBrowserEvent('open', static::class . 'RelationManagerCreateModal');
    }

    public function openAdd()
    {
        $this->dispatchBrowserEvent('open', static::class . 'RelationManagerAddModal');
    }

    public function openEdit($record)
    {
        $this->emit('switchRelationManagerEditRecord', static::class, $record);

        $this->dispatchBrowserEvent('open', static::class . 'RelationManagerEditModal');
    }

    public function refreshList($manager = null)
    {
        if ($manager !== null && $manager !== static::class) return;

        $this->callMethod('$refresh');
    }

    public function render()
    {
        return view('filament::relation-manager', [
            'records' => $this->getRecords(),
        ]);
    }
}
