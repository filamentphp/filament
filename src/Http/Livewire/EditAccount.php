<?php

namespace Filament\Http\Livewire;

use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Filament\Models\FilamentUser;
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
                        ->disableAutocomplete()
                        ->required(),
                    Components\TextInput::make('record.email')
                        ->email()
                        ->disableAutocomplete()
                        ->required()
                        ->unique(FilamentUser::class, 'email', true),
                ]),
                Components\Fieldset::make('Set a new password', [
                    Components\TextInput::make('newPassword')
                        ->label('Password')
                        ->password()
                        ->autocomplete('new-password')
                        ->confirmed()
                        ->minLength(8),
                    Components\TextInput::make('newPasswordConfirmation')
                        ->label('Confirm password')
                        ->password()
                        ->autocomplete('new-password')
                        ->requiredWith('newPassword'),
                ]),
                Components\FileUpload::make('record.avatar')
                    ->avatar()
                    ->directory('filament-avatars')
                    ->disk(config('filament.default_filesystem_disk')),
            ])
            ->context(static::class)
            ->record($this->record);
    }

    public function mount()
    {
        $this->record = Auth::guard('filament')->user();
    }

    public function submit()
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

        $this->notify(__('filament::edit-account.updated'));
    }
}
