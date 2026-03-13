<?php

use Filament\Tests\Fixtures\Livewire\PostsQueryBuilderTable;
use Filament\Tests\Fixtures\Livewire\UsersQueryBuilderTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;

uses(TestCase::class);

function applyQueryBuilderFilter(array $rules)
{
    return fn ($livewire) => $livewire
        ->set('tableDeferredFilters.query_builder.rules', $rules)
        ->call('applyTableFilters');
}

it('can filter records using text constraint with contains operator', function (): void {
    $posts = Post::factory()->count(10)->create([
        'title' => 'Test Post Title',
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'title' => 'Different Title',
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'title',
                'data' => [
                    'operator' => 'contains',
                    'settings' => ['text' => 'Test Post'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($posts)
        ->assertCanNotSeeTableRecords($otherPosts);
});

it('can filter records using text constraint with does not contain operator', function (): void {
    $posts = Post::factory()->count(10)->create([
        'title' => 'Test Post Title',
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'title' => 'Different Title',
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'title',
                'data' => [
                    'operator' => 'contains.inverse',
                    'settings' => ['text' => 'Test Post'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($otherPosts)
        ->assertCanNotSeeTableRecords($posts);
});

it('can filter records using text constraint with starts with operator', function (): void {
    $posts = Post::factory()->count(10)->create([
        'title' => 'Test Post Title',
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'title' => 'Different Title',
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'title',
                'data' => [
                    'operator' => 'startsWith',
                    'settings' => ['text' => 'Test'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($posts)
        ->assertCanNotSeeTableRecords($otherPosts);
});

it('can filter records using text constraint with does not start with operator', function (): void {
    $posts = Post::factory()->count(10)->create([
        'title' => 'Test Post Title',
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'title' => 'Different Title',
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'title',
                'data' => [
                    'operator' => 'startsWith.inverse',
                    'settings' => ['text' => 'Test'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($otherPosts)
        ->assertCanNotSeeTableRecords($posts);
});

it('can filter records using text constraint with ends with operator', function (): void {
    $posts = Post::factory()->count(10)->create([
        'title' => 'Post Title Test',
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'title' => 'Different Title',
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'title',
                'data' => [
                    'operator' => 'endsWith',
                    'settings' => ['text' => 'Test'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($posts)
        ->assertCanNotSeeTableRecords($otherPosts);
});

it('can filter records using text constraint with does not end with operator', function (): void {
    $posts = Post::factory()->count(10)->create([
        'title' => 'Post Title Test',
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'title' => 'Different Title',
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'title',
                'data' => [
                    'operator' => 'endsWith.inverse',
                    'settings' => ['text' => 'Test'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($otherPosts)
        ->assertCanNotSeeTableRecords($posts);
});

it('can filter records using text constraint with equals operator', function (): void {
    $posts = Post::factory()->count(10)->create([
        'title' => 'Exact Title',
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'title' => 'Different Title',
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'title',
                'data' => [
                    'operator' => 'equals',
                    'settings' => ['text' => 'Exact Title'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($posts)
        ->assertCanNotSeeTableRecords($otherPosts);
});

it('can filter records using text constraint with does not equal operator', function (): void {
    $posts = Post::factory()->count(10)->create([
        'title' => 'Exact Title',
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'title' => 'Different Title',
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'title',
                'data' => [
                    'operator' => 'equals.inverse',
                    'settings' => ['text' => 'Exact Title'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($otherPosts)
        ->assertCanNotSeeTableRecords($posts);
});

it('can filter records using boolean constraint with is true operator', function (): void {
    $publishedPosts = Post::factory()->count(10)->create([
        'is_published' => true,
    ]);

    $unpublishedPosts = Post::factory()->count(5)->create([
        'is_published' => false,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($publishedPosts->merge($unpublishedPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'is_published',
                'data' => [
                    'operator' => 'isTrue',
                    'settings' => [],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($publishedPosts)
        ->assertCanNotSeeTableRecords($unpublishedPosts);
});

it('can filter records using boolean constraint with is false operator', function (): void {
    $publishedPosts = Post::factory()->count(10)->create([
        'is_published' => true,
    ]);

    $unpublishedPosts = Post::factory()->count(5)->create([
        'is_published' => false,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($publishedPosts->merge($unpublishedPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'is_published',
                'data' => [
                    'operator' => 'isTrue.inverse',
                    'settings' => [],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($unpublishedPosts)
        ->assertCanNotSeeTableRecords($publishedPosts);
});

it('can filter records using number constraint with minimum operator', function (): void {
    $highRatedPosts = Post::factory()->count(5)->create([
        'rating' => 8,
    ]);

    $lowRatedPosts = Post::factory()->count(5)->create([
        'rating' => 3,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($highRatedPosts->merge($lowRatedPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'rating',
                'data' => [
                    'operator' => 'isMin',
                    'settings' => ['number' => 5],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($highRatedPosts)
        ->assertCanNotSeeTableRecords($lowRatedPosts);
});

it('can filter records using number constraint with less than operator', function (): void {
    $highRatedPosts = Post::factory()->count(5)->create([
        'rating' => 8,
    ]);

    $lowRatedPosts = Post::factory()->count(5)->create([
        'rating' => 3,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($highRatedPosts->merge($lowRatedPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'rating',
                'data' => [
                    'operator' => 'isMin.inverse',
                    'settings' => ['number' => 5],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($lowRatedPosts)
        ->assertCanNotSeeTableRecords($highRatedPosts);
});

it('can filter records using number constraint with maximum operator', function (): void {
    $highRatedPosts = Post::factory()->count(5)->create([
        'rating' => 8,
    ]);

    $lowRatedPosts = Post::factory()->count(5)->create([
        'rating' => 3,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($highRatedPosts->merge($lowRatedPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'rating',
                'data' => [
                    'operator' => 'isMax',
                    'settings' => ['number' => 5],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($lowRatedPosts)
        ->assertCanNotSeeTableRecords($highRatedPosts);
});

it('can filter records using number constraint with greater than operator', function (): void {
    $highRatedPosts = Post::factory()->count(5)->create([
        'rating' => 8,
    ]);

    $lowRatedPosts = Post::factory()->count(5)->create([
        'rating' => 3,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($highRatedPosts->merge($lowRatedPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'rating',
                'data' => [
                    'operator' => 'isMax.inverse',
                    'settings' => ['number' => 5],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($highRatedPosts)
        ->assertCanNotSeeTableRecords($lowRatedPosts);
});

it('can filter records using number constraint with equals operator', function (): void {
    $targetPosts = Post::factory()->count(5)->create([
        'rating' => 5,
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'rating' => 3,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($targetPosts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'rating',
                'data' => [
                    'operator' => 'equals',
                    'settings' => ['number' => 5],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($targetPosts)
        ->assertCanNotSeeTableRecords($otherPosts);
});

it('can filter records using number constraint with does not equal operator', function (): void {
    $targetPosts = Post::factory()->count(5)->create([
        'rating' => 5,
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'rating' => 3,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($targetPosts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'rating',
                'data' => [
                    'operator' => 'equals.inverse',
                    'settings' => ['number' => 5],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($otherPosts)
        ->assertCanNotSeeTableRecords($targetPosts);
});

it('can filter records using integer number constraint', function (): void {
    $integerRatedPosts = Post::factory()->count(5)->create([
        'rating' => 5,
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'rating' => 3,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($integerRatedPosts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'rating_integer',
                'data' => [
                    'operator' => 'equals',
                    'settings' => ['number' => 5],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($integerRatedPosts)
        ->assertCanNotSeeTableRecords($otherPosts);
});

it('can filter records using date constraint with is after operator', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(5),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => ['date' => now()->toDateString()],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with is before operator', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(5),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => ['date' => now()->toDateString()],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($oldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

it('can filter records using date constraint with is date operator', function (): void {
    $targetDate = now()->startOfDay();

    $targetPosts = Post::factory()->count(5)->create([
        'created_at' => $targetDate,
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'created_at' => $targetDate->copy()->addDays(1),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($targetPosts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isDate',
                    'settings' => ['date' => $targetDate->toDateString()],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($targetPosts)
        ->assertCanNotSeeTableRecords($otherPosts);
});

it('can filter records using select constraint', function (): void {
    $targetPosts = Post::factory()->count(5)->create([
        'rating' => 3,
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'rating' => 5,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($targetPosts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'rating_select',
                'data' => [
                    'operator' => 'is',
                    'settings' => ['value' => 3],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($targetPosts)
        ->assertCanNotSeeTableRecords($otherPosts);
});

it('can filter records using select constraint with is not operator', function (): void {
    $targetPosts = Post::factory()->count(5)->create([
        'rating' => 3,
    ]);

    $otherPosts = Post::factory()->count(5)->create([
        'rating' => 5,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($targetPosts->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'rating_select',
                'data' => [
                    'operator' => 'is.inverse',
                    'settings' => ['value' => 3],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($otherPosts)
        ->assertCanNotSeeTableRecords($targetPosts);
});

it('can filter records using multiple select constraint', function (): void {
    $posts3or5 = Post::factory()->count(5)->create([
        'rating' => 3,
    ])->merge(Post::factory()->count(3)->create([
        'rating' => 5,
    ]));

    $otherPosts = Post::factory()->count(5)->create([
        'rating' => 1,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts3or5->merge($otherPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'rating_select_multiple',
                'data' => [
                    'operator' => 'is',
                    'settings' => ['values' => [3, 5]],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($posts3or5)
        ->assertCanNotSeeTableRecords($otherPosts);
});

it('can filter records using relationship constraint with text operator', function (): void {
    $author = User::factory()->create(['name' => 'John Doe']);
    Post::factory()->count(5)->create(['author_id' => $author->id]);
    Post::factory()->count(5)->create();

    $allPosts = Post::with('author')->get();
    $matchingPosts = $allPosts->filter(fn ($post) => str_contains($post->author->name ?? '', 'John'));
    $nonMatchingPosts = $allPosts->reject(fn ($post) => str_contains($post->author->name ?? '', 'John'));

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($allPosts)
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author_name',
                'data' => [
                    'operator' => 'contains',
                    'settings' => ['text' => 'John'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can filter records using relationship constraint with is related to operator', function (): void {
    $author = User::factory()->create(['name' => 'John Doe']);
    Post::factory()->count(5)->create(['author_id' => $author->id]);
    Post::factory()->count(5)->create();

    $allPosts = Post::all();
    $matchingPosts = $allPosts->filter(fn ($post) => $post->author_id === $author->id);
    $nonMatchingPosts = $allPosts->reject(fn ($post) => $post->author_id === $author->id);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($allPosts)
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author',
                'data' => [
                    'operator' => 'isRelatedTo',
                    'settings' => ['value' => $author->id],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can filter records using relationship constraint with is not related to operator', function (): void {
    $author = User::factory()->create(['name' => 'John Doe']);
    Post::factory()->count(5)->create(['author_id' => $author->id]);
    Post::factory()->count(5)->create();

    $allPosts = Post::all();
    $matchingPosts = $allPosts->filter(fn ($post) => $post->author_id !== $author->id);
    $nonMatchingPosts = $allPosts->reject(fn ($post) => $post->author_id !== $author->id);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($allPosts)
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author',
                'data' => [
                    'operator' => 'isRelatedTo.inverse',
                    'settings' => ['value' => $author->id],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can filter records using nullable constraint with is filled operator', function (): void {
    $filledPosts = Post::factory()->count(5)->create([
        'content' => 'Some content here',
    ]);

    $emptyPosts = Post::factory()->count(5)->create([
        'content' => null,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($filledPosts->merge($emptyPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'content',
                'data' => [
                    'operator' => 'isFilled',
                    'settings' => [],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($filledPosts)
        ->assertCanNotSeeTableRecords($emptyPosts);
});

it('can filter records using nullable constraint with is blank operator', function (): void {
    $filledPosts = Post::factory()->count(5)->create([
        'content' => 'Some content here',
    ]);

    $emptyPosts = Post::factory()->count(5)->create([
        'content' => null,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($filledPosts->merge($emptyPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'content',
                'data' => [
                    'operator' => 'isFilled.inverse',
                    'settings' => [],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($emptyPosts)
        ->assertCanNotSeeTableRecords($filledPosts);
});

it('can filter records using complex nested AND rules', function (): void {
    $matchingPosts = Post::factory()->count(3)->create([
        'title' => 'Test Post',
        'rating' => 8,
        'is_published' => true,
    ]);

    $nonMatchingPosts = Post::factory()->count(5)->create([
        'title' => 'Different Title',
        'rating' => 3,
        'is_published' => false,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($matchingPosts->merge($nonMatchingPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'title',
                'data' => [
                    'operator' => 'contains',
                    'settings' => ['text' => 'Test'],
                ],
            ],
            [
                'type' => 'rating',
                'data' => [
                    'operator' => 'isMin',
                    'settings' => ['number' => 5],
                ],
            ],
            [
                'type' => 'is_published',
                'data' => [
                    'operator' => 'isTrue',
                    'settings' => [],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can filter records using OR block rules', function (): void {
    $highRatedPosts = Post::factory()->count(3)->create([
        'title' => 'High Rated',
        'rating' => 9,
        'is_published' => false,
    ]);

    $publishedPosts = Post::factory()->count(3)->create([
        'title' => 'Published',
        'rating' => 3,
        'is_published' => true,
    ]);

    $nonMatchingPosts = Post::factory()->count(5)->create([
        'title' => 'Low Rated',
        'rating' => 2,
        'is_published' => false,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($highRatedPosts->merge($publishedPosts)->merge($nonMatchingPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'or',
                'data' => [
                    'groups' => [
                        [
                            'rules' => [
                                [
                                    'type' => 'rating',
                                    'data' => [
                                        'operator' => 'isMin',
                                        'settings' => ['number' => 8],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'rules' => [
                                [
                                    'type' => 'is_published',
                                    'data' => [
                                        'operator' => 'isTrue',
                                        'settings' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($highRatedPosts->merge($publishedPosts))
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can filter records using deeply nested OR and AND rules', function (): void {
    // Matching: (title contains "Premium" AND rating >= 8) OR (is_published = true AND rating >= 5)
    $premiumHighRated = Post::factory()->count(2)->create([
        'title' => 'Premium Product',
        'rating' => 9,
        'is_published' => false,
    ]);

    $publishedMediumRated = Post::factory()->count(2)->create([
        'title' => 'Standard Product',
        'rating' => 6,
        'is_published' => true,
    ]);

    $nonMatchingPosts = Post::factory()->count(5)->create([
        'title' => 'Basic Product',
        'rating' => 3,
        'is_published' => false,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($premiumHighRated->merge($publishedMediumRated)->merge($nonMatchingPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'or',
                'data' => [
                    'groups' => [
                        [
                            'rules' => [
                                [
                                    'type' => 'title',
                                    'data' => [
                                        'operator' => 'contains',
                                        'settings' => ['text' => 'Premium'],
                                    ],
                                ],
                                [
                                    'type' => 'rating',
                                    'data' => [
                                        'operator' => 'isMin',
                                        'settings' => ['number' => 8],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'rules' => [
                                [
                                    'type' => 'is_published',
                                    'data' => [
                                        'operator' => 'isTrue',
                                        'settings' => [],
                                    ],
                                ],
                                [
                                    'type' => 'rating',
                                    'data' => [
                                        'operator' => 'isMin',
                                        'settings' => ['number' => 5],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($premiumHighRated->merge($publishedMediumRated))
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can filter records using multiple constraints with different data types', function (): void {
    $targetDate = now()->subDays(2);
    $author = User::factory()->create(['name' => 'Alice Smith']);

    $matchingPosts = Post::factory()->count(3)->create([
        'title' => 'Featured Article',
        'rating' => 7,
        'is_published' => true,
        'created_at' => $targetDate,
        'author_id' => $author->id,
    ]);

    $nonMatchingPosts = Post::factory()->count(5)->create([
        'title' => 'Draft Article',
        'rating' => 2,
        'is_published' => false,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($matchingPosts->merge($nonMatchingPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'title',
                'data' => [
                    'operator' => 'contains',
                    'settings' => ['text' => 'Featured'],
                ],
            ],
            [
                'type' => 'rating',
                'data' => [
                    'operator' => 'isMin',
                    'settings' => ['number' => 5],
                ],
            ],
            [
                'type' => 'is_published',
                'data' => [
                    'operator' => 'isTrue',
                    'settings' => [],
                ],
            ],
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => ['date' => now()->subDays(1)->toDateString()],
                ],
            ],
            [
                'type' => 'author_name',
                'data' => [
                    'operator' => 'contains',
                    'settings' => ['text' => 'Alice'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can combine multiple OR blocks with AND conditions', function (): void {
    // Must be published AND ((rating >= 8) OR (title contains "Featured"))
    $publishedHighRated = Post::factory()->count(2)->create([
        'title' => 'Regular Post',
        'rating' => 9,
        'is_published' => true,
    ]);

    $publishedFeatured = Post::factory()->count(2)->create([
        'title' => 'Featured Content',
        'rating' => 5,
        'is_published' => true,
    ]);

    $unpublishedHighRated = Post::factory()->count(2)->create([
        'title' => 'Regular Post',
        'rating' => 9,
        'is_published' => false,
    ]);

    $unpublishedFeatured = Post::factory()->count(2)->create([
        'title' => 'Featured Content',
        'rating' => 5,
        'is_published' => false,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords(
            $publishedHighRated
                ->merge($publishedFeatured)
                ->merge($unpublishedHighRated)
                ->merge($unpublishedFeatured)
        )
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'is_published',
                'data' => [
                    'operator' => 'isTrue',
                    'settings' => [],
                ],
            ],
            [
                'type' => 'or',
                'data' => [
                    'groups' => [
                        [
                            'rules' => [
                                [
                                    'type' => 'rating',
                                    'data' => [
                                        'operator' => 'isMin',
                                        'settings' => ['number' => 8],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'rules' => [
                                [
                                    'type' => 'title',
                                    'data' => [
                                        'operator' => 'contains',
                                        'settings' => ['text' => 'Featured'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($publishedHighRated->merge($publishedFeatured))
        ->assertCanNotSeeTableRecords($unpublishedHighRated->merge($unpublishedFeatured));
});

it('can filter records using text constraint with relationship method', function (): void {
    $matchingAuthor = User::factory()->create(['email' => 'john@example.com']);
    $matchingPosts = Post::factory()->count(5)->create(['author_id' => $matchingAuthor->id]);

    $nonMatchingAuthor = User::factory()->create(['email' => 'jane@different.com']);
    $nonMatchingPosts = Post::factory()->count(5)->create(['author_id' => $nonMatchingAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($matchingPosts->merge($nonMatchingPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author.email',
                'data' => [
                    'operator' => 'contains',
                    'settings' => ['text' => 'example.com'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can filter records using boolean constraint with relationship method', function (): void {
    $activeAuthor = User::factory()->create(['has_email_authentication' => true]);
    $activePosts = Post::factory()->count(5)->create(['author_id' => $activeAuthor->id]);

    $inactiveAuthor = User::factory()->create(['has_email_authentication' => false]);
    $inactivePosts = Post::factory()->count(5)->create(['author_id' => $inactiveAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($activePosts->merge($inactivePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author.has_email_authentication',
                'data' => [
                    'operator' => 'isTrue',
                    'settings' => [],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($activePosts)
        ->assertCanNotSeeTableRecords($inactivePosts);
});

it('can filter records using boolean constraint with relationship method and inverse operator', function (): void {
    $activeAuthor = User::factory()->create(['has_email_authentication' => true]);
    $activePosts = Post::factory()->count(5)->create(['author_id' => $activeAuthor->id]);

    $inactiveAuthor = User::factory()->create(['has_email_authentication' => false]);
    $inactivePosts = Post::factory()->count(5)->create(['author_id' => $inactiveAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($activePosts->merge($inactivePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author.has_email_authentication',
                'data' => [
                    'operator' => 'isTrue.inverse',
                    'settings' => [],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($inactivePosts)
        ->assertCanNotSeeTableRecords($activePosts);
});

it('can filter records using number constraint with relationship method', function (): void {
    $highScoreAuthor = User::factory()->create(['score' => 95]);
    $highScorePosts = Post::factory()->count(5)->create(['author_id' => $highScoreAuthor->id]);

    $lowScoreAuthor = User::factory()->create(['score' => 45]);
    $lowScorePosts = Post::factory()->count(5)->create(['author_id' => $lowScoreAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($highScorePosts->merge($lowScorePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author.score',
                'data' => [
                    'operator' => 'isMin',
                    'settings' => ['number' => 80, 'aggregate' => null],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($highScorePosts)
        ->assertCanNotSeeTableRecords($lowScorePosts);
});

it('can filter records using number constraint with relationship method and inverse operator', function (): void {
    $highScoreAuthor = User::factory()->create(['score' => 95]);
    $highScorePosts = Post::factory()->count(5)->create(['author_id' => $highScoreAuthor->id]);

    $lowScoreAuthor = User::factory()->create(['score' => 45]);
    $lowScorePosts = Post::factory()->count(5)->create(['author_id' => $lowScoreAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($highScorePosts->merge($lowScorePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author.score',
                'data' => [
                    'operator' => 'isMin.inverse',
                    'settings' => ['number' => 80, 'aggregate' => null],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($lowScorePosts)
        ->assertCanNotSeeTableRecords($highScorePosts);
});

it('can filter records using select constraint with relationship method', function (): void {
    $activeAuthor = User::factory()->create(['status' => 'active']);
    $activePosts = Post::factory()->count(5)->create(['author_id' => $activeAuthor->id]);

    $pendingAuthor = User::factory()->create(['status' => 'pending']);
    $pendingPosts = Post::factory()->count(5)->create(['author_id' => $pendingAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($activePosts->merge($pendingPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author.status',
                'data' => [
                    'operator' => 'is',
                    'settings' => ['value' => 'active'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($activePosts)
        ->assertCanNotSeeTableRecords($pendingPosts);
});

it('can filter records using select constraint with relationship method and inverse operator', function (): void {
    $activeAuthor = User::factory()->create(['status' => 'active']);
    $activePosts = Post::factory()->count(5)->create(['author_id' => $activeAuthor->id]);

    $pendingAuthor = User::factory()->create(['status' => 'pending']);
    $pendingPosts = Post::factory()->count(5)->create(['author_id' => $pendingAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($activePosts->merge($pendingPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author.status',
                'data' => [
                    'operator' => 'is.inverse',
                    'settings' => ['value' => 'active'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($pendingPosts)
        ->assertCanNotSeeTableRecords($activePosts);
});

it('can filter records using date constraint with relationship method', function (): void {
    $recentDate = '2024-06-20';
    $oldDate = '2024-01-10';
    $filterDate = '2024-06-15';

    $recentAuthor = User::factory()->create(['email_verified_at' => $recentDate]);
    $recentPosts = Post::factory()->count(5)->create(['author_id' => $recentAuthor->id]);

    $oldAuthor = User::factory()->create(['email_verified_at' => $oldDate]);
    $oldPosts = Post::factory()->count(5)->create(['author_id' => $oldAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author.email_verified_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => ['date' => $filterDate],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with relationship method and inverse operator', function (): void {
    $recentDate = '2024-06-20';
    $oldDate = '2024-01-10';
    $filterDate = '2024-06-15';

    $recentAuthor = User::factory()->create(['email_verified_at' => $recentDate]);
    $recentPosts = Post::factory()->count(5)->create(['author_id' => $recentAuthor->id]);

    $oldAuthor = User::factory()->create(['email_verified_at' => $oldDate]);
    $oldPosts = Post::factory()->count(5)->create(['author_id' => $oldAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author.email_verified_at',
                'data' => [
                    'operator' => 'isAfter.inverse',
                    'settings' => ['date' => $filterDate],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($oldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

it('can combine relationship constraints with regular constraints', function (): void {
    $activeHighScoreAuthor = User::factory()->create([
        'score' => 95,
        'status' => 'active',
    ]);
    $matchingPosts = Post::factory()->count(3)->create([
        'author_id' => $activeHighScoreAuthor->id,
        'is_published' => true,
    ]);

    $activeHighScoreUnpublished = Post::factory()->count(2)->create([
        'author_id' => $activeHighScoreAuthor->id,
        'is_published' => false,
    ]);

    $inactiveAuthor = User::factory()->create([
        'score' => 95,
        'status' => 'inactive',
    ]);
    $nonMatchingPosts = Post::factory()->count(3)->create([
        'author_id' => $inactiveAuthor->id,
        'is_published' => true,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($matchingPosts->merge($activeHighScoreUnpublished)->merge($nonMatchingPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author.score',
                'data' => [
                    'operator' => 'isMin',
                    'settings' => ['number' => 80, 'aggregate' => null],
                ],
            ],
            [
                'type' => 'author.status',
                'data' => [
                    'operator' => 'is',
                    'settings' => ['value' => 'active'],
                ],
            ],
            [
                'type' => 'is_published',
                'data' => [
                    'operator' => 'isTrue',
                    'settings' => [],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($activeHighScoreUnpublished->merge($nonMatchingPosts));
});

it('can filter records using number constraint with sum aggregate on relationship', function (): void {
    // Create user with high total rating across all posts
    $highTotalUser = User::factory()->create();
    Post::factory()->count(3)->create([
        'author_id' => $highTotalUser->id,
        'rating' => 8, // Total: 24
    ]);

    // Create user with low total rating across all posts
    $lowTotalUser = User::factory()->create();
    Post::factory()->count(3)->create([
        'author_id' => $lowTotalUser->id,
        'rating' => 2, // Total: 6
    ]);

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highTotalUser, $lowTotalUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'posts.rating',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 20, 'aggregate' => 'sum'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highTotalUser])
        ->assertCanNotSeeTableRecords([$lowTotalUser]);
});

it('can filter records using number constraint with average aggregate on relationship', function (): void {
    // Create user with high average rating
    $highAvgUser = User::factory()->create();
    Post::factory()->count(3)->create([
        'author_id' => $highAvgUser->id,
        'rating' => 9, // Average: 9
    ]);

    // Create user with low average rating
    $lowAvgUser = User::factory()->create();
    Post::factory()->count(3)->create([
        'author_id' => $lowAvgUser->id,
        'rating' => 3, // Average: 3
    ]);

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highAvgUser, $lowAvgUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'posts.rating',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 7, 'aggregate' => 'avg'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highAvgUser])
        ->assertCanNotSeeTableRecords([$lowAvgUser]);
});

it('can filter records using number constraint with min aggregate on relationship', function (): void {
    // Create user where even the lowest rating is high
    $highMinUser = User::factory()->create();
    Post::factory()->create([
        'author_id' => $highMinUser->id,
        'rating' => 8,
    ]);
    Post::factory()->create([
        'author_id' => $highMinUser->id,
        'rating' => 9,
    ]);
    Post::factory()->create([
        'author_id' => $highMinUser->id,
        'rating' => 10,
    ]);

    // Create user with at least one low rating
    $lowMinUser = User::factory()->create();
    Post::factory()->create([
        'author_id' => $lowMinUser->id,
        'rating' => 2, // This is the min
    ]);
    Post::factory()->create([
        'author_id' => $lowMinUser->id,
        'rating' => 9,
    ]);

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highMinUser, $lowMinUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'posts.rating',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 7, 'aggregate' => 'min'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highMinUser])
        ->assertCanNotSeeTableRecords([$lowMinUser]);
});

it('can filter records using number constraint with max aggregate on relationship', function (): void {
    // Create user with at least one very high rating
    $highMaxUser = User::factory()->create();
    Post::factory()->create([
        'author_id' => $highMaxUser->id,
        'rating' => 10, // This is the max
    ]);
    Post::factory()->create([
        'author_id' => $highMaxUser->id,
        'rating' => 5,
    ]);

    // Create user where even the highest rating is low
    $lowMaxUser = User::factory()->create();
    Post::factory()->create([
        'author_id' => $lowMaxUser->id,
        'rating' => 3,
    ]);
    Post::factory()->create([
        'author_id' => $lowMaxUser->id,
        'rating' => 4, // This is the max
    ]);

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highMaxUser, $lowMaxUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'posts.rating',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 8, 'aggregate' => 'max'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highMaxUser])
        ->assertCanNotSeeTableRecords([$lowMaxUser]);
});

it('can filter records using number constraint with aggregate and inverse operator', function (): void {
    // Create user with high average rating
    $highAvgUser = User::factory()->create();
    Post::factory()->count(3)->create([
        'author_id' => $highAvgUser->id,
        'rating' => 9,
    ]);

    // Create user with low average rating
    $lowAvgUser = User::factory()->create();
    Post::factory()->count(3)->create([
        'author_id' => $lowAvgUser->id,
        'rating' => 3,
    ]);

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highAvgUser, $lowAvgUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'posts.rating',
                    'data' => [
                        'operator' => 'isMin.inverse', // Less than
                        'settings' => ['number' => 7, 'aggregate' => 'avg'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$lowAvgUser])
        ->assertCanNotSeeTableRecords([$highAvgUser]);
});

it('can filter records using number constraint with sum aggregate on `BelongsToMany` relationship', function (): void {
    // Create user with high total budget across all teams
    $highTotalUser = User::factory()->create();
    $highTeams = Team::factory()->count(3)->create(['budget' => 5000]); // Total: 15000
    $highTotalUser->teams()->attach($highTeams->pluck('id'));

    // Create user with low total budget across all teams
    $lowTotalUser = User::factory()->create();
    $lowTeams = Team::factory()->count(2)->create(['budget' => 1000]); // Total: 2000
    $lowTotalUser->teams()->attach($lowTeams->pluck('id'));

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highTotalUser, $lowTotalUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'teams.budget',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 10000, 'aggregate' => 'sum'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highTotalUser])
        ->assertCanNotSeeTableRecords([$lowTotalUser]);
});

it('can filter records using number constraint with average aggregate on `BelongsToMany` relationship', function (): void {
    // Create user with high average budget
    $highAvgUser = User::factory()->create();
    $highTeams = Team::factory()->count(3)->create(['budget' => 8000]); // Average: 8000
    $highAvgUser->teams()->attach($highTeams->pluck('id'));

    // Create user with low average budget
    $lowAvgUser = User::factory()->create();
    $lowTeams = Team::factory()->count(3)->create(['budget' => 2000]); // Average: 2000
    $lowAvgUser->teams()->attach($lowTeams->pluck('id'));

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highAvgUser, $lowAvgUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'teams.budget',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 5000, 'aggregate' => 'avg'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highAvgUser])
        ->assertCanNotSeeTableRecords([$lowAvgUser]);
});

it('can filter records using number constraint with min aggregate on `BelongsToMany` relationship', function (): void {
    // Create user where even the lowest budget is high
    $highMinUser = User::factory()->create();
    $highMinTeams = collect([
        Team::factory()->create(['budget' => 6000]),
        Team::factory()->create(['budget' => 8000]),
        Team::factory()->create(['budget' => 10000]),
    ]);
    $highMinUser->teams()->attach($highMinTeams->pluck('id'));

    // Create user with at least one low budget team
    $lowMinUser = User::factory()->create();
    $lowMinTeams = collect([
        Team::factory()->create(['budget' => 1000]), // This is the min
        Team::factory()->create(['budget' => 9000]),
    ]);
    $lowMinUser->teams()->attach($lowMinTeams->pluck('id'));

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highMinUser, $lowMinUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'teams.budget',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 5000, 'aggregate' => 'min'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highMinUser])
        ->assertCanNotSeeTableRecords([$lowMinUser]);
});

it('can filter records using number constraint with max aggregate on `BelongsToMany` relationship', function (): void {
    // Create user with at least one very high budget team
    $highMaxUser = User::factory()->create();
    $highMaxTeams = collect([
        Team::factory()->create(['budget' => 15000]), // This is the max
        Team::factory()->create(['budget' => 3000]),
    ]);
    $highMaxUser->teams()->attach($highMaxTeams->pluck('id'));

    // Create user where even the highest budget is low
    $lowMaxUser = User::factory()->create();
    $lowMaxTeams = collect([
        Team::factory()->create(['budget' => 2000]),
        Team::factory()->create(['budget' => 3000]), // This is the max
    ]);
    $lowMaxUser->teams()->attach($lowMaxTeams->pluck('id'));

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highMaxUser, $lowMaxUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'teams.budget',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 10000, 'aggregate' => 'max'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highMaxUser])
        ->assertCanNotSeeTableRecords([$lowMaxUser]);
});

it('can filter records using number constraint with aggregate and inverse operator on `BelongsToMany` relationship', function (): void {
    // Create user with high average budget
    $highAvgUser = User::factory()->create();
    $highTeams = Team::factory()->count(3)->create(['budget' => 8000]);
    $highAvgUser->teams()->attach($highTeams->pluck('id'));

    // Create user with low average budget
    $lowAvgUser = User::factory()->create();
    $lowTeams = Team::factory()->count(3)->create(['budget' => 2000]);
    $lowAvgUser->teams()->attach($lowTeams->pluck('id'));

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highAvgUser, $lowAvgUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'teams.budget',
                    'data' => [
                        'operator' => 'isMin.inverse', // Less than
                        'settings' => ['number' => 5000, 'aggregate' => 'avg'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$lowAvgUser])
        ->assertCanNotSeeTableRecords([$highAvgUser]);
});

// Legacy `relationship()` method tests - these ensure backwards compatibility

it('can filter records using text constraint with relationship method (legacy `relationship()`)', function (): void {
    $matchingAuthor = User::factory()->create(['email' => 'john@example.com']);
    $matchingPosts = Post::factory()->count(5)->create(['author_id' => $matchingAuthor->id]);

    $nonMatchingAuthor = User::factory()->create(['email' => 'jane@different.com']);
    $nonMatchingPosts = Post::factory()->count(5)->create(['author_id' => $nonMatchingAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($matchingPosts->merge($nonMatchingPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author_email',
                'data' => [
                    'operator' => 'contains',
                    'settings' => ['text' => 'example.com'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can filter records using boolean constraint with relationship method (legacy `relationship()`)', function (): void {
    $activeAuthor = User::factory()->create(['has_email_authentication' => true]);
    $activePosts = Post::factory()->count(5)->create(['author_id' => $activeAuthor->id]);

    $inactiveAuthor = User::factory()->create(['has_email_authentication' => false]);
    $inactivePosts = Post::factory()->count(5)->create(['author_id' => $inactiveAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($activePosts->merge($inactivePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author_has_email_auth',
                'data' => [
                    'operator' => 'isTrue',
                    'settings' => [],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($activePosts)
        ->assertCanNotSeeTableRecords($inactivePosts);
});

it('can filter records using boolean constraint with relationship method and inverse operator (legacy `relationship()`)', function (): void {
    $activeAuthor = User::factory()->create(['has_email_authentication' => true]);
    $activePosts = Post::factory()->count(5)->create(['author_id' => $activeAuthor->id]);

    $inactiveAuthor = User::factory()->create(['has_email_authentication' => false]);
    $inactivePosts = Post::factory()->count(5)->create(['author_id' => $inactiveAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($activePosts->merge($inactivePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author_has_email_auth',
                'data' => [
                    'operator' => 'isTrue.inverse',
                    'settings' => [],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($inactivePosts)
        ->assertCanNotSeeTableRecords($activePosts);
});

it('can filter records using number constraint with relationship method (legacy `relationship()`)', function (): void {
    $highScoreAuthor = User::factory()->create(['score' => 95]);
    $highScorePosts = Post::factory()->count(5)->create(['author_id' => $highScoreAuthor->id]);

    $lowScoreAuthor = User::factory()->create(['score' => 45]);
    $lowScorePosts = Post::factory()->count(5)->create(['author_id' => $lowScoreAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($highScorePosts->merge($lowScorePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author_score',
                'data' => [
                    'operator' => 'isMin',
                    'settings' => ['number' => 80, 'aggregate' => null],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($highScorePosts)
        ->assertCanNotSeeTableRecords($lowScorePosts);
});

it('can filter records using number constraint with relationship method and inverse operator (legacy `relationship()`)', function (): void {
    $highScoreAuthor = User::factory()->create(['score' => 95]);
    $highScorePosts = Post::factory()->count(5)->create(['author_id' => $highScoreAuthor->id]);

    $lowScoreAuthor = User::factory()->create(['score' => 45]);
    $lowScorePosts = Post::factory()->count(5)->create(['author_id' => $lowScoreAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($highScorePosts->merge($lowScorePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author_score',
                'data' => [
                    'operator' => 'isMin.inverse',
                    'settings' => ['number' => 80, 'aggregate' => null],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($lowScorePosts)
        ->assertCanNotSeeTableRecords($highScorePosts);
});

it('can filter records using select constraint with relationship method (legacy `relationship()`)', function (): void {
    $activeAuthor = User::factory()->create(['status' => 'active']);
    $activePosts = Post::factory()->count(5)->create(['author_id' => $activeAuthor->id]);

    $pendingAuthor = User::factory()->create(['status' => 'pending']);
    $pendingPosts = Post::factory()->count(5)->create(['author_id' => $pendingAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($activePosts->merge($pendingPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author_status',
                'data' => [
                    'operator' => 'is',
                    'settings' => ['value' => 'active'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($activePosts)
        ->assertCanNotSeeTableRecords($pendingPosts);
});

it('can filter records using select constraint with relationship method and inverse operator (legacy `relationship()`)', function (): void {
    $activeAuthor = User::factory()->create(['status' => 'active']);
    $activePosts = Post::factory()->count(5)->create(['author_id' => $activeAuthor->id]);

    $pendingAuthor = User::factory()->create(['status' => 'pending']);
    $pendingPosts = Post::factory()->count(5)->create(['author_id' => $pendingAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($activePosts->merge($pendingPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author_status',
                'data' => [
                    'operator' => 'is.inverse',
                    'settings' => ['value' => 'active'],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($pendingPosts)
        ->assertCanNotSeeTableRecords($activePosts);
});

it('can filter records using date constraint with relationship method (legacy `relationship()`)', function (): void {
    $recentDate = '2024-06-20';
    $oldDate = '2024-01-10';
    $filterDate = '2024-06-15';

    $recentAuthor = User::factory()->create(['email_verified_at' => $recentDate]);
    $recentPosts = Post::factory()->count(5)->create(['author_id' => $recentAuthor->id]);

    $oldAuthor = User::factory()->create(['email_verified_at' => $oldDate]);
    $oldPosts = Post::factory()->count(5)->create(['author_id' => $oldAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author_verified_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => ['date' => $filterDate],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with relationship method and inverse operator (legacy `relationship()`)', function (): void {
    $recentDate = '2024-06-20';
    $oldDate = '2024-01-10';
    $filterDate = '2024-06-15';

    $recentAuthor = User::factory()->create(['email_verified_at' => $recentDate]);
    $recentPosts = Post::factory()->count(5)->create(['author_id' => $recentAuthor->id]);

    $oldAuthor = User::factory()->create(['email_verified_at' => $oldDate]);
    $oldPosts = Post::factory()->count(5)->create(['author_id' => $oldAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author_verified_at',
                'data' => [
                    'operator' => 'isAfter.inverse',
                    'settings' => ['date' => $filterDate],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($oldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

it('can combine relationship constraints with regular constraints (legacy `relationship()`)', function (): void {
    $activeHighScoreAuthor = User::factory()->create([
        'score' => 95,
        'status' => 'active',
    ]);
    $matchingPosts = Post::factory()->count(3)->create([
        'author_id' => $activeHighScoreAuthor->id,
        'is_published' => true,
    ]);

    $activeHighScoreUnpublished = Post::factory()->count(2)->create([
        'author_id' => $activeHighScoreAuthor->id,
        'is_published' => false,
    ]);

    $inactiveAuthor = User::factory()->create([
        'score' => 95,
        'status' => 'inactive',
    ]);
    $nonMatchingPosts = Post::factory()->count(3)->create([
        'author_id' => $inactiveAuthor->id,
        'is_published' => true,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($matchingPosts->merge($activeHighScoreUnpublished)->merge($nonMatchingPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author_score',
                'data' => [
                    'operator' => 'isMin',
                    'settings' => ['number' => 80, 'aggregate' => null],
                ],
            ],
            [
                'type' => 'author_status',
                'data' => [
                    'operator' => 'is',
                    'settings' => ['value' => 'active'],
                ],
            ],
            [
                'type' => 'is_published',
                'data' => [
                    'operator' => 'isTrue',
                    'settings' => [],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($activeHighScoreUnpublished->merge($nonMatchingPosts));
});

it('can filter records using number constraint with sum aggregate on relationship (legacy `relationship()`)', function (): void {
    // Create user with high total rating across all posts
    $highTotalUser = User::factory()->create();
    Post::factory()->count(3)->create([
        'author_id' => $highTotalUser->id,
        'rating' => 8, // Total: 24
    ]);

    // Create user with low total rating across all posts
    $lowTotalUser = User::factory()->create();
    Post::factory()->count(3)->create([
        'author_id' => $lowTotalUser->id,
        'rating' => 2, // Total: 6
    ]);

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highTotalUser, $lowTotalUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'posts_rating',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 20, 'aggregate' => 'sum'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highTotalUser])
        ->assertCanNotSeeTableRecords([$lowTotalUser]);
});

it('can filter records using number constraint with average aggregate on relationship (legacy `relationship()`)', function (): void {
    // Create user with high average rating
    $highAvgUser = User::factory()->create();
    Post::factory()->count(3)->create([
        'author_id' => $highAvgUser->id,
        'rating' => 9, // Average: 9
    ]);

    // Create user with low average rating
    $lowAvgUser = User::factory()->create();
    Post::factory()->count(3)->create([
        'author_id' => $lowAvgUser->id,
        'rating' => 3, // Average: 3
    ]);

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highAvgUser, $lowAvgUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'posts_rating',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 7, 'aggregate' => 'avg'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highAvgUser])
        ->assertCanNotSeeTableRecords([$lowAvgUser]);
});

it('can filter records using number constraint with min aggregate on relationship (legacy `relationship()`)', function (): void {
    // Create user where even the lowest rating is high
    $highMinUser = User::factory()->create();
    Post::factory()->create([
        'author_id' => $highMinUser->id,
        'rating' => 8,
    ]);
    Post::factory()->create([
        'author_id' => $highMinUser->id,
        'rating' => 9,
    ]);
    Post::factory()->create([
        'author_id' => $highMinUser->id,
        'rating' => 10,
    ]);

    // Create user with at least one low rating
    $lowMinUser = User::factory()->create();
    Post::factory()->create([
        'author_id' => $lowMinUser->id,
        'rating' => 2, // This is the min
    ]);
    Post::factory()->create([
        'author_id' => $lowMinUser->id,
        'rating' => 9,
    ]);

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highMinUser, $lowMinUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'posts_rating',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 7, 'aggregate' => 'min'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highMinUser])
        ->assertCanNotSeeTableRecords([$lowMinUser]);
});

it('can filter records using number constraint with max aggregate on relationship (legacy `relationship()`)', function (): void {
    // Create user with at least one very high rating
    $highMaxUser = User::factory()->create();
    Post::factory()->create([
        'author_id' => $highMaxUser->id,
        'rating' => 10, // This is the max
    ]);
    Post::factory()->create([
        'author_id' => $highMaxUser->id,
        'rating' => 5,
    ]);

    // Create user where even the highest rating is low
    $lowMaxUser = User::factory()->create();
    Post::factory()->create([
        'author_id' => $lowMaxUser->id,
        'rating' => 3,
    ]);
    Post::factory()->create([
        'author_id' => $lowMaxUser->id,
        'rating' => 4, // This is the max
    ]);

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highMaxUser, $lowMaxUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'posts_rating',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 8, 'aggregate' => 'max'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highMaxUser])
        ->assertCanNotSeeTableRecords([$lowMaxUser]);
});

it('can filter records using number constraint with aggregate and inverse operator (legacy `relationship()`)', function (): void {
    // Create user with high average rating
    $highAvgUser = User::factory()->create();
    Post::factory()->count(3)->create([
        'author_id' => $highAvgUser->id,
        'rating' => 9,
    ]);

    // Create user with low average rating
    $lowAvgUser = User::factory()->create();
    Post::factory()->count(3)->create([
        'author_id' => $lowAvgUser->id,
        'rating' => 3,
    ]);

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highAvgUser, $lowAvgUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'posts_rating',
                    'data' => [
                        'operator' => 'isMin.inverse', // Less than
                        'settings' => ['number' => 7, 'aggregate' => 'avg'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$lowAvgUser])
        ->assertCanNotSeeTableRecords([$highAvgUser]);
});

it('can filter records using number constraint with sum aggregate on `BelongsToMany` relationship (legacy `relationship()`)', function (): void {
    // Create user with high total budget across all teams
    $highTotalUser = User::factory()->create();
    $highTeams = Team::factory()->count(3)->create(['budget' => 5000]); // Total: 15000
    $highTotalUser->teams()->attach($highTeams->pluck('id'));

    // Create user with low total budget across all teams
    $lowTotalUser = User::factory()->create();
    $lowTeams = Team::factory()->count(2)->create(['budget' => 1000]); // Total: 2000
    $lowTotalUser->teams()->attach($lowTeams->pluck('id'));

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highTotalUser, $lowTotalUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'teams_budget',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 10000, 'aggregate' => 'sum'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highTotalUser])
        ->assertCanNotSeeTableRecords([$lowTotalUser]);
});

it('can filter records using number constraint with average aggregate on `BelongsToMany` relationship (legacy `relationship()`)', function (): void {
    // Create user with high average budget
    $highAvgUser = User::factory()->create();
    $highTeams = Team::factory()->count(3)->create(['budget' => 8000]); // Average: 8000
    $highAvgUser->teams()->attach($highTeams->pluck('id'));

    // Create user with low average budget
    $lowAvgUser = User::factory()->create();
    $lowTeams = Team::factory()->count(3)->create(['budget' => 2000]); // Average: 2000
    $lowAvgUser->teams()->attach($lowTeams->pluck('id'));

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highAvgUser, $lowAvgUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'teams_budget',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 5000, 'aggregate' => 'avg'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highAvgUser])
        ->assertCanNotSeeTableRecords([$lowAvgUser]);
});

it('can filter records using number constraint with min aggregate on `BelongsToMany` relationship (legacy `relationship()`)', function (): void {
    // Create user where even the lowest budget is high
    $highMinUser = User::factory()->create();
    $highMinTeams = collect([
        Team::factory()->create(['budget' => 6000]),
        Team::factory()->create(['budget' => 8000]),
        Team::factory()->create(['budget' => 10000]),
    ]);
    $highMinUser->teams()->attach($highMinTeams->pluck('id'));

    // Create user with at least one low budget team
    $lowMinUser = User::factory()->create();
    $lowMinTeams = collect([
        Team::factory()->create(['budget' => 1000]), // This is the min
        Team::factory()->create(['budget' => 9000]),
    ]);
    $lowMinUser->teams()->attach($lowMinTeams->pluck('id'));

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highMinUser, $lowMinUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'teams_budget',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 5000, 'aggregate' => 'min'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highMinUser])
        ->assertCanNotSeeTableRecords([$lowMinUser]);
});

it('can filter records using number constraint with max aggregate on `BelongsToMany` relationship (legacy `relationship()`)', function (): void {
    // Create user with at least one very high budget team
    $highMaxUser = User::factory()->create();
    $highMaxTeams = collect([
        Team::factory()->create(['budget' => 15000]), // This is the max
        Team::factory()->create(['budget' => 3000]),
    ]);
    $highMaxUser->teams()->attach($highMaxTeams->pluck('id'));

    // Create user where even the highest budget is low
    $lowMaxUser = User::factory()->create();
    $lowMaxTeams = collect([
        Team::factory()->create(['budget' => 2000]),
        Team::factory()->create(['budget' => 3000]), // This is the max
    ]);
    $lowMaxUser->teams()->attach($lowMaxTeams->pluck('id'));

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highMaxUser, $lowMaxUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'teams_budget',
                    'data' => [
                        'operator' => 'isMin',
                        'settings' => ['number' => 10000, 'aggregate' => 'max'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$highMaxUser])
        ->assertCanNotSeeTableRecords([$lowMaxUser]);
});

it('can filter records using number constraint with aggregate and inverse operator on `BelongsToMany` relationship (legacy `relationship()`)', function (): void {
    // Create user with high average budget
    $highAvgUser = User::factory()->create();
    $highTeams = Team::factory()->count(3)->create(['budget' => 8000]);
    $highAvgUser->teams()->attach($highTeams->pluck('id'));

    // Create user with low average budget
    $lowAvgUser = User::factory()->create();
    $lowTeams = Team::factory()->count(3)->create(['budget' => 2000]);
    $lowAvgUser->teams()->attach($lowTeams->pluck('id'));

    livewire(UsersQueryBuilderTable::class)
        ->assertCanSeeTableRecords([$highAvgUser, $lowAvgUser])
        ->tap(fn ($livewire) => $livewire
            ->set('tableDeferredFilters.query_builder.rules', [
                [
                    'type' => 'teams_budget',
                    'data' => [
                        'operator' => 'isMin.inverse', // Less than
                        'settings' => ['number' => 5000, 'aggregate' => 'avg'],
                    ],
                ],
            ])
            ->call('applyTableFilters'))
        ->assertCanSeeTableRecords([$lowAvgUser])
        ->assertCanNotSeeTableRecords([$highAvgUser]);
});

// Relative Date Filtering Tests - IsAfterOperator

it('can filter records using date constraint with is after operator in `absolute` mode', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(5),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'absolute',
                        'date' => now()->toDateString(),
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with is after operator with `today` preset', function (): void {
    $todayPosts = Post::factory()->count(3)->create([
        'created_at' => now()->startOfDay(),
    ]);

    $futurePosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(5),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($todayPosts->merge($futurePosts)->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'today',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($todayPosts->merge($futurePosts))
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with is after operator with `this_year` preset', function (): void {
    $thisYearPosts = Post::factory()->count(5)->create([
        'created_at' => now()->startOfYear()->addDays(30),
    ]);

    $lastYearPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subYear(),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($thisYearPosts->merge($lastYearPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'this_year',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($thisYearPosts)
        ->assertCanNotSeeTableRecords($lastYearPosts);
});

it('can filter records using date constraint with is after operator with `past_week` preset', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(3),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(10),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_week',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with is after operator with `past_2_weeks` preset', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(10),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(20),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_2_weeks',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with is after operator with `past_month` preset', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(15),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(45),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_month',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with is after operator with `past_quarter` preset', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subMonths(2),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subMonths(4),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_quarter',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with is after operator with `past_6_months` preset', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subMonths(4),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subMonths(8),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_6_months',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with is after operator with `past_year` preset', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subMonths(6),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subMonths(18),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_year',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with is after operator with `next_week` preset', function (): void {
    $farFuturePosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(14),
    ]);

    $nearFuturePosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(3),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($farFuturePosts->merge($nearFuturePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'next_week',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($farFuturePosts)
        ->assertCanNotSeeTableRecords($nearFuturePosts);
});

it('can filter records using date constraint with is after operator with `next_month` preset', function (): void {
    $farFuturePosts = Post::factory()->count(5)->create([
        'created_at' => now()->addMonths(2),
    ]);

    $nearFuturePosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(15),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($farFuturePosts->merge($nearFuturePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'next_month',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($farFuturePosts)
        ->assertCanNotSeeTableRecords($nearFuturePosts);
});

it('can filter records using date constraint with is after operator with custom relative `day` unit past', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(3),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(15),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'past',
                        'relative_value' => 10,
                        'relative_unit' => 'day',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with is after operator with custom relative `week` unit past', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(10),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subWeeks(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'past',
                        'relative_value' => 3,
                        'relative_unit' => 'week',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with is after operator with custom relative `month` unit past', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subMonths(2),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subMonths(6),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'past',
                        'relative_value' => 4,
                        'relative_unit' => 'month',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with is after operator with custom relative `year` unit past', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subYear(),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subYears(4),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'past',
                        'relative_value' => 2,
                        'relative_unit' => 'year',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using date constraint with is after operator with custom relative `day` unit future', function (): void {
    $farFuturePosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(20),
    ]);

    $nearFuturePosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(3),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($farFuturePosts->merge($nearFuturePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'future',
                        'relative_value' => 10,
                        'relative_unit' => 'day',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($farFuturePosts)
        ->assertCanNotSeeTableRecords($nearFuturePosts);
});

it('can filter records using date constraint with is after operator with custom relative `month` unit future', function (): void {
    $farFuturePosts = Post::factory()->count(5)->create([
        'created_at' => now()->addMonths(5),
    ]);

    $nearFuturePosts = Post::factory()->count(5)->create([
        'created_at' => now()->addMonth(),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($farFuturePosts->merge($nearFuturePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'future',
                        'relative_value' => 3,
                        'relative_unit' => 'month',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($farFuturePosts)
        ->assertCanNotSeeTableRecords($nearFuturePosts);
});

it('can filter records using date constraint with is after operator inverse with preset', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(3),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(15),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter.inverse',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_week',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($oldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

it('can filter records using date constraint with is after operator inverse with custom relative', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(3),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(20),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter.inverse',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'past',
                        'relative_value' => 10,
                        'relative_unit' => 'day',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($oldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

// Relative Date Filtering Tests - IsBeforeOperator

it('can filter records using date constraint with is before operator in `absolute` mode', function (): void {
    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(5),
    ]);

    $futurePosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($oldPosts->merge($futurePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'absolute',
                        'date' => now()->toDateString(),
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($oldPosts)
        ->assertCanNotSeeTableRecords($futurePosts);
});

it('can filter records using date constraint with is before operator with `today` preset', function (): void {
    $todayPosts = Post::factory()->count(3)->create([
        'created_at' => now()->startOfDay(),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(5),
    ]);

    $futurePosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($todayPosts->merge($oldPosts)->merge($futurePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'today',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($todayPosts->merge($oldPosts))
        ->assertCanNotSeeTableRecords($futurePosts);
});

it('can filter records using date constraint with is before operator with `next_week` preset', function (): void {
    $soonPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(3),
    ]);

    $laterPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(14),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($soonPosts->merge($laterPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'next_week',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($soonPosts)
        ->assertCanNotSeeTableRecords($laterPosts);
});

it('can filter records using date constraint with is before operator with `next_2_weeks` preset', function (): void {
    $soonPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(7),
    ]);

    $laterPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(21),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($soonPosts->merge($laterPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'next_2_weeks',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($soonPosts)
        ->assertCanNotSeeTableRecords($laterPosts);
});

it('can filter records using date constraint with is before operator with `next_month` preset', function (): void {
    $soonPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(15),
    ]);

    $laterPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addMonths(2),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($soonPosts->merge($laterPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'next_month',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($soonPosts)
        ->assertCanNotSeeTableRecords($laterPosts);
});

it('can filter records using date constraint with is before operator with `next_quarter` preset', function (): void {
    $soonPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addMonths(2),
    ]);

    $laterPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addMonths(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($soonPosts->merge($laterPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'next_quarter',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($soonPosts)
        ->assertCanNotSeeTableRecords($laterPosts);
});

it('can filter records using date constraint with is before operator with `next_6_months` preset', function (): void {
    $soonPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addMonths(4),
    ]);

    $laterPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addMonths(8),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($soonPosts->merge($laterPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'next_6_months',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($soonPosts)
        ->assertCanNotSeeTableRecords($laterPosts);
});

it('can filter records using date constraint with is before operator with `next_year` preset', function (): void {
    $soonPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addMonths(6),
    ]);

    $laterPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addMonths(18),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($soonPosts->merge($laterPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'next_year',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($soonPosts)
        ->assertCanNotSeeTableRecords($laterPosts);
});

it('can filter records using date constraint with is before operator with `past_week` preset', function (): void {
    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(14),
    ]);

    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(3),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($oldPosts->merge($recentPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_week',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($oldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

it('can filter records using date constraint with is before operator with `past_month` preset', function (): void {
    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subMonths(2),
    ]);

    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(15),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($oldPosts->merge($recentPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_month',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($oldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

it('can filter records using date constraint with is before operator with custom relative `day` unit future', function (): void {
    $soonPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(3),
    ]);

    $laterPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(20),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($soonPosts->merge($laterPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'future',
                        'relative_value' => 10,
                        'relative_unit' => 'day',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($soonPosts)
        ->assertCanNotSeeTableRecords($laterPosts);
});

it('can filter records using date constraint with is before operator with custom relative `week` unit future', function (): void {
    $soonPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(7),
    ]);

    $laterPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addWeeks(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($soonPosts->merge($laterPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'future',
                        'relative_value' => 3,
                        'relative_unit' => 'week',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($soonPosts)
        ->assertCanNotSeeTableRecords($laterPosts);
});

it('can filter records using date constraint with is before operator with custom relative `month` unit future', function (): void {
    $soonPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addMonths(2),
    ]);

    $laterPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addMonths(6),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($soonPosts->merge($laterPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'future',
                        'relative_value' => 4,
                        'relative_unit' => 'month',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($soonPosts)
        ->assertCanNotSeeTableRecords($laterPosts);
});

it('can filter records using date constraint with is before operator with custom relative `year` unit future', function (): void {
    $soonPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addYear(),
    ]);

    $laterPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addYears(4),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($soonPosts->merge($laterPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'future',
                        'relative_value' => 2,
                        'relative_unit' => 'year',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($soonPosts)
        ->assertCanNotSeeTableRecords($laterPosts);
});

it('can filter records using date constraint with is before operator with custom relative `day` unit past', function (): void {
    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(20),
    ]);

    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(3),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($oldPosts->merge($recentPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'past',
                        'relative_value' => 10,
                        'relative_unit' => 'day',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($oldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

it('can filter records using date constraint with is before operator with custom relative `month` unit past', function (): void {
    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subMonths(6),
    ]);

    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subMonths(2),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($oldPosts->merge($recentPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'past',
                        'relative_value' => 4,
                        'relative_unit' => 'month',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($oldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

it('can filter records using date constraint with is before operator inverse with preset', function (): void {
    $soonPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(3),
    ]);

    $laterPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(14),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($soonPosts->merge($laterPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore.inverse',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'next_week',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($laterPosts)
        ->assertCanNotSeeTableRecords($soonPosts);
});

it('can filter records using date constraint with is before operator inverse with custom relative', function (): void {
    $soonPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(5),
    ]);

    $laterPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(25),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($soonPosts->merge($laterPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore.inverse',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'future',
                        'relative_value' => 15,
                        'relative_unit' => 'day',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($laterPosts)
        ->assertCanNotSeeTableRecords($soonPosts);
});

// Combined relative date tests

it('can filter records using combined `relative` date constraints', function (): void {
    // Posts created in the last month but before next week
    $matchingPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(10),
    ]);

    // Posts created more than a month ago
    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subMonths(2),
    ]);

    // Posts created in the future
    $futurePosts = Post::factory()->count(5)->create([
        'created_at' => now()->addWeeks(2),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($matchingPosts->merge($oldPosts)->merge($futurePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_month',
                    ],
                ],
            ],
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'next_week',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($oldPosts->merge($futurePosts));
});

it('can filter records using `relative` date constraint with relationship', function (): void {
    $recentAuthor = User::factory()->create(['email_verified_at' => now()->subDays(5)]);
    $recentPosts = Post::factory()->count(5)->create(['author_id' => $recentAuthor->id]);

    $oldAuthor = User::factory()->create(['email_verified_at' => now()->subMonths(3)]);
    $oldPosts = Post::factory()->count(5)->create(['author_id' => $oldAuthor->id]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'author.email_verified_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_month',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using `relative` date constraint with OR rules', function (): void {
    // Posts created in the past week
    $recentPosts = Post::factory()->count(3)->create([
        'created_at' => now()->subDays(3),
        'is_published' => false,
    ]);

    // Published posts (regardless of date)
    $publishedPosts = Post::factory()->count(3)->create([
        'created_at' => now()->subMonths(3),
        'is_published' => true,
    ]);

    // Old unpublished posts
    $oldUnpublishedPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subMonths(2),
        'is_published' => false,
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($publishedPosts)->merge($oldUnpublishedPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'or',
                'data' => [
                    'groups' => [
                        [
                            'rules' => [
                                [
                                    'type' => 'created_at',
                                    'data' => [
                                        'operator' => 'isAfter',
                                        'settings' => [
                                            'mode' => 'relative',
                                            'preset' => 'past_week',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'rules' => [
                                [
                                    'type' => 'is_published',
                                    'data' => [
                                        'operator' => 'isTrue',
                                        'settings' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts->merge($publishedPosts))
        ->assertCanNotSeeTableRecords($oldUnpublishedPosts);
});

it('can filter records using `relative` date without `mode` defaults to `absolute` for backwards compatibility', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'created_at' => now()->addDays(5),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'created_at' => now()->subDays(5),
    ]);

    // When mode is not specified, it should default to absolute behavior
    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'created_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => ['date' => now()->toDateString()],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

// Time-Based Date Filtering Tests

it('can filter records using datetime constraint with is after operator with `this_minute` preset', function (): void {
    $currentMinutePosts = Post::factory()->count(3)->create([
        'published_at' => now()->startOfMinute()->addSeconds(30),
    ]);

    $futurePosts = Post::factory()->count(5)->create([
        'published_at' => now()->addMinutes(5),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subMinutes(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($currentMinutePosts->merge($futurePosts)->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'this_minute',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($currentMinutePosts->merge($futurePosts))
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using datetime constraint with is after operator with `this_hour` preset', function (): void {
    $currentHourPosts = Post::factory()->count(3)->create([
        'published_at' => now()->startOfHour()->addMinutes(30),
    ]);

    $futurePosts = Post::factory()->count(5)->create([
        'published_at' => now()->addHours(5),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subHours(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($currentHourPosts->merge($futurePosts)->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'this_hour',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($currentHourPosts->merge($futurePosts))
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using datetime constraint with is after operator with `past_minute` preset', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subSeconds(30),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subMinutes(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_minute',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using datetime constraint with is after operator with `past_hour` preset', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subMinutes(30),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subHours(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_hour',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using datetime constraint with is after operator with `next_minute` preset', function (): void {
    $farFuturePosts = Post::factory()->count(5)->create([
        'published_at' => now()->addMinutes(5),
    ]);

    $nearFuturePosts = Post::factory()->count(5)->create([
        'published_at' => now()->addSeconds(30),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($farFuturePosts->merge($nearFuturePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'next_minute',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($farFuturePosts)
        ->assertCanNotSeeTableRecords($nearFuturePosts);
});

it('can filter records using datetime constraint with is after operator with `next_hour` preset', function (): void {
    $farFuturePosts = Post::factory()->count(5)->create([
        'published_at' => now()->addHours(5),
    ]);

    $nearFuturePosts = Post::factory()->count(5)->create([
        'published_at' => now()->addMinutes(30),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($farFuturePosts->merge($nearFuturePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'next_hour',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($farFuturePosts)
        ->assertCanNotSeeTableRecords($nearFuturePosts);
});

it('can filter records using datetime constraint with is after operator with `custom` preset and `second` unit in past tense', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subSeconds(15),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subMinutes(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'past',
                        'relative_value' => 30,
                        'relative_unit' => 'second',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using datetime constraint with is after operator with `custom` preset and `minute` unit in past tense', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subMinutes(5),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subMinutes(30),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'past',
                        'relative_value' => 10,
                        'relative_unit' => 'minute',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using datetime constraint with is after operator with `custom` preset and `hour` unit in past tense', function (): void {
    $recentPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subHours(1),
    ]);

    $oldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subHours(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($recentPosts->merge($oldPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'past',
                        'relative_value' => 3,
                        'relative_unit' => 'hour',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($recentPosts)
        ->assertCanNotSeeTableRecords($oldPosts);
});

it('can filter records using datetime constraint with is after operator with `custom` preset and `second` unit in future tense', function (): void {
    $farFuturePosts = Post::factory()->count(5)->create([
        'published_at' => now()->addMinutes(5),
    ]);

    $nearFuturePosts = Post::factory()->count(5)->create([
        'published_at' => now()->addSeconds(15),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($farFuturePosts->merge($nearFuturePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'future',
                        'relative_value' => 30,
                        'relative_unit' => 'second',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($farFuturePosts)
        ->assertCanNotSeeTableRecords($nearFuturePosts);
});

it('can filter records using datetime constraint with is after operator with `custom` preset and `minute` unit in future tense', function (): void {
    $farFuturePosts = Post::factory()->count(5)->create([
        'published_at' => now()->addMinutes(30),
    ]);

    $nearFuturePosts = Post::factory()->count(5)->create([
        'published_at' => now()->addMinutes(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($farFuturePosts->merge($nearFuturePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'future',
                        'relative_value' => 10,
                        'relative_unit' => 'minute',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($farFuturePosts)
        ->assertCanNotSeeTableRecords($nearFuturePosts);
});

it('can filter records using datetime constraint with is after operator with `custom` preset and `hour` unit in future tense', function (): void {
    $farFuturePosts = Post::factory()->count(5)->create([
        'published_at' => now()->addHours(5),
    ]);

    $nearFuturePosts = Post::factory()->count(5)->create([
        'published_at' => now()->addHours(1),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($farFuturePosts->merge($nearFuturePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isAfter',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'future',
                        'relative_value' => 3,
                        'relative_unit' => 'hour',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($farFuturePosts)
        ->assertCanNotSeeTableRecords($nearFuturePosts);
});

// IsBeforeOperator Time-Based Tests

it('can filter records using datetime constraint with is before operator with `this_minute` preset', function (): void {
    $oldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subMinutes(5),
    ]);

    $currentMinutePosts = Post::factory()->count(3)->create([
        'published_at' => now()->startOfMinute()->addSeconds(30),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($oldPosts->merge($currentMinutePosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'this_minute',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($oldPosts)
        ->assertCanNotSeeTableRecords($currentMinutePosts);
});

it('can filter records using datetime constraint with is before operator with `this_hour` preset', function (): void {
    $oldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subHours(5),
    ]);

    $currentHourPosts = Post::factory()->count(3)->create([
        'published_at' => now()->startOfHour()->addMinutes(30),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($oldPosts->merge($currentHourPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'this_hour',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($oldPosts)
        ->assertCanNotSeeTableRecords($currentHourPosts);
});

it('can filter records using datetime constraint with is before operator with `past_minute` preset', function (): void {
    $veryOldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subMinutes(5),
    ]);

    $recentPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subSeconds(30),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($veryOldPosts->merge($recentPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_minute',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($veryOldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

it('can filter records using datetime constraint with is before operator with `past_hour` preset', function (): void {
    $veryOldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subHours(5),
    ]);

    $recentPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subMinutes(30),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($veryOldPosts->merge($recentPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'past_hour',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($veryOldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

it('can filter records using datetime constraint with is before operator with `custom` preset and `second` unit', function (): void {
    $veryOldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subMinutes(5),
    ]);

    $recentPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subSeconds(15),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($veryOldPosts->merge($recentPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'past',
                        'relative_value' => 30,
                        'relative_unit' => 'second',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($veryOldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

it('can filter records using datetime constraint with is before operator with `custom` preset and `minute` unit', function (): void {
    $veryOldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subMinutes(30),
    ]);

    $recentPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subMinutes(5),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($veryOldPosts->merge($recentPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'past',
                        'relative_value' => 10,
                        'relative_unit' => 'minute',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($veryOldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

it('can filter records using datetime constraint with is before operator with `custom` preset and `hour` unit', function (): void {
    $veryOldPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subHours(5),
    ]);

    $recentPosts = Post::factory()->count(5)->create([
        'published_at' => now()->subHours(1),
    ]);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($veryOldPosts->merge($recentPosts))
        ->tap(applyQueryBuilderFilter([
            [
                'type' => 'published_at',
                'data' => [
                    'operator' => 'isBefore',
                    'settings' => [
                        'mode' => 'relative',
                        'preset' => 'custom',
                        'tense' => 'past',
                        'relative_value' => 3,
                        'relative_unit' => 'hour',
                    ],
                ],
            ],
        ]))
        ->assertCanSeeTableRecords($veryOldPosts)
        ->assertCanNotSeeTableRecords($recentPosts);
});

it('can delete a rule in the query builder filter in the browser', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    visit('/query-builder-table-test')
        ->assertSee('Query Builder Table Test')
        ->click('button[title="Filter"]')
        ->assertSee('Add rule')
        ->click('text=Add rule')
        ->assertSee('Title')
        ->click('.fi-dropdown-list-item >> text=Title')
        ->assertPresent('.fi-fo-builder-item')
        ->click('.fi-fo-builder-item button[title="Delete"]')
        ->assertNotPresent('.fi-fo-builder-item')
        ->assertNoSmoke();
});
