<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'content' => $this->faker->paragraph(),
            'is_published' => $this->faker->boolean(),
            'tags' => $this->faker->words(),
            'title' => $this->faker->sentence(),
            'rating' => $this->faker->numberBetween(1, 10),
        ];
    }
}
