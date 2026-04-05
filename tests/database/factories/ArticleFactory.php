<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Fixtures\Models\Article;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'is_published' => $this->faker->boolean(),
        ];
    }
}
