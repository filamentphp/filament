<?php

namespace Filament\Resources;

use Filament\Tables\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Component;

class RelationManager extends Component
{
    use HasTable;

    public static $createButtonLabel = 'New';

    public static $createModalCancelButtonLabel = 'Cancel';

    public static $createModalCreateButtonLabel = 'Create';

    public static $createdMessage = 'Created!';

    public static $createModalHeading = 'Create';

    public static $editModalCancelButtonLabel = 'Cancel';

    public static $editModalHeading = 'Edit';

    public static $editModalSaveButtonLabel = 'Save';

    public static $relationship;

    public static $savedMessage = 'Saved!';

    public $filterable = true;

    public $owner;

    public $searchable = true;

    public $sortable = true;

    protected $listeners = [
        'refreshRelationManagerList' => 'refreshList',
    ];

    public function getTable()
    {
        return static::table(Table::make())
            ->filterable($this->filterable)
            ->pagination(false)
            ->recordAction('openEdit')
            ->searchable($this->searchable)
            ->sortable($this->sortable);
    }

    public static function getRelationship()
    {
        return static::$relationship;
    }

    public function getModel()
    {
        return $this->getQuery()->getModel();
    }

    public function getQuery()
    {
        return $this->owner->{static::$relationship}();
    }

    public static function getTitle()
    {
        if (property_exists(static::class, 'title')) return static::$title;

        return (string) Str::of(static::$relationship)
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public function openCreate()
    {
        $this->dispatchBrowserEvent('open', static::class.'RelationManagerCreateModal');
    }

    public function openEdit($record)
    {
        $this->emit('switchRelationManagerEditRecord', static::class, $record);

        $this->dispatchBrowserEvent('open', static::class.'RelationManagerEditModal');
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
