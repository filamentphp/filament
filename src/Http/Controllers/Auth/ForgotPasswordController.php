<?php

namespace Filament\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Filament\Http\Controllers\Controller;
use Filament\Support\Fields\Field;

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

        $fields = [
            Field::make(false, 'email', false)
                ->placeholder('E-mail Address')
                ->input('email')
                ->value('')
                ->rules(['required'])
                ->help('<a href="'.route('filament.auth.login').'">&larr; '.__('Back to Login').'</a>'),
        ];

        return view('filament::auth.passwords.email', compact('title', 'fields'));
    }
}
