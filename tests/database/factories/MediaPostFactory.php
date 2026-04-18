<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Fixtures\Models\MediaPost;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaPostFactory extends Factory
{
    protected $model = MediaPost::class;

    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'content' => $this->faker->paragraph(),
            'is_published' => $this->faker->boolean(),
            'tags' => json_encode($this->faker->words()),
            'title' => $this->faker->sentence(),
            'rating' => $this->faker->numberBetween(1, 10),
        ];
    }
}
