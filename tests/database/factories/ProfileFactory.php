<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Profile;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'bio' => $this->faker->sentence(),
        ];
    }
}
