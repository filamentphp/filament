<?php

use Filament\Facades\Filament;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Filament\Http\Livewire\GlobalSearch;
use Filament\Tests\Admin\GlobalSearch\TestCase;
use Filament\Tests\Models\Post;
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

it('can retrieve results via custom search provider', function () {
    Filament::globalSearchProvider(CustomSearchProvider::class);

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
