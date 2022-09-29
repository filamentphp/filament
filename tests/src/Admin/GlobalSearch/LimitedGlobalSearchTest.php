<?php

use Filament\Http\Livewire\GlobalSearch;
use Filament\Tests\Admin\GlobalSearch\LimitedTestCase;
use Filament\Tests\Models\Post;
use Illuminate\Database\Eloquent\Factories\Sequence;
use function Pest\Livewire\livewire;

uses(LimitedTestCase::class);

it('can be mounted', function () {
    livewire(GlobalSearch::class)
        ->assertOk();
});

it('can retrieve search results', function () {
    $posts = Post::factory()->count(2)->state(new Sequence(
        ['title' => 'post title 1'],
        ['title' => 'post title 2']
    ))->create();

    livewire(GlobalSearch::class)
        ->set('search', 'post title')
        ->assertDispatchedBrowserEvent('open-global-search-results')
        ->assertSee($posts[0]->title)
        ->assertDontSee($posts[1]->title);
});
