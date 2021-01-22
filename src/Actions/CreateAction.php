<?php

namespace Filament\Actions;

use Filament\Action;
use Filament\Traits\WithNotifications;

abstract class CreateAction extends Action
{
    use WithNotifications;

    public $record = [];

    protected $rules = [];

    public function fields()
    {
        return [];
    }

    public function rules()
    {
        return [];
    }

    public function submit()
    {
        $this->validate();

        static::$model::create($this->record);

        $this->notify('Created!');
    }

    public function render()
    {
        return view('filament::actions.create')
            ->layout('filament::layouts.app', ['title' => static::getTitle()]);
    }
}
