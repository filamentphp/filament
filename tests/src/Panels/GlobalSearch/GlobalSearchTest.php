<?php

use Filament\Facades\Filament;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Filament\GlobalSearch\Providers\Contracts\GlobalSearchProvider;
use Filament\Livewire\GlobalSearch;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Panels\GlobalSearch\TestCase;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(GlobalSearch::class)
        ->assertSeeHtml('search');
});

it('can retrieve search results', function (): void {
    $post = Post::factory()->create();

    livewire(GlobalSearch::class)
        ->set('search', $post->title)
        ->assertDispatched('open-global-search-results')
        ->assertSee($post->title);
});

it('can retrieve limited search results', function (): void {
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
        ->assertDispatched('open-global-search-results')
        ->assertSee($posts[0]->title)
        ->assertSee($posts[1]->title)
        ->assertSee($posts[2]->title)
        ->assertDontSee($posts[3]->title);
});

it('can retrieve results via custom search provider', function (): void {
    Filament::getCurrentOrDefaultPanel()->globalSearch(CustomSearchProvider::class);

    livewire(GlobalSearch::class)
        ->set('search', 'foo')
        ->assertDispatched('open-global-search-results')
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
