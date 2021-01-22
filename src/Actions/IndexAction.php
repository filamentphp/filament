<?php

namespace Filament\Actions;

use Filament\Action;

abstract class IndexAction extends Action
{
    public $createRoute = 'create';

    public function render()
    {
        return view('filament::actions.index', [
            'records' => static::$model::paginate(10),
        ])->layout('filament::layouts.app', ['title' => static::getTitle()]);
    }
}
