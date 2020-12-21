<?php

namespace Filament\Http\Livewire\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    RateLimiter,
    Auth,
    Route,
};
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Lockout;
use Livewire\Component;
use Filament\Facades\Filament;
use Filament\Fields\{
    Text,
    Checkbox,
};

class Login extends Component
{
    public $message;
    public $email;
    public $password;
    public $remember = false;
    
    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ];

    /**
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function mount()
    {
        if (Auth::check()) {
            return redirect()->to(Filament::home());
        }

        $this->message = session('message');
    }

    /**
     * @return array
     *
     * @psalm-return array{0: mixed, 1: mixed, 2: mixed}
     */
    public function fields(): array
    {
        return [
            Text::make('email')
                ->type('email')
                ->label('E-Mail Address')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'email',
                    'autofocus' => 'true',
                ])
                ->hint(Route::has('filament.register') 
                    ? '['.__('filament::auth.register').']('.route('filament.register').')' 
                    : null
                ),
            Text::make('password')
                ->type('password')
                ->label('Password')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'current-password',
                ])
                ->hint('['.__('Forgot Your Password?').']('.route('filament.password.forgot').')'),
            Checkbox::make('remember')
                ->label('Remember me'),
        ];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function submit(Request $request)
    {
        $this->ensureIsNotRateLimited($request);

        $data = $this->validate();

        if (Auth::attempt($data, (bool) $this->remember)) {
            RateLimiter::clear($this->throttleKey());

            return redirect()->intended(Filament::home());
        }

        RateLimiter::hit($this->throttleKey());

        $this->addError('email', __('auth.failed'));
    }
    
    public function render(): \Illuminate\View\View
    {
        return view('filament::livewire.auth.login')
            ->layout('filament::layouts.auth', ['title' => __('filament::auth.signin')]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function ensureIsNotRateLimited(Request $request)
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
    * Get the rate limiting throttle key for the request.
    *
    * @return string
    */
    protected function throttleKey()
    {
        return Str::lower($this->email.'|'.request()->ip());
    }
}