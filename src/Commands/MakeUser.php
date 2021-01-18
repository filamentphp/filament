<?php

namespace Filament\Commands;

use Filament\Models\User;
use Filament\Traits\ConsoleValidation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeUser extends Command
{
    use ConsoleValidation;

    protected $description = 'Creates a Filament user.';

    protected $signature = 'make:filament-user';

    public function handle()
    {
        $name = $this->validateInput(function () {
            return $this->ask('Name');
        }, ['name', 'required|string']);

        $email = $this->validateInput(function () {
            return $this->ask('Email');
        }, ['email', 'required|email|unique:users']);

        $password = $this->validateInput(function () {
            return $this->secret('Password');
        }, ['password', 'required|min:8']);

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        event(new Registered($user));

        $appName = config('app.name');
        $loginURL = route('filament.login');

        $this->info("Success! You may now login to {$appName} at {$loginURL} with user `{$user->name}`.");
    }
}
