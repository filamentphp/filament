<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Filament\Support\Commands\Concerns\CanValidateInput;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

class MakeUserCommand extends Command
{
    use CanValidateInput;

    protected $description = 'Creates a Filament user.';

    protected $signature = 'make:filament-user
                            {--name= : The name of the user}
                            {--email= : A valid and unique email address}
                            {--password= : The password for the user (min. 8 characters)}';

    /**
     * @var array{'name': string | null, 'email': string | null, 'password': string | null}
     */
    protected array $options;

    /**
     * @return array{'name': string, 'email': string, 'password': string}
     */
    protected function getUserData(): array
    {
        return [
            'name' => $this->validateInput(fn () => $this->options['name'] ?? $this->ask('Name'), 'name', ['required'], fn () => $this->options['name'] = null),
            'email' => $this->validateInput(fn () => $this->options['email'] ?? $this->ask('Email address'), 'email', ['required', 'email', 'unique:' . $this->getUserModel()], fn () => $this->options['email'] = null),
            'password' => Hash::make($this->validateInput(fn () => $this->options['password'] ?? $this->secret('Password'), 'password', ['required', 'min:8'], fn () => $this->options['password'] = null)),
        ];
    }

    protected function createUser(): Authenticatable
    {
        return static::getUserModel()::create($this->getUserData());
    }

    protected function sendSuccessMessage(Authenticatable $user): void
    {
        $loginUrl = Filament::getLoginUrl();

        $this->components->info('Success! ' . ($user->getAttribute('email') ?? $user->getAttribute('username') ?? 'You') . " may now log in at {$loginUrl}.");
    }

    protected function getAuthGuard(): Guard
    {
        return Filament::auth();
    }

    protected function getUserProvider(): UserProvider
    {
        return $this->getAuthGuard()->getProvider();
    }

    protected function getUserModel(): string
    {
        /** @var EloquentUserProvider $provider */
        $provider = $this->getUserProvider();

        return $provider->getModel();
    }

    public function handle(): int
    {
        $this->options = $this->options();

        $user = $this->createUser();

        $this->sendSuccessMessage($user);

        return static::SUCCESS;
    }
}
