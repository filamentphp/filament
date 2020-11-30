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
    public $password;
    public $password_confirmation;

    protected $rules = [
        'user.avatar' => 'nullable|image',
        'user.name' => 'required|min:2',
        'user.email' => [
            'required', 
            'email', 
        ],
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

    public function update()
    {
        $this->validate();
        
        if ($this->password) {
            $this->user->password = Hash::make($this->password);
        }

        $this->user->save();

        $this->reset(['password', 'password_confirmation']);
    }

    public function render()
    {
        return view('filament::livewire.account');
    }
}