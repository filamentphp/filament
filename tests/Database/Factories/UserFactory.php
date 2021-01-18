<?php

namespace Filament\Tests\Database\Factories;

use Filament\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'avatar' => null,
            'email' => $this->faker->unique()->safeEmail,
            'name' => $this->faker->name,
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
