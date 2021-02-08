<?php

namespace Filament\ComponentConcerns;

trait CreatesResourceRecords
{
    use HasForm;
    use SendsToastNotifications;
    use UsesResource;

    public $record;

    public $showRoute = 'edit';

    public function mount()
    {
        $this->record = [];

        $this->fillWithFormDefaults();
    }

    public function submit()
    {
        $this->validate();

        $record = static::getModel()::create($this->record);

        $this->redirect($this->getResource()::route($this->showRoute, [
            'record' => $record,
        ]));
    }

    public function render()
    {
        return view('filament::actions.create')
            ->layout('filament::layouts.app', ['title' => static::getTitle()]);
    }
}
