<?php

namespace Filament\Http\Livewire;

use Filament\Filament;
use Filament\Forms\HasForm;
use Filament\Resources\Forms\Form;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Hash;

class EditAccount extends Page
{
    use HasForm;

    public $record;

    public static $title = 'filament::edit-account.title';

    public static $view = 'filament::edit-account';

    public function getForm()
    {
        return static::getResource()::form(
            Form::make()
                ->context(static::class)
                ->model(static::getModel())
                ->record($this->record)
                ->submitMethod('save'),
        );
    }

    public static function getResource()
    {
        return Filament::userResource();
    }

    public function mount()
    {
        $this->record = Filament::auth()->user();
    }

    public function save()
    {
        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        unset($this->record->passwordConfirmation);

        if ($this->record->password) {
            $this->record->password = Hash::make($this->record->password);
        } else {
            unset($this->record->password);
        }

        $this->record->save();

        $this->record->password = null;
        $this->record->passwordConfirmation = null;

        $this->notify(__('filament::edit-account.messages.saved'));
    }
}
