<?php

namespace Filament\Actions;

use Filament\Action;
use Filament\Traits\WithNotifications;

abstract class CreateAction extends Action
{
    use WithNotifications;

    public $record = [];

    public function submit()
    {
        $this->validate();

        static::getModel()::create($this->record);

        $this->notify('Created!');
    }

    public function render()
    {
        return view('filament::actions.create')
            ->layout('filament::layouts.app', ['title' => static::getTitle()]);
    }
}
