<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Models\Post;
use Filament\Tests\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'content' => fake()->paragraph(),
            'is_published' => fake()->boolean(),
            'tags' => fake()->words(),
            'title' => fake()->sentence(),
            'rating' => fake()->numberBetween(1, 10),
        ];
    }
}
