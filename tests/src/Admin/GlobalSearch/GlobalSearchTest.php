<?php

use Filament\Facades\Filament;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Filament\Http\Livewire\GlobalSearch;
use Filament\Tests\Admin\GlobalSearch\TestCase;
use Filament\Tests\Models\Post;
use Livewire\Livewire;

uses(TestCase::class);

it('can be mounted', function () {
    Livewire::test(GlobalSearch::class)
        ->assertOk();
});

it('can retrieve search results', function () {
    $post = Post::factory()->create();

    Livewire::test(GlobalSearch::class)
        ->set('searchQuery', $post->title)
        ->assertDispatchedBrowserEvent('open-global-search-results')
        ->assertSee($post->title);
});

it('can retrieve results via custom search provider', function () {
    Filament::globalSearchProvider(CustomSearchProvider::class);

    Livewire::test(GlobalSearch::class)
        ->set('searchQuery', 'foo')
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
