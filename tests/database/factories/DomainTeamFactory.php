<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Models\DomainTeam;
use Illuminate\Database\Eloquent\Factories\Factory;

class DomainTeamFactory extends Factory
{
    protected $model = DomainTeam::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'domain' => fake()->domainName(),
        ];
    }
}
