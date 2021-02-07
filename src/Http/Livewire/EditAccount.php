<?php

namespace Filament\Http\Livewire;

use Filament\Action;
use Filament\Fields;
use Filament\Models\FilamentUser;
use Filament\View\Components\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditAccount extends Action
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
                    ->disableAutocomplete()
                    ->required(),
                Fields\Text::make('record.email')
                    ->label('Email')
                    ->email()
                    ->disableAutocomplete()
                    ->required()
                    ->unique(FilamentUser::class, 'email', true),
            ])
                ->columns(2),
            Fields\Fieldset::make('Set a new password')->fields([
                Fields\Text::make('newPassword')
                    ->label('Password')
                    ->password()
                    ->autocomplete('new-password')
                    ->confirmed()
                    ->minLength(8),
                Fields\Text::make('newPasswordConfirmation')
                    ->label('Confirm password')
                    ->password()
                    ->autocomplete('new-password')
                    ->requiredWith('newPassword'),
            ])
                ->columns(2),
        ];
    }

    public function getForm()
    {
        return new Form($this->fields(), static::class, $this->record);
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

        $this->notify(__('filament::edit-account.updated'));
    }

    public function render()
    {
        return view('filament::edit-account')
            ->layout('filament::layouts.app', ['title' => 'filament::edit-account.title']);
    }
}
