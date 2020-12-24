<?php

namespace Filament\Http\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Filament\Traits\WithNotifications;
use Filament\Fields\Text;

class ForgotPassword extends Component
{
    use WithNotifications;

    public $email;

    protected $rules = [
        'email' => 'required|string|email',
    ];

    public function submit(): void
    {       
        $this->validate();
        
        $status = Password::sendResetLink(['email' => $this->email]);
        if ($status === Password::RESET_LINK_SENT) {
            $this->notify(__($status));
        } else {
            $this->addError('email', __($status));
        }
    }

    /**
     * @return array
     *
     * @psalm-return array{0: mixed}
     */
    public function fields(): array
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
                ->hint('['.__('Back to login').']('.route('filament.login').')'),
        ];
    }

    public function render(): \Illuminate\View\View
    {
        return view('filament::livewire.auth.forgot-password')
            ->layout('filament::layouts.auth', ['title' => __('Reset Password')]);
    }
}