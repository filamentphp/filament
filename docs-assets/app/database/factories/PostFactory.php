<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(asText: true),
            'slug' => fake()->slug(),
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(['draft', 'reviewing', 'published']),
            'is_featured' => fake()->boolean(),
            'author_id' => User::factory(),
            'rating' => fake()->numberBetween(1, 10),
        ];
    }
}
