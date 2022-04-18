<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\SessionGuard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeUserCommand extends Command
{
    use Concerns\CanValidateInput;

    protected $description = 'Creates a Filament user.';

    protected $signature = 'make:filament-user';

    protected static function getAuthGuard(): SessionGuard
    {
        return Filament::auth();
    }

    protected static function getUserProvider(): EloquentUserProvider
    {
        return static::getAuthGuard()->getProvider();
    }

    protected static function getUserModel(): string
    {
        return static::getUserProvider()->getModel();
    }

    protected function askAttributes(): array
    {
        return [
            'name' => $this->validateInput(fn () => $this->ask('Name'), 'name', ['required']),
            'email' => $this->validateInput(fn () => $this->ask('Email address'), 'email', ['required', 'email', 'unique:'.static::getUserModel()]),
            'password' => Hash::make($this->validateInput(fn () => $this->secret('Password'), 'password', ['required', 'min:8'])),
        ];
    }

    protected function createUser()
    {
        return static::getUserModel()::create(
            $this->askAttributes()
        );
    }

    protected function successMessage($user): int
    {
        $loginUrl = route('filament.auth.login');
        $this->info("Success! {$user->email} may now log in at {$loginUrl}.");

        if (static::getUserModel()::count() === 1 && $this->confirm('Would you like to show some love by starring the repo?', true)) {
            if (PHP_OS_FAMILY === 'Darwin') {
                exec('open https://github.com/laravel-filament/filament');
            }
            if (PHP_OS_FAMILY === 'Linux') {
                exec('xdg-open https://github.com/laravel-filament/filament');
            }
            if (PHP_OS_FAMILY === 'Windows') {
                exec('start https://github.com/laravel-filament/filament');
            }

            $this->line('Thank you!');
        }

        return static::SUCCESS;
    }

    public function handle(): int
    {
        return $this->successMessage(
            $this->createUser()
        );
    }
}
