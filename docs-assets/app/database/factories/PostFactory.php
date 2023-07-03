<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
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
            'title' => $this->faker->words(asText: true),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['draft', 'reviewing', 'published']),
            'is_featured' => $this->faker->boolean(),
            'author_id' => User::factory(),
            'rating' => $this->faker->numberBetween(1, 10),
        ];
    }
}
