<?php

namespace Filament\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Route;

use Filament\Http\Controllers\Controller;

class LoginController extends Controller 
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users when successfully authenticated.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $redirect = config('filament.redirects.admin');

        return Route::has($redirect) ? route($redirect) : $redirect;
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
            return redirect($this->redirectTo());
        }

        $title = __('filament::auth.login', ['name' => config('app.name')]);

        return view('filament::auth.login', compact('title'));
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
            : redirect(route('filament.auth.login'))->with('alert', [
                'type' => 'success',
                'message' => __('filament::auth.logout'),
            ]);
    }
}