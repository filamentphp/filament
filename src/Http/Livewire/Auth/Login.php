<?php

namespace Filament\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    Route,
};
use Filament;
use Filament\Traits\ThrottlesLogins;
use Filament\Fields\{
    Text,
    Checkbox,
};

class Login extends Component
{
    use ThrottlesLogins;

    public $message;
    public $email;
    public $password;
    public $remember = false;
    
    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ];

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->to(Filament::home());
        }

        $this->message = session('message');
    }

    public function fields()
    {
        return [
            Text::make('email')
                ->type('email')
                ->label('E-Mail Address')
                ->model('email')
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
                ->model('password')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'current-password',
                ])
                ->hint('['.__('Forgot Your Password?').']('.route('filament.password.forgot').')'),
            Checkbox::make('remember')
                ->label('Remember me')
                ->model('remember'),
        ];
    }

    public function login(Request $request)
    {
        $data = $this->validate();

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if (Auth::attempt($data, (bool) $this->remember)) {
            return redirect()->intended(Filament::home());
        }

        $this->incrementLoginAttempts($request);

        $this->addError('password', __('auth.failed'));
    }
    
    public function render()
    {
        return view('filament::livewire.auth.login')
            ->layout('filament::layouts.auth', ['title' => __('filament::auth.signin')]);
    }
}