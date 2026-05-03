<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Fixtures\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['English', 'Spanish', 'French', 'German', 'Italian', 'Japanese', 'Mandarin']),
        ];
    }
}
