<?php

namespace Filament\Http\Livewire;

use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Filament\Models\User;
use Filament\Pages\Page;
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

    public function getForm()
    {
        return Form::make()
            ->schema([
                Components\Grid::make([
                    Components\TextInput::make('record.name')
                        ->label('filament::edit-account.form.name.label')
                        ->disableAutocomplete()
                        ->required(),
                    Components\TextInput::make('record.email')
                        ->label('filament::edit-account.form.email.label')
                        ->email()
                        ->disableAutocomplete()
                        ->required()
                        ->unique(User::class, 'email', true),
                ]),
                Components\Fieldset::make('filament::edit-account.form.newPassword.fieldset.label', [
                    Components\TextInput::make('newPassword')
                        ->label('filament::edit-account.form.newPassword.fields.newPassword.label')
                        ->password()
                        ->autocomplete('new-password')
                        ->confirmed()
                        ->minLength(8),
                    Components\TextInput::make('newPasswordConfirmation')
                        ->label('filament::edit-account.form.newPassword.fields.newPasswordConfirmation.label')
                        ->password()
                        ->autocomplete('new-password')
                        ->requiredWith('newPassword'),
                ]),
                Components\FileUpload::make('record.avatar')
                    ->label('filament::edit-account.form.avatar.label')
                    ->avatar()
                    ->directory('filament-avatars')
                    ->disk(config('filament.default_filesystem_disk')),
            ])
            ->context(static::class)
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

        if ($this->newPassword) {
            $this->record->password = Hash::make($this->newPassword);

            $this->newPassword = null;
            $this->newPasswordConfirmation = null;
        }

        $this->record->save();

        $this->notify(__('filament::edit-account.messages.saved'));
    }
}
