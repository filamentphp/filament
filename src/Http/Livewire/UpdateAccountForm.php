<?php

namespace Filament\Http\Livewire;

use Filament\Fields\Avatar;
use Filament\Fields\Fieldset;
use Filament\Fields\Layout;
use Filament\Fields\Text;
use Filament\Filament;
use Filament\Traits\WithNotifications;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateAccountForm extends Component
{
    use WithFileUploads, WithNotifications;

    public $newAvatar;

    public $newPassword;

    public $newPasswordConfirmation;

    public $user;

    public function deleteAvatar()
    {
        if (! $this->user->avatar) return;

        Filament::storage()->delete($this->user->avatar);

        $this->reset('newAvatar');

        $this->user->avatar = null;
        $this->user->save();

        $this->notify(__('filament::avatar.delete', ['name' => $this->user->name]));
    }

    public function fields()
    {
        return [
            Layout::columns(2)
                ->fields([
                    Text::make('user.name')
                        ->label('Name')
                        ->extraAttributes([
                            'required' => 'true',
                        ]),
                    Text::make('user.email')
                        ->type('email')
                        ->label('filament::fields.labels.email')
                        ->extraAttributes([
                            'autocomplete' => 'email',
                            'required' => 'true',
                        ]),
                ]),
            Avatar::make('newAvatar')
                ->label('filament::update-account-form.labels.userPhoto')
                ->avatar($this->newAvatar)
                ->user($this->user)
                ->deleteMethod('deleteAvatar'),
            Fieldset::make('filament::update-account-form.labels.updatePassword')
                ->fields([
                    Text::make('newPassword')
                        ->type('password')
                        ->label('filament::fields.labels.password')
                        ->extraAttributes([
                            'autocomplete' => 'new-password',
                        ])
                        ->hint(__('filament::fields.hints.optional'))
                        ->help(__('filament::update-account-form.help.passwordKeep')),
                    Text::make('newPasswordConfirmation')
                        ->type('password')
                        ->label('filament::fields.labels.newPassword')
                        ->extraAttributes([
                            'autocomplete' => 'new-password',
                        ])
                        ->hint(__('filament::fields.hints.optional')),
                ])
                ->columns(2),
        ];
    }

    public function mount()
    {
        $this->user = Auth::guard('filament')->user();
    }

    public function rules()
    {
        return [
            'newAvatar' => ['nullable', 'image', 'max:1024'],
            'newPassword' => ['nullable', 'min:8'],
            'newPasswordConfirmation' => ['required_with:newPassword', 'same:newPassword'],
            'user.email' => [
                'required',
                'email',
                Rule::unique('filament_users', 'email')->ignore($this->user->id),
            ],
            'user.name' => ['required'],
        ];
    }

    public function submit()
    {
        $this->validate();

        if ($this->newAvatar) {
            $this->user->avatar = $this->newAvatar->store('avatars', config('filament.storage_disk'));

            $this->reset('newAvatar');
        }

        if ($this->newPassword) {
            $this->user->password = Hash::make($this->newPassword);

            $this->reset(['newPassword', 'newPasswordConfirmation']);
        }

        $this->user->save();

        $this->notify(__('filament::update-account-form.updated'));
    }

    public function updatedNewAvatar($value)
    {
        $this->validateOnly('newAvatar');
    }

    public function render()
    {
        return view('filament::update-account-form')
            ->layout('filament::layouts.app', ['title' => __('filament::update-account-form.title')]);
    }
}
