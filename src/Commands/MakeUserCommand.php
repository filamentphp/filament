<?php

namespace Filament\Commands;

use Filament\Models\FilamentUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakeUserCommand extends Command
{
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

    protected function validateInput($callback, $field, $rules)
    {
        $input = $callback();

        $validator = Validator::make(
            [$field => $input],
            [$field => $rules],
        );

        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            $input = $this->validateInput($callback, $field, $rules);
        }

        return $input;
    }
}
