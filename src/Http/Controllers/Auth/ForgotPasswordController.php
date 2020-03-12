<?php

namespace Alpine\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Alpine\Http\Controllers\Controller;
use Alpine\Http\Forms\PasswordEmailForm;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        $title = __('Reset Password');
        
        $form = $this->form(PasswordEmailForm::class, [
            'method' => 'POST',
            'url' => route('alpine.auth.password.email'),
        ]);

        return view('alpine::auth.passwords.email', compact('title', 'form'));
    }
}
