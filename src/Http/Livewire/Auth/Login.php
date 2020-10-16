<?php

namespace Filament\Http\Livewire\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email;
    public $password;

    public function login()
    {
        $data = $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if (Auth::attempt($data)) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $this->addError('password', trans('auth.failed'));
    }

    public function render()
    {
        return view('filament::livewire.auth.login')->layout('filament::layouts.app');
    }
}