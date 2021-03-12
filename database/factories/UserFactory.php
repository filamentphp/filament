<?php

namespace Filament\Database\Factories;

use Filament\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'avatar' => null,
            'email' => $this->faker->unique()->safeEmail,
            'is_admin' => true,
            'name' => $this->faker->name,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'roles' => [],
        ];
    }
}
