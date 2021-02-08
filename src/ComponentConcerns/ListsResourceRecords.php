<?php

namespace Filament\ComponentConcerns;

trait ListsResourceRecords
{
    use HasTable;
    use UsesResource;

    public $createRoute = 'create';

    public function render()
    {
        return view('filament::actions.index', [
            'records' => static::getModel()::paginate(10),
        ])->layout('filament::layouts.app', ['title' => static::getTitle()]);
    }
}
