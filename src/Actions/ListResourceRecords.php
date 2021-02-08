<?php

namespace Filament\Actions;

use Filament\ComponentConcerns;
use Livewire\Component;

class ListResourceRecords extends Component
{
    use ComponentConcerns\HasTable;
    use ComponentConcerns\HasTitle;
    use ComponentConcerns\UsesResource;

    public $createRoute = 'create';

    public function render()
    {
        return view('filament::actions.list-resource-records', [
            'records' => static::getModel()::paginate(10),
        ])->layout('filament::layouts.app', ['title' => static::getTitle()]);
    }
}
