<?php

use Filament\Facades\Filament;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Filament\Http\Livewire\GlobalSearch;
use Filament\Tests\Models\Post;
use Filament\Tests\Panels\GlobalSearch\TestCase;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can be mounted', function () {
    livewire(GlobalSearch::class)
        ->assertOk();
});

it('can retrieve search results', function () {
    $post = Post::factory()->create();

    livewire(GlobalSearch::class)
        ->set('search', $post->title)
        ->assertDispatchedBrowserEvent('open-global-search-results')
        ->assertSee($post->title);
});

it('can retrieve limited search results', function () {
    $title = Str::random();

    $posts = Post::factory()
        ->count(4)
        ->state(new Sequence(
            ['title' => "{$title} 0"],
            ['title' => "{$title} 1"],
            ['title' => "{$title} 2"],
            ['title' => "{$title} 3"],
        ))
        ->create();

    livewire(GlobalSearch::class)
        ->set('search', $title)
        ->assertDispatchedBrowserEvent('open-global-search-results')
        ->assertSee($posts[0]->title)
        ->assertSee($posts[1]->title)
        ->assertSee($posts[2]->title)
        ->assertDontSee($posts[3]->title);
});

it('can retrieve results via custom search provider', function () {
    Filament::getCurrentPanel()->globalSearch(CustomSearchProvider::class);

    livewire(GlobalSearch::class)
        ->set('search', 'foo')
        ->assertDispatchedBrowserEvent('open-global-search-results')
        ->assertSee(['foo', 'bar', 'baz']);
});

class CustomSearchProvider implements GlobalSearchProvider
{
    public function getResults(string $query): ?GlobalSearchResults
    {
        return GlobalSearchResults::make()
            ->category('foobarbaz', [
                new GlobalSearchResult(title: 'foo', url: '#', details: []),
                new GlobalSearchResult(title: 'bar', url: '#', details: []),
                new GlobalSearchResult(title: 'baz', url: '#', details: []),
            ]);
    }
}
