<?php

namespace Filament\Commands;

use Filament\Filament;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeUserCommand extends Command
{
    use Concerns\CanValidateInput;

    protected $description = 'Creates a Filament user.';

    protected $signature = 'make:filament-user';

    public function handle()
    {
        $userModel = Filament::auth()->getProvider()->getModel();

        $details = [];

        $details['name'] = $this->validateInput(fn () => $this->ask('Name'), 'name', ['required']);

        $details['email'] = $this->validateInput(fn () => $this->ask('Email'), 'email', ['required', 'email', 'unique:' . $userModel]);

        $details['password'] = Hash::make($this->validateInput(fn () => $this->secret('Password'), 'password', ['required', 'min:8']));

        $adminColumn = $userModel::getFilamentAdminColumn();
        if ($adminColumn !== null) {
            $details[$adminColumn] = $this->confirm('Would you like this user to be an administrator?', true);
        }

        $userColumn = $userModel::getFilamentUserColumn();
        if ($userColumn !== null) {
            $details[$userColumn] = true;
        }

        $user = $userModel::create($details);

        $loginUrl = route('filament.auth.login');
        $this->info("Success! {$user->email} may now log in at {$loginUrl}.");

        if (Filament::auth()->getProvider()->getModel()::count() === 1 && $this->confirm('Would you like to show some love by starring the repo?', true)) {
            if (PHP_OS_FAMILY === 'Darwin') exec('open https://github.com/laravel-filament/filament');
            if (PHP_OS_FAMILY === 'Linux') exec('xdg-open https://github.com/laravel-filament/filament');
            if (PHP_OS_FAMILY === 'Windows') exec('start https://github.com/laravel-filament/filament');

            $this->line('Thank you!');
        }
    }
}
