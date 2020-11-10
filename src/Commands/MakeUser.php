<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Filament\Traits\ConsoleValidation;

class MakeUser extends Command
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

        $user = app('Filament\User')::create([
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