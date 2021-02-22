<?php

namespace Filament\Resources;

use Filament\Resources\Forms\Form;
use Filament\Forms\HasForm;
use Filament\Tables\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Component;

class RelationManager extends Component
{
    use HasForm;
    use HasTable;

    public static $actions = [
        'create',
        'delete',
        'edit',
    ];

    public static $relationship;

    public static $formSubmitMethods = [
        'create' => 'create',
        'edit' => 'save',
    ];

    public $context;

    public $filterable = true;

    public $owner;

    public $record;

    public $searchable = true;

    public $sortable = true;

    public function create()
    {
        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $record = static::getModel()::create($this->record);

        $this->redirect($this->getResource()::generateUrl(static::$showRoute, [
            'record' => $record,
        ]));
    }

    public static function getTitle()
    {
        if (property_exists(static::class, 'title')) return static::$title;

        return (string) Str::of(static::$relationship)
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public function getForm()
    {
        $form = static::form(Form::make())
            ->context($this->context)
            ->model(get_class($this->owner->{static::$relationship}()->getModel()));

        if ($this->record instanceof Model) {
            $form->record($this->record);
        }

        if (array_key_exists($this->context, static::$formSubmitMethods)) {
            $form->submitMethod(static::$formSubmitMethods[$this->context]);
        }

        return $form;
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

    protected function getQuery()
    {
        return $this->owner->{static::$relationship}();
    }

    public function openCreate()
    {
        $this->context = 'create';

        $this->record = [];
        $this->fillWithFormDefaults();
        $this->resetTemporaryUploadedFiles();

        $this->dispatchBrowserEvent('open', static::$relationship.'CreateModal');
    }

    public function openEdit($record)
    {
        $this->context = 'edit';

        $this->record = $this->getQuery()->find($record);
        $this->resetTemporaryUploadedFiles();

        $this->dispatchBrowserEvent('open', static::$relationship.'EditModal');
    }

    public function save()
    {
        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->record->save();

        $this->notify(__(static::$savedMessage));
    }

    public function render()
    {
        return view('filament::relation-manager', [
            'records' => $this->getRecords(),
        ]);
    }
}
