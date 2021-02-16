<?php

namespace Filament\Actions;

use Filament\Actions\Concerns;
use Filament\Tables\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class ListRecords extends Component
{
    use Concerns\HasTitle;
    use Concerns\UsesResource;
    use HasTable;

    public $createRoute = 'create';

    public $pagination = true;

    public $recordRoute = 'edit';

    public $searchable = true;

    public $sortable = true;

    public function getTable()
    {
        return Table::make($this->getColumns())
            ->context(static::class)
            ->pagination($this->pagination)
            ->recordUrl(fn($record) => $this->getResource()::route($this->recordRoute, ['record' => $record]))
            ->searchable($this->searchable)
            ->sortable($this->sortable);
    }

    public function render()
    {
        return view('filament::actions.list-records', [
            'records' => $this->getRecords(),
            'title' => static::getTitle(),
        ])->layout('filament::components.layouts.app');
    }
}
