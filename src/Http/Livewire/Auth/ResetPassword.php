<?php

namespace Filament\Http\Livewire\Auth;

use Filament\Fields\Text;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ResetPassword extends Component
{
    public $email;

    public $password;

    public $passwordConfirmation;

    public $token;

    public $user;

    protected $rules = [
        'email' => ['required', 'email'],
        'password' => ['required', 'min:8'],
        'passwordConfirmation' => ['required', 'same:password'],
    ];

    public function fields()
    {
        return [
            Text::make('email')
                ->type('email')
                ->label('filament::fields.labels.email')
                ->modelDirective('wire:model.lazy')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'email',
                ]),
            Text::make('password')
                ->type('password')
                ->label('filament::fields.labels.password')
                ->extraAttributes([
                    'required' => 'true',
                    'autofocus' => 'true',
                    'autocomplete' => 'new-password',
                ]),
            Text::make('passwordConfirmation')
                ->type('password')
                ->label('filament::fields.labels.newPassword')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'new-password',
                ]),
        ];
    }

    public function mount(Request $request, $token)
    {
        $this->email = $request->input('email');
        $this->token = $token;
    }

    public function submit()
    {
        $this->validate();

        $resetStatus = Password::broker('filament_users')->reset([
            'email' => $this->email,
            'password' => $this->password,
            'token' => $this->token,
        ], function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();

            $this->user = $user;
        });

        if (Password::PASSWORD_RESET !== $resetStatus) {
            $this->addError('email', __('filament::auth.' . $resetStatus));

            return;
        }

        Auth::guard('filament')->login($this->user, true);

        return redirect()->to(route('filament.dashboard'));
    }

    public function render()
    {
        return view('filament::.auth.reset-password')
            ->layout('filament::layouts.auth', ['title' => __('filament::auth.resetPassword')]);
    }
}
