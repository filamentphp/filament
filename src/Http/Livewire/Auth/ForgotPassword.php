<?php

namespace Filament\Http\Livewire\Auth;

use Filament\Fields\Text;
use Filament\Traits\WithNotifications;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    use WithNotifications;

    public $email;

    protected $rules = [
        'email' => 'required|string|email',
    ];

    public function fields()
    {
        return [
            Text::make('email')
                ->type('email')
                ->label('E-Mail Address')
                ->extraAttributes([
                    'required' => 'true',
                    'autofocus' => 'true',
                    'autocomplete' => 'email',
                ])
                ->hint('[' . __('Back to login') . '](' . route('filament.login') . ')'),
        ];
    }

    public function submit()
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);
        if ($status === Password::RESET_LINK_SENT) {
            $this->notify(__($status));
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('filament::livewire.auth.forgot-password')
            ->layout('filament::layouts.auth', ['title' => __('Reset Password')]);
    }
}
