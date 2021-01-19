<?php

namespace Filament\Tests\Database\Factories;

use Filament\Models\FilamentUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FilamentUserFactory extends Factory
{
    protected $model = FilamentUser::class;

    public function definition()
    {
        return [
            'avatar' => null,
            'email' => $this->faker->unique()->safeEmail,
            'name' => $this->faker->name,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
}
