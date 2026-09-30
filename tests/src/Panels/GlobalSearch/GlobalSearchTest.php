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
use Illuminate\Support\Facades\Artisan;
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

describe('SPA mode', function (): void {
    it('clears the search and closes the results after clicking a result', function (): void {
        retry(10, function (): void {
            Artisan::call('filament:assets');

            Post::query()->delete();

            $post = Post::factory()->create();
            $expectedPath = parse_url(PostResource::getUrl('view', ['record' => $post], panel: 'spa'), PHP_URL_PATH);

            $page = visit(PostResource::getUrl(panel: 'spa'))
                ->type('.fi-global-search-field input', $post->title)
                ->assertVisible('.fi-global-search-result-link');

            $page->script("window.persistedGlobalSearch = document.querySelector('.fi-global-search')");

            $page
                ->click('.fi-global-search-result-link')
                ->assertPathIs($expectedPath)
                ->assertValue('.fi-global-search-field input', '')
                ->assertMissing('.fi-global-search-results-ctn')
                ->assertScript("window.persistedGlobalSearch === document.querySelector('.fi-global-search')");
        });
    });

    it('clears the search and closes the results after selecting a result with the keyboard', function (): void {
        retry(10, function (): void {
            Artisan::call('filament:assets');

            Post::query()->delete();

            $post = Post::factory()->create();
            $expectedPath = parse_url(PostResource::getUrl('view', ['record' => $post], panel: 'spa'), PHP_URL_PATH);

            $page = visit(PostResource::getUrl(panel: 'spa'))
                ->type('.fi-global-search-field input', $post->title)
                ->assertVisible('.fi-global-search-result-link');

            $page->script("window.persistedGlobalSearch = document.querySelector('.fi-global-search')");

            $page
                ->keys('.fi-global-search-field input', 'ArrowDown')
                ->assertPresent('.fi-global-search-result-link:focus')
                ->keys('.fi-global-search-result-link', 'Enter')
                ->assertPathIs($expectedPath)
                ->assertValue('.fi-global-search-field input', '')
                ->assertMissing('.fi-global-search-results-ctn')
                ->assertScript("window.persistedGlobalSearch === document.querySelector('.fi-global-search')");
        });
    });

    it('preserves the search when navigation is canceled', function (): void {
        retry(10, function (): void {
            Artisan::call('filament:assets');

            Post::query()->delete();

            $post = Post::factory()->create();
            $expectedPath = parse_url(PostResource::getUrl(panel: 'spa'), PHP_URL_PATH);

            $page = visit(PostResource::getUrl(panel: 'spa'))
                ->type('.fi-global-search-field input', $post->title)
                ->assertVisible('.fi-global-search-result-link');

            $page->script("window.addEventListener('livewire:navigate', (event) => event.preventDefault(), { once: true })");

            $page
                ->click('.fi-global-search-result-link')
                ->assertPathIs($expectedPath)
                ->assertValue('.fi-global-search-field input', $post->title);
        });
    });

    it('does not reopen the results after the search is cleared', function (): void {
        retry(10, function (): void {
            Artisan::call('filament:assets');

            Post::query()->delete();

            $post = Post::factory()->create();

            $page = visit(PostResource::getUrl(panel: 'spa'))
                ->type('.fi-global-search-field input', $post->title)
                ->assertVisible('.fi-global-search-results-ctn');

            $page->script(<<<'JS'
                const globalSearch = document.querySelector('.fi-global-search')
                const livewireId = globalSearch.closest('[wire\\:id]').getAttribute('wire:id')

                window.dispatchEvent(new CustomEvent('livewire:navigate'))
                Livewire.find(livewireId).search = ''
                window.dispatchEvent(new CustomEvent('open-global-search-results'))
                JS);

            $page->assertMissing('.fi-global-search-results-ctn');
        });
    });

    it('has no accessibility issues in light and dark modes', function (bool $isDarkMode): void {
        retry(10, function () use ($isDarkMode): void {
            Artisan::call('filament:assets');

            Post::query()->delete();

            $post = Post::factory()->create();

            $page = visit(PostResource::getUrl(panel: 'spa'));

            if ($isDarkMode) {
                $page = $page->inDarkMode();
            }

            $page
                ->type('.fi-global-search-field input', $post->title)
                ->assertVisible('.fi-global-search-results-ctn')
                ->assertNoAccessibilityIssues();
        });
    })->with(['light' => false, 'dark' => true]);
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
