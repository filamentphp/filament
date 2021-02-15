<?php

namespace Filament\Actions;

use Filament\Actions\Concerns;
use Filament\Tables\HasTable;
use Livewire\Component;

class ListRecords extends Component
{
    use Concerns\HasTitle;
    use Concerns\UsesResource;
    use HasTable;

    public $createRoute = 'create';

    public function render()
    {
        return view('filament::actions.list-records', [
            'records' => $this->getRecords(),
            'title' => static::getTitle(),
        ])->layout('filament::components.layouts.app');
    }
}
