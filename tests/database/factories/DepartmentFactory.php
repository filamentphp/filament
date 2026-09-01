<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Fixtures\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
        ];
    }
}
