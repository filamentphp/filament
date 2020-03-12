<?php

namespace Alpine\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Alpine\Http\Controllers\Controller;

class LoginController extends Controller 
{
    use AuthenticatesUsers;

    /**
     * Named route to redirect users when successfully authenticated.
     *
     * @var string
     */
    protected $redirectRouteName = 'alpine.admin.dashboard';

    /**
     * Where to redirect users when successfully authenticated.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return route($this->redirectRouteName);
    }

    /**
     * Show the application's login form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm(Request $request)
    {
        // check if already logged in
        if ($request->user()) {
            return redirect()->route($this->redirectRouteName);
        }

        $title = 'Sign in to '.config('app.name');

        return view('alpine::auth.login', compact('title'));
    }
}