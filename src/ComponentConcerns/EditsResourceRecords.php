<?php

namespace Filament\ComponentConcerns;

trait EditsResourceRecords
{
    use HasForm;
    use SendsToastNotifications;
    use UsesResource;

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
