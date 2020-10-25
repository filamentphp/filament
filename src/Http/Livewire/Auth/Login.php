<?php

namespace Filament\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Filament\Traits\ThrottlesLogins;

class Login extends Component
{
    use ThrottlesLogins;

    public $email;
    public $password;
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ];

    public function login(Request $request)
    {
        $data = $this->validate();

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if (Auth::attempt($data, (bool) $this->remember)) {
            return redirect()->intended(route('filament.dashboard'));
        }

        $this->incrementLoginAttempts($request);

        $this->addError('email', __('auth.failed'));
    }
    
    public function render()
    {
        return view('filament::livewire.auth.login')
            ->layout('filament::layouts.auth', ['title' => __('filament::auth.signin')]);
    }
}