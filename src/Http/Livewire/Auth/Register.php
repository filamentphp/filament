<?php

namespace Filament\Http\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Livewire\Component;
use Filament;
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

    public function updatedEmail($value)
    {
        $this->validateOnly('email', ['email' => 'unique:users']);
    }

    public function fields()
    {
        return [
            Text::make('name')
                ->label('Name')
                ->model('name')
                ->extraAttributes([
                    'required' => 'true',
                    'autofocus' => 'true',
                ])
                ->hint('['.__('Back to login').']('.route('filament.login').')'),
            Text::make('email')
                ->type('email')
                ->label('E-Mail Address')
                ->model('email', 'wire:model.lazy')
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'email',
                ]),
            Text::make('password')
                ->type('password')
                ->label('Password')
                ->model('password',)
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'new-password',
                ]),
            Text::make('password_confirmation')
                ->type('password')
                ->label('Confirm New Password')
                ->model('password_confirmation',)
                ->extraAttributes([
                    'required' => 'true',
                    'autocomplete' => 'new-password',
                ]),
        ];
    }

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

    public function render()
    {
        return view('filament::livewire.auth.register')
            ->layout('filament::layouts.auth', ['title' => __('filament::auth.register')]);
    }
}