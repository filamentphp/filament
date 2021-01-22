<?php

namespace Filament\Actions;

use Filament\Action;
use Filament\Traits\WithNotifications;

abstract class EditAction extends Action
{
    use WithNotifications;

    public $record;

    public function fields()
    {
        return [];
    }

    public function mount($record)
    {
        $this->record = static::$model::findOrFail($record);
    }

    public function rules()
    {
        return [];
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
