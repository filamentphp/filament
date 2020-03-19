<?php

namespace Filament\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Filament\Http\Controllers\Controller;

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

        return view('filament::auth.passwords.email', compact('title'));
    }
}
