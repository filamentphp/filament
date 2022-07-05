<?php

use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can render text column', function () {
    Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanRenderTableColumn('title');
});

it('can render text column with relationship', function () {
    Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanRenderTableColumn('author.name');
});
