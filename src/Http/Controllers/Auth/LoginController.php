<?php

namespace Filament\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Route;
use Filament\Http\Controllers\Controller;
use Filament\Support\Fields\Field;

class LoginController extends Controller 
{
    use AuthenticatesUsers;

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

        $fields = [
            Field::make(false, 'email', false)
                ->placeholder('E-mail Address')
                ->input('email')
                ->value('')
                ->rules(['required']),
            Field::make(false, 'password', false)
                ->placeholder('Password')
                ->input('password')
                ->rules(['required'])
                ->help('<a href="'.route('filament.auth.password.forgot').'">'.__('Forgot Your Password?').'</a>'),
            Field::make('Remember me', 'remember')
                ->checkbox()
                ->value(1),
        ];

        return view('filament::auth.login', compact('title', 'fields'));
    }

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
            : redirect(route('filament.auth.login'))->with('notification', [
                'type' => 'success',
                'message' => __('filament::auth.logout'),
            ]);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {        
        $user->timestamps = false;
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->save();

        session()->flash('notification', [
            'type' => 'success',
            'message' => __('filament::auth.logged_in', ['name' => $user->name]),
        ]);
    }
}