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

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect(route('alpine.auth.login'))->with('alert', [
                'type' => 'success',
                'message' => trans('alpine::auth.logout'),
            ]);
    }
}