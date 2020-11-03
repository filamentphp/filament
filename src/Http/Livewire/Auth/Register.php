<?php

namespace Filament\Http\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Livewire\Component;

class Register extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    protected $rules = [
        'name' => 'required|min:2',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required|same:password',
    ];

    public function updatedEmail($value)
    {
        $this->validateOnly('email', ['email' => 'unique:users']);
    }

    public function register()
    {
        $this->validate();
        
        $user = app('Filament\User')::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);
        
        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('filament.dashboard');
    }

    public function render()
    {
        return view('filament::livewire.auth.register')
            ->layout('filament::layouts.auth', ['title' => __('filament::auth.register')]);
    }
}