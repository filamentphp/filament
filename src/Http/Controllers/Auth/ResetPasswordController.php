<?php

namespace Filament\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Filament\Http\Controllers\Controller;
use Filament\Support\Fields\Field;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Named route to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectRouteName = 'filament.auth.login';

    /**
     * Where to redirect users after resetting their password.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return route($this->redirectRouteName);
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        $title = __('Reset Password');

        $fields = [
            Field::make('E-mail Address', 'email', false)
                ->input('email')
                ->value($request->email)
                ->rules(['required']),
            Field::make('Password', 'password', false)
                ->input('password')
                ->rules(['required', 'confirmed']),
            Field::make('Confirm Password', 'password_confirmation', false)
                ->input('password'),
        ];

        return view('filament::auth.passwords.reset', compact('title', 'fields', 'token'));
    }

    /**
     * Set the user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function setUserPassword($user, $password)
    {
        $user->password = $password; // Hash::make($password);
    }

}
