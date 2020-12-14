<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    Hash,
    Storage,
};
use Filament\Traits\WithNotifications;
use Filament\Fields\{
    Text,
    Avatar,
    Fieldset,
};

class Account extends Component
{
    use WithFileUploads, WithNotifications;
    
    public $user;
    public $avatar;
    public $password;
    public $password_confirmation;

    protected $rules = [
        'user.name' => 'required|min:2',
        'user.email' => [
            'required',
            'email',
        ],
        'avatar' => 'nullable|mimes:png,jpg,jpeg,bmp,gif|max:512',
        'password' => 'nullable|required_with:password_confirmation|min:8|confirmed',
        'password_confirmation' => 'nullable|same:password',
    ];

    public function updatedAvatar($value)
    {
        $extension = pathinfo($value->getFilename(), PATHINFO_EXTENSION);
        if (!in_array($extension, ['png', 'jpg', 'jpeg', 'bmp', 'gif'])) {
            $this->reset('avatar');
        }

        $this->validate([
            'avatar' => $this->rules['avatar'],
        ]);
    }

    public function updatedUserEmail($value)
    {
        $this->validate([
            'user.email' => [
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
        ]);
    }

    public function deleteAvatar()
    {
        $avatar = $this->user->avatar;
        
        if ($avatar) {
            Storage::disk(config('filament.storage_disk'))->delete($avatar);
            $this->avatar = null;
            
            $this->user->avatar = null;
            $this->user->save();

            $this->notify(__('Avatar removed for :name', ['name' => $this->user->name]));
        }
    }

    public function fields()
    {
        return [
            Text::make('name')
                ->label('Name')
                ->model('user.name')
                ->extraAttributes([
                    'required' => 'true',
                ]),
            Text::make('email')
                ->type('email')
                ->label('E-Mail Address')
                ->model('user.email', 'wire:model.lazy')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'email',
                ]),
            Avatar::make('avatar')
                ->label('User Photo')
                ->model('avatar')
                ->avatar($this->avatar)
                ->user($this->user)
                ->delete('deleteAvatar')
                ->hint(__('Optional')),
            Fieldset::legend('Update Password')
                ->fields([
                    Text::make('password')
                        ->type('password')
                        ->label('Password')
                        ->model('password',)
                        ->extraAttributes([
                            'autocomplete' => 'new-password',
                        ])
                        ->hint(__('Optional'))
                        ->help('Leave blank to keep current password.'),
                    Text::make('password_confirmation')
                        ->type('password')
                        ->label('Confirm New Password')
                        ->model('password_confirmation',)
                        ->extraAttributes([
                            'autocomplete' => 'new-password',
                        ])
                        ->hint(__('Optional')),
                ])
                ->class('grid grid-cols-1 gap-6 lg:grid-cols-2'),
        ];
    }

    public function submit()
    {
        $this->validate();

        if ($this->avatar) {
            $this->user->avatar = $this->avatar->store('avatars', config('filament.storage_disk'));
        }
        
        if ($this->password) {
            $this->user->password = Hash::make($this->password);
        }

        $this->user->save();

        $this->reset(['password', 'password_confirmation']);

        $this->notify(__('Account saved!'));
    }

    public function render()
    {
        return view('filament::livewire.account');
    }
}