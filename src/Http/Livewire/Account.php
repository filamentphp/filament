<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

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
        'avatar' => 'nullable|image|max:20',
        'password' => 'nullable',
    ];

    public function updated($user)
    {
        $this->validateOnly($user, [
            'user.email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'password' => 'nullable|required_with:password_confirmation|min:8',
            'password_confirmation' => 'nullable|same:password',
        ]);
    }

    public function submit()
    {
        $this->validate();

        if ($this->avatar) {
            dd($this->avatar);
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