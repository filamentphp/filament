<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Fixtures\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        return [
            'url' => $this->faker->imageUrl(),
            'alt_text' => $this->faker->sentence(),
        ];
    }
}
