<?php

namespace Filament\Resources;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Forms\Form;
use Filament\Forms\HasForm;
use Filament\Tables\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Component;

class RelationManager extends Component
{
    use HasTable;

    public static $actions = [
        'create',
        'edit',
    ];

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

    protected function getQuery()
    {
        return $this->owner->{static::$relationship}();
    }

    public function openCreate()
    {
        $this->dispatchBrowserEvent('open', static::class.'RelationManagerCreateModal');
        $this->dispatchBrowserEvent('refresh-relation-manager-create-form', static::class);
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
