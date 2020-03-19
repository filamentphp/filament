<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Filament\Traits\ConsoleValidation;
use Filament\Contracts\User as UserContract;

class CreateUserCommand extends Command
{
    use ConsoleValidation;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filament:user';

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

        $superAdmin = $this->confirm("Make `{$name}` a super admin?") ? true : false;

        $userClass = app(UserContract::class);
        $user = $userClass::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'is_super_admin' => $superAdmin,
        ]);

        $appName = config('filament.name');
        $loginURL = route('filament.auth.login');
        $this->info("Success! You may now login to {$appName} ( {$loginURL} ) with user `{$user->name}`.");
    }
}
