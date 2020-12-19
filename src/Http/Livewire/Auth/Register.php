<?php

namespace Filament\Http\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Livewire\Component;
use Filament\Facades\Filament;
use Filament\Fields\Text;

class Register extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    protected $rules = [
        'name' => 'required|string|min:2|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'password_confirmation' => 'required|string|same:password',
    ];

    public function updatedEmail($value): void
    {
        $this->validateOnly('email', ['email' => 'unique:users']);
    }

    /**
     * @return array
     *
     * @psalm-return array{0: mixed, 1: mixed, 2: mixed, 3: mixed}
     */
    public function fields(): array
    {
        return [
            Text::make('name')
                ->label('Name')
                ->extraAttributes([
                    'required' => 'true',
                    'autofocus' => 'true',
                ])
                ->hint('['.__('Back to login').']('.route('filament.login').')'),
            Text::make('email')
                ->type('email')
                ->label('E-Mail Address')
                ->modelDirective('wire:model.lazy')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'email',
                ]),
            Text::make('password')
                ->type('password')
                ->label('Password')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'new-password',
                ]),
            Text::make('password_confirmation')
                ->type('password')
                ->label('Confirm New Password')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'new-password',
                ]),
        ];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit()
    {
        $this->validate();
        
        $user = app('Filament\User')::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);
        
        event(new Registered($user));
        Auth::login($user);

        return redirect()->to(Filament::home());
    }

    public function render(): \Illuminate\View\View
    {
        return view('filament::livewire.auth.register')
            ->layout('filament::layouts.auth', ['title' => __('filament::auth.register')]);
    }
}