<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

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
