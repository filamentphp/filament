<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Models\Team;
use Filament\Tests\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
        ];
    }
}
