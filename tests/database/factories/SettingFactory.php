<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Fixtures\Models\Profile;
use Filament\Tests\Fixtures\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'profile_id' => Profile::factory(),
            'theme' => $this->faker->randomElement(['light', 'dark', 'auto']),
            'language' => $this->faker->randomElement(['en', 'es', 'fr', 'de']),
        ];
    }
}
