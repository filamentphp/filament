<?php

namespace Alpine\Console\Commands;

use Illuminate\Console\Command;
use Alpine\Traits\ConsoleValidation;
use Alpine\Contracts\User as UserContract;

class CreateUserCommand extends Command
{
    use ConsoleValidation;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alpine:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a user.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->validateInput(function() {
            return $this->ask('Name');
        }, ['name', 'required|string']);

        $email = $this->validateInput(function() {
            return $this->ask('Email');
        }, ['email','required|email|unique:users']);

        $password = $this->validateInput(function() {
            return $this->secret('Password');
        }, ['password', 'required|min:8']);

        $userClass = app(UserContract::class);
        $user = $userClass::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $appName = config('alpine.name');
        $loginURL = route('alpine.auth.login');
        $this->info("Success! You may now login to {$appName} ( {$loginURL} ) with user `{$user->name}`.");
    }
}
