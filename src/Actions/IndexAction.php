<?php

namespace Filament\Actions;

use Filament\Action;

abstract class IndexAction extends Action
{
    public $createAction = 'create';

    public $hasRouteParameter = false;

    public function render()
    {
        return view('filament::actions.index', [
            'records' => $this->getModel()::paginate(10),
        ])->layout('filament::layouts.app', ['title' => $this->getTitle()]);
    }
}
