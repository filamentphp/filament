<?php

namespace Filament\Http\Livewire;

use Filament\Components\Concerns;
use Filament\Forms\Fields;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Filament\Models\FilamentUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class EditAccount extends Component
{
    use Concerns\HasTitle;
    use Concerns\SendsToastNotifications;
    use HasForm;

    public $newPassword;

    public $newPasswordConfirmation;

    public $record;

    public function getForm()
    {
        return Form::make($this->fields())
            ->context(static::class)
            ->record($this->record);
    }

    public function fields()
    {
        return [
            Fields\Fieldset::make()->fields([
                Fields\Text::make('record.name')
                    ->disableAutocomplete()
                    ->required(),
                Fields\Text::make('record.email')
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
            Fields\File::make('record.avatar')
                ->avatar()
                ->directory('filament-avatars')
                ->disk(config('filament.default_filesystem_disk')),
        ];
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

            $this->reset(['newPassword', 'newPasswordConfirmation']);
        }

        $this->record->save();

        $this->notify(__('filament::edit-account.updated'));
    }

    public function render()
    {
        return view('filament::edit-account')
            ->layout('filament::components.layouts.app');
    }
}
