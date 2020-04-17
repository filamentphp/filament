<?php

namespace Filament\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
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
            Field::make('email', false, 'email')
                ->placeholder('E-mail Address')
                ->input('email')
                ->value('')
                ->rules(['required'])
                ->help('<a href="'.route('filament.auth.login').'">&larr; '.__('Back to Login').'</a>'),
        ];

        return view('filament::auth.passwords.email', compact('title', 'fields'));
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return back()->with('notification', [
            'type' => 'success',
            'message' => trans($response),
        ]);
    }
}
