<?php

namespace Filament\Http\Livewire;

use Filament\Filament;
use Filament\Forms\HasForm;
use Filament\Resources\Forms\Form;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditAccount extends Page
{
    use HasForm;

    public static $title = 'filament::edit-account.title';

    public static $view = 'filament::edit-account';

    public $newPassword;

    public $newPasswordConfirmation;

    public $record;

    public static function getResource()
    {
        return Filament::userResource();
    }

    public function getForm()
    {
        return static::getResource()::form(Form::make())
            ->context(static::class)
            ->model(static::getModel())
            ->record($this->record)
            ->submitMethod('save');
    }

    public function mount()
    {
        $this->record = Auth::guard('filament')->user();
    }

    public function save()
    {
        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        if ($this->record->password) {
            $this->record->password = Hash::make($this->record->password);
        }

        unset($this->record->password);
        unset($this->record->passwordConfirmation);

        $this->record->save();

        $this->notify(__('filament::edit-account.messages.saved'));
    }
}
