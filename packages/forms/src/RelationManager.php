<?php

namespace Filament\Forms;

use Filament\Tables\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Component;

class RelationManager extends Component
{
//    use HasForm;
    use HasTable;

    public $filterable = true;

    public $owner;

    public $searchable = true;

    public $sortable = true;

    protected static $relationship;

    public function columns()
    {
        return [];
    }

    public function fields()
    {
        return [];
    }

    public function filters()
    {
        return [];
    }

    protected function getQuery()
    {
        return $this->owner->{static::$relationship}();
    }

    public function getTable()
    {
        return Table::make($this->columns(), $this->filters())
            ->context(static::class)
            ->filterable($this->filterable)
            ->pagination(false)
            ->searchable($this->searchable)
            ->sortable($this->sortable);
    }

    public static function getTitle()
    {
        if (property_exists(static::class, 'title')) return static::$title;

        return (string) Str::of(static::$relationship)
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public function render()
    {
        return view('forms::relation-manager', [
            'records' => $this->getRecords(),
        ]);
    }
}
