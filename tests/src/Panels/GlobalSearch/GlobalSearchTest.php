<?php

use Filament\Facades\Filament;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Filament\GlobalSearch\Providers\Contracts\GlobalSearchProvider;
use Filament\Livewire\GlobalSearch;
use Filament\Resources\Resource;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\Users\UserResource;
use Filament\Tests\Panels\GlobalSearch\TestCase;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('search results', function (): void {
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

    it('orders resource global search results by `$globalSearchSort`', function (): void {
        User::factory()->create([
            'name' => 'Test',
        ]);

        Post::factory()->create([
            'title' => 'Test',
        ]);

        $provider = Filament::getCurrentOrDefaultPanel()->getGlobalSearchProvider();
        $results = $provider->getResults('Test');

        $categories = $results->getCategories()->keys()->all();

        expect($categories[0])->toBe('users');
        expect($categories[1])->toBe('posts');
    });
});

describe('`globalSearchResourceOptIn()`', function (): void {
    it('excludes resources without explicit `$isGloballySearchable` when `globalSearchResourceOptIn()` is enabled', function (): void {
        Filament::getCurrentOrDefaultPanel()->globalSearchResourceOptIn();

        expect(PostResource::canGloballySearch())->toBeFalse();
        expect(UserResource::canGloballySearch())->toBeFalse();
    });

    it('includes resources with explicit `$isGloballySearchable` when `globalSearchResourceOptIn()` is enabled', function (): void {
        Filament::getCurrentOrDefaultPanel()->globalSearchResourceOptIn();

        expect(OptedInGlobalSearchResource::canGloballySearch())->toBeTrue();
    });

    it('includes all resources by default without `globalSearchResourceOptIn()`', function (): void {
        expect(PostResource::canGloballySearch())->toBeTrue();
        expect(UserResource::canGloballySearch())->toBeTrue();
        expect(OptedInGlobalSearchResource::canGloballySearch())->toBeTrue();
    });

    it('does not return search results for resources without explicit `$isGloballySearchable` when `globalSearchResourceOptIn()` is enabled', function (): void {
        Filament::getCurrentOrDefaultPanel()->globalSearchResourceOptIn();

        $post = Post::factory()->create();

        livewire(GlobalSearch::class)
            ->set('search', $post->title)
            ->assertDontSee($post->title);
    });

    class OptedInGlobalSearchResource extends Resource
    {
        protected static ?string $model = Post::class;

        protected static ?string $recordTitleAttribute = 'title';

        protected static bool $isGloballySearchable = true;

        protected static ?string $slug = 'opted-in-global-search-posts';

        /**
         * @return array<string>
         */
        public static function getGloballySearchableAttributes(): array
        {
            return ['title'];
        }
    }

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
});
