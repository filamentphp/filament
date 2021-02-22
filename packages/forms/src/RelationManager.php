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

    protected static $relationship;

    public $filterable = true;

    public $owner;

    public $searchable = true;

    public $sortable = true;

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
            ->context(static::class)
            ->filterable($this->filterable)
            ->pagination(false)
            ->searchable($this->searchable)
            ->sortable($this->sortable);
    }

    public function render()
    {
        return view('forms::relation-manager', [
            'records' => $this->getRecords(),
        ]);
    }

    protected function getQuery()
    {
        return $this->owner->{static::$relationship}();
    }
}
