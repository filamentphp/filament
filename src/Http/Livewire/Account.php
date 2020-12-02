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

class Account extends Component
{
    use WithFileUploads;
    
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
        'password' => 'nullable|required_with:password_confirmation|min:8|confirmed',
        'password_confirmation' => 'nullable|same:password',
    ];

    public function updated($user)
    {
        $this->validateOnly($user, [
            'user.email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
        ]);
    }

    public function updatedAvatar($value)
    {
        $extension = pathinfo($value->getFilename(), PATHINFO_EXTENSION);
        if (!in_array($extension, ['png', 'jpeg', 'bmp', 'gif'])) {
            $this->reset('avatar');
        }

        $this->validate([
            'avatar' => 'mimes:png,jpeg,bmp,gif|max:512', // .5MB Max
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

            $this->dispatchBrowserEvent('notify', __('Avatar removed for :name', ['name' => $this->user->name]));
        }
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

        $this->dispatchBrowserEvent('notify', __('Account saved!'));
    }

    public function render()
    {
        return view('filament::livewire.account');
    }
}