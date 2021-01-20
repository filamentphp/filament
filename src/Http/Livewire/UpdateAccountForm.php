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

    public $avatar;

    public $password;

    public $passwordConfirmation;

    public $user;

    public function deleteAvatar()
    {
        $avatar = $this->user->avatar;

        if ($avatar) {
            Filament::storage()->delete($avatar);
            $this->avatar = null;

            $this->user->avatar = null;
            $this->user->save();

            $this->notify(__('filament::avatar.delete', ['name' => $this->user->name]));
        }
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
                        ->modelDirective('wire:model.lazy')
                        ->extraAttributes([
                            'required' => 'true',
                            'autocomplete' => 'email',
                        ]),
                ]),
            Avatar::make('avatar')
                ->label('filament::update-account-form.labels.userPhoto')
                ->avatar($this->avatar)
                ->user($this->user)
                ->deleteMethod('deleteAvatar'),
            Fieldset::make('filament::update-account-form.labels.updatePassword')
                ->fields([
                    Text::make('password')
                        ->type('password')
                        ->label('filament::fields.labels.password')
                        ->extraAttributes([
                            'autocomplete' => 'new-password',
                        ])
                        ->hint(__('filament::fields.hints.optional'))
                        ->help(__('filament::update-account-form.help.passwordKeep')),
                    Text::make('passwordConfirmation')
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

    public function submit()
    {
        $this->validate();

        if ($this->avatar) {
            $this->user->avatar = $this->avatar->store('avatars', config('filament.storage_disk'));
            $this->reset('avatar');
        }

        if ($this->password) {
            $this->user->password = Hash::make($this->password);
            $this->reset(['password', 'password_confirmation']);
        }

        $this->user->save();

        $this->notify(__('filament::update-account-form.updated'));
    }

    public function updatedAvatar($value)
    {
        $this->validateOnly('avatar');
    }

    public function rules()
    {
        return [
            'avatar' => 'nullable|image|max:1024',
            'password' => 'nullable|string|required_with:password_confirmation|min:6|confirmed',
            'password_confirmation' => 'nullable|string|same:password',
            'user.email' => [
                'required',
                'string',
                'email',
                Rule::unique('filament_users', 'email')->ignore($this->user->id),
            ],
            'user.name' => 'required|min:2|max:255',
        ];
    }

    public function render()
    {
        return view('filament::update-account-form')
            ->layout('filament::layouts.app', ['title' => __('filament::update-account-form.title')]);
    }
}
