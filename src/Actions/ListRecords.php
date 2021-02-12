<?php

namespace Filament\Actions;

use Filament\Actions\Concerns;
use Livewire\Component;

class ListRecords extends Component
{
    use Concerns\HasTable;
    use Concerns\HasTitle;
    use Concerns\UsesResource;

    public $createRoute = 'create';

    public function render()
    {
        return view('filament::actions.list-resource-records', [
            'records' => static::getModel()::paginate(10),
            'title' => static::getTitle(),
        ])->layout('filament::components.layouts.app');
    }
}
