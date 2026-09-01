<?php

namespace Filament\Tests\Database\Factories;

use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\PostMetadata;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostMetadataFactory extends Factory
{
    protected $model = PostMetadata::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'seo_title' => $this->faker->sentence(),
        ];
    }
}
