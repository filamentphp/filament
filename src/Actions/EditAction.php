<?php

namespace Filament\Actions;

use Filament\Action;

abstract class EditAction extends Action
{
    public $record;

    public function mount($record)
    {
        $this->record = static::getModel()::findOrFail($record);
    }

    public function submit()
    {
        $this->validate();

        $this->record->save();

        $this->notify('Saved!');
    }

    public function render()
    {
        return view('filament::actions.edit')
            ->layout('filament::layouts.app', ['title' => static::getTitle()]);
    }
}
