<?php

namespace Filament\Http\Livewire;

use Filament\Action;
use Filament\Fields;
use Filament\Models\FilamentUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdateAccountForm extends Action
{
    public $newPassword;

    public $newPasswordConfirmation;

    public $record;

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
                    ->unique(FilamentUser::class, 'email', true),
            ])
                ->columns(2),
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

        if ($this->newPassword) {
            $this->record->password = Hash::make($this->newPassword);

            $this->reset(['newPassword', 'newPasswordConfirmation']);
        }

        $this->record->save();

        $this->notify(__('filament::update-account-form.updated'));
    }

    public function render()
    {
        return view('filament::update-account-form')
            ->layout('filament::layouts.app', ['title' => 'filament::update-account-form.title']);
    }
}
