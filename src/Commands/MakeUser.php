<?php

namespace Filament\Commands;

use Filament\Models\FilamentUser;
use Filament\Traits\ConsoleValidation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeUser extends Command
{
    use ConsoleValidation;

    protected $description = 'Creates a Filament user.';

    protected $signature = 'make:filament-user';

    public function handle()
    {
        $name = $this->validateInput(fn () => $this->ask('Name'), 'name', ['required']);

        $email = $this->validateInput(fn () => $this->ask('Email'), 'email', ['required', 'email', 'unique:filament_users']);

        $password = $this->validateInput(fn () => $this->secret('Password'), 'password', ['required', 'min:8']);

        $user = FilamentUser::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $loginUrl = route('filament.auth.login');
        $this->info("Success! {$user->email} may now log in at {$loginUrl}.");
    }
}
