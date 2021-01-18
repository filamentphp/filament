<?php

namespace Filament\Http\Livewire;

use Filament\Fields\{Avatar, Fieldset, Layout, Tabs, Text,};
use Filament\Filament;
use Filament\Traits\WithNotifications;
use Illuminate\Support\Facades\{Auth, Hash,};
use Illuminate\Validation\Rule;
use Livewire\{Component, WithFileUploads,};

class Profile extends Component
{
    use WithFileUploads, WithNotifications;

    public $avatar;

    public $password;

    public $password_confirmation;

    public $user;

    public function deleteAvatar()
    {
        $avatar = $this->user->avatar;

        if ($avatar) {
            Filament::storage()->delete($avatar);
            $this->avatar = null;

            $this->user->avatar = null;
            $this->user->save();

            $this->notify(__('Avatar removed for :name', ['name' => $this->user->name]));
        }
    }

    public function fields()
    {
        return [
            Tabs::make('Profile')
                ->tab('Account', array_merge_recursive([
                    Layout::make('grid grid-cols-1 lg:grid-cols-2 gap-6')
                        ->fields([
                            Text::make('user.name')
                                ->label('Name')
                                ->extraAttributes([
                                    'required' => 'true',
                                ]),
                            Text::make('user.email')
                                ->type('email')
                                ->label('E-Mail Address')
                                ->modelDirective('wire:model.lazy')
                                ->extraAttributes([
                                    'required' => 'true',
                                    'autocomplete' => 'email',
                                ]),
                        ]),
                    Avatar::make('avatar')
                        ->label('User Photo')
                        ->avatar($this->avatar)
                        ->user($this->user)
                        ->deleteMethod('deleteAvatar'),
                    Fieldset::make('Update Password')
                        ->fields([
                            Text::make('password')
                                ->type('password')
                                ->label('Password')
                                ->extraAttributes([
                                    'autocomplete' => 'new-password',
                                ])
                                ->hint(__('Optional'))
                                ->help('Leave blank to keep current password.'),
                            Text::make('password_confirmation')
                                ->type('password')
                                ->label('Confirm New Password')
                                ->extraAttributes([
                                    'autocomplete' => 'new-password',
                                ])
                                ->hint(__('Optional')),
                        ])
                        ->class('grid grid-cols-1 lg:grid-cols-2 gap-6'),
                ], $this->accountFields())),
        ];
    }

    public function accountFields()
    {
        return [];
    }

    public function mount()
    {
        $this->user = Auth::user();
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

        $this->save();

        $this->user->save();

        $this->notify(__('Profile saved!'));
    }

    public function save()
    {
        return;
    }

    public function updatedAvatar($value)
    {
        $this->validate([
            'avatar' => $this->rules()['avatar'],
        ]);
    }

    public function rules()
    {
        return [
            'user.name' => 'required|string|min:2|max:255',
            'user.email' => [
                'required',
                'string',
                'email',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'avatar' => 'nullable|image|max:1024',
            'password' => 'nullable|string|required_with:password_confirmation|min:6|confirmed',
            'password_confirmation' => 'nullable|string|same:password',
        ];
    }

    public function render()
    {
        return view('filament::livewire.profile')
            ->layout('filament::layouts.app', ['title' => __('Profile')]);;
    }
}
