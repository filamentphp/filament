<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Fixtures\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
        ];
    }
}
