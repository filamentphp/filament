<?php

namespace Filament\Commands;

use Filament\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeUserCommand extends Command
{
    use Concerns\CanValidateInput;

    protected $description = 'Creates a Filament user.';

    protected $signature = 'make:filament-user';

    public function handle()
    {
        $name = $this->validateInput(fn () => $this->ask('Name'), 'name', ['required']);

        $email = $this->validateInput(fn () => $this->ask('Email'), 'email', ['required', 'email', 'unique:filament_users']);

        $password = $this->validateInput(fn () => $this->secret('Password'), 'password', ['required', 'min:8']);

        $isAdmin = $this->confirm('Would you like this user to be an administrator?', true);

        $user = User::create([
            'is_admin' => $isAdmin,
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $loginUrl = route('filament.auth.login');
        $this->info("Success! {$user->email} may now log in at {$loginUrl}.");

        if (User::count() === 1 && $this->confirm('Would you like to show some love by starring the repo?', true)) {
            if (PHP_OS_FAMILY === 'Darwin') exec('open https://github.com/laravel-filament/filament');
            if (PHP_OS_FAMILY === 'Linux') exec('xdg-open https://github.com/laravel-filament/filament');
            if (PHP_OS_FAMILY === 'Windows') exec('start https://github.com/laravel-filament/filament');

            $this->line('Thank you!');
        }
    }
}
