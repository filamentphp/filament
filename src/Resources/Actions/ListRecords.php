<?php

namespace Filament\Resources\Actions;

use Filament\Components\Concerns;
use Filament\Tables\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Component;

class ListRecords extends Component
{
    use Concerns\HasTitle;
    use Concerns\UsesResource;
    use HasTable;

    public $filterable = true;

    public $createRoute = 'create';

    public $pagination = true;

    public $recordRoute = 'edit';

    public $searchable = true;

    public $sortable = true;

    public function getTable()
    {
        return Table::make($this->columns(), $this->filters())
            ->context(static::class)
            ->filterable($this->filterable)
            ->pagination($this->pagination)
            ->recordUrl(fn($record) => $this->getResource()::route($this->recordRoute, ['record' => $record]))
            ->searchable($this->searchable)
            ->sortable($this->sortable);
    }

    public static function getTitle()
    {
        if (static::$title) return static::$title;

        return (string) Str::of(class_basename(static::getModel()))
            ->kebab()
            ->replace('-', ' ')
            ->plural()
            ->title();
    }

    public function render()
    {
        return view('filament::resources.actions.list-records', [
            'records' => $this->getRecords(),
            'title' => static::getTitle(),
        ])->layout('filament::components.layouts.app');
    }
}
