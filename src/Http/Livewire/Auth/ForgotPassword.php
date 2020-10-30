<?php

namespace Filament\Http\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public $message;
    public $email;

    protected $rules = [
        'email' => 'required|email',
    ];

    public function sendEmail()
    {       
        $this->validate();
        $status = Password::sendResetLink(['email' => $this->email]);
        if ($status === Password::RESET_LINK_SENT) {
            $this->message = __($status);
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('filament::livewire.auth.forgot-password')
            ->layout('filament::layouts.auth', ['title' => __('Reset Password')]);
    }
}