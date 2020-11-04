<?php

namespace Filament\Http\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    Auth,
    Hash,
    Password,
};
use Livewire\Component;

class ResetPassword extends Component
{
    public $email;
    public $password;
    public $password_confirmation;
    public $token;
    public $user;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required|same:password',
    ];

    public function mount(Request $request, $token)
    {
        $this->token = $token;
        $this->email = $request->input('email');
    }

    public function resetPassword()
    {
        $this->validate();
        $status = Password::reset(
            $this->credentials(),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->setRememberToken(Str::random(60));
                event(new PasswordReset($user));
                $this->user = $user;
            }
        );
        
        if ($status === Password::PASSWORD_RESET) {
            Auth::login($this->user);
            return redirect()->route('filament.dashboard');
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('filament::livewire.auth.reset-password')
            ->layout('filament::layouts.auth', ['title' => __('Reset Password')]);
    }

    protected function credentials()
    {
        return [
            'token' => $this->token,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ];
    }
}