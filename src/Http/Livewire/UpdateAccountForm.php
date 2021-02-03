<?php

namespace Filament\Http\Livewire;

use Filament\Action;
use Filament\Fields;
use Filament\Filament;
use Filament\Traits\WithNotifications;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;

class UpdateAccountForm extends Action
{
    use WithFileUploads, WithNotifications;

    public $newAvatar;

    public $newPassword;

    public $newPasswordConfirmation;

    public $record;

    public function deleteAvatar()
    {
        if (! $this->record->avatar) return;

        Filament::storage()->delete($this->record->avatar);

        $this->reset('newAvatar');

        $this->record->avatar = null;
        $this->record->save();

        $this->notify(__('filament::avatar.delete', ['name' => $this->record->name]));
    }

    public function fields()
    {
        return [
            Fields\Fieldset::make()->fields([
                Fields\Text::make('record.name')
                    ->label('Name')
                    ->required(),
                Fields\Text::make('record.email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique('users', 'email', true),
            ])
                ->columns(2),
            Fields\Avatar::make('newAvatar')
                ->label('User photo')
                ->avatar($this->newAvatar)
                ->user($this->record)
                ->deleteMethod('deleteAvatar')
                ->maxSize(1024),
            Fields\Fieldset::make('Set a new password')->fields([
                Fields\Text::make('newPassword')
                    ->label('Password')
                    ->password()
                    ->confirmed()
                    ->minLength(8),
                Fields\Text::make('newPasswordConfirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->requiredWith('newPassword'),
            ])
                ->columns(2),
        ];
    }

    public function mount()
    {
        $this->record = Auth::guard('filament')->user();
    }

    public function submit()
    {
        $this->validate();

        $this->callHooks('submit');

        if ($this->newAvatar) {
            $this->record->avatar = $this->newAvatar->store('avatars', config('filament.storage_disk'));

            $this->reset('newAvatar');
        }

        if ($this->newPassword) {
            $this->record->password = Hash::make($this->newPassword);

            $this->reset(['newPassword', 'newPasswordConfirmation']);
        }

        $this->record->save();

        $this->notify(__('filament::update-account-form.updated'));
    }

    public function updatedNewAvatar($value)
    {
        $this->validateOnly('newAvatar');
    }

    public function render()
    {
        return view('filament::update-account-form')
            ->layout('filament::layouts.app', ['title' => 'filament::update-account-form.title']);
    }
}
