<?php

use Filament\Tests\Fixtures\Livewire\PostsTableWithAuthorState;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\ExpectationFailedException;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('`assertTableRendersWithinQueries()`', function (): void {
    it('passes when the table renders within the query budget', function (): void {
        Post::factory()->count(5)->create();

        livewire(PostsTableWithAuthorState::class)
            ->assertTableRendersWithinQueries(50);
    });

    it('fails when the table executes more queries than the budget', function (): void {
        Post::factory()->count(5)->create();

        $component = livewire(PostsTableWithAuthorState::class, ['eagerLoadAuthor' => false]);

        expect(fn () => $component->assertTableRendersWithinQueries(1))
            ->toThrow(ExpectationFailedException::class, 'Failed asserting that the [' . PostsTableWithAuthorState::class . '] table renders within [1] queries');
    });

    it('only counts the queries executed while rendering', function (): void {
        Post::factory()->count(5)->create();

        $component = livewire(PostsTableWithAuthorState::class);

        DB::enableQueryLog();

        try {
            foreach (range(1, 20) as $i) {
                DB::table('posts')->count();
            }

            $component->assertTableRendersWithinQueries(15);
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }
    });

    it('fails with a budget of zero', function (): void {
        Post::factory()->count(2)->create();

        expect(fn () => livewire(PostsTableWithAuthorState::class)->assertTableRendersWithinQueries(0))
            ->toThrow(ExpectationFailedException::class, 'renders within [0] queries');
    });

    it('disables the query log afterwards if it was not enabled before', function (): void {
        DB::disableQueryLog();

        livewire(PostsTableWithAuthorState::class)
            ->assertTableRendersWithinQueries(50);

        expect(DB::logging())->toBeFalse();
    });

    it('keeps the query log enabled afterwards if it was enabled before', function (): void {
        DB::enableQueryLog();

        livewire(PostsTableWithAuthorState::class)
            ->assertTableRendersWithinQueries(50);

        expect(DB::logging())->toBeTrue();

        DB::disableQueryLog();
    });
});

describe('`assertTableQueryCountDoesNotGrowWithRecords()`', function (): void {
    it('passes when relationships are eager loaded', function (): void {
        livewire(PostsTableWithAuthorState::class)
            ->assertTableQueryCountDoesNotGrowWithRecords(
                fn (int $count) => Post::factory()->count($count)->create(),
            );
    });

    it('fails when there is an N+1 query', function (): void {
        $component = livewire(PostsTableWithAuthorState::class, ['eagerLoadAuthor' => false]);

        expect(fn () => $component->assertTableQueryCountDoesNotGrowWithRecords(
            fn (int $count) => Post::factory()->count($count)->create(),
        ))->toThrow(ExpectationFailedException::class, 'Failed asserting that the number of queries executed while rendering the [' . PostsTableWithAuthorState::class . '] table does not grow with the number of records.');
    });

    it('includes the default record counts in the failure message', function (): void {
        $component = livewire(PostsTableWithAuthorState::class, ['eagerLoadAuthor' => false]);

        expect(fn () => $component->assertTableQueryCountDoesNotGrowWithRecords(
            fn (int $count) => Post::factory()->count($count)->create(),
        ))->toThrow(ExpectationFailedException::class, 'executed for [20] records');
    });

    it('creates the small number of records, then the remaining records', function (): void {
        $counts = [];

        livewire(PostsTableWithAuthorState::class)
            ->assertTableQueryCountDoesNotGrowWithRecords(function (int $count) use (&$counts): void {
                $counts[] = $count;

                Post::factory()->count($count)->create();
            });

        expect($counts)->toBe([3, 17])
            ->and(Post::query()->count())->toBe(20);
    });

    it('can customize the small and large record counts', function (): void {
        $counts = [];

        livewire(PostsTableWithAuthorState::class)
            ->assertTableQueryCountDoesNotGrowWithRecords(
                function (int $count) use (&$counts): void {
                    $counts[] = $count;

                    Post::factory()->count($count)->create();
                },
                smallRecordCount: 2,
                largeRecordCount: 5,
            );

        expect($counts)->toBe([2, 3])
            ->and(Post::query()->count())->toBe(5);
    });

    it('detects an N+1 query with custom record counts', function (): void {
        expect(fn () => livewire(PostsTableWithAuthorState::class, ['eagerLoadAuthor' => false])
            ->assertTableQueryCountDoesNotGrowWithRecords(
                fn (int $count) => Post::factory()->count($count)->create(),
                smallRecordCount: 2,
                largeRecordCount: 4,
            ))->toThrow(ExpectationFailedException::class);
    });
});

describe('`assertTableHasNoLazyLoading()`', function (): void {
    it('passes when relationships are eager loaded', function (): void {
        Post::factory()->count(3)->create();

        livewire(PostsTableWithAuthorState::class)
            ->assertTableHasNoLazyLoading();
    });

    it('fails and names the lazy loaded relationship', function (): void {
        Post::factory()->count(3)->create();

        $component = livewire(PostsTableWithAuthorState::class, ['eagerLoadAuthor' => false]);

        expect(fn () => $component->assertTableHasNoLazyLoading())
            ->toThrow(ExpectationFailedException::class, 'The following relationships were lazy loaded: [' . Post::class . '::author].');
    });

    it('includes the Livewire class in the failure message', function (): void {
        Post::factory()->count(3)->create();

        $component = livewire(PostsTableWithAuthorState::class, ['eagerLoadAuthor' => false]);

        expect(fn () => $component->assertTableHasNoLazyLoading())
            ->toThrow(ExpectationFailedException::class, 'Failed asserting that the [' . PostsTableWithAuthorState::class . '] table renders without lazy loading relationships.');
    });

    it('restores lazy loading prevention when it was disabled', function (): void {
        Model::preventLazyLoading(false);

        Post::factory()->count(3)->create();

        $component = livewire(PostsTableWithAuthorState::class, ['eagerLoadAuthor' => false]);

        try {
            $component->assertTableHasNoLazyLoading();
        } catch (ExpectationFailedException) {
        }

        expect(Model::preventsLazyLoading())->toBeFalse();
    });

    it('restores lazy loading prevention when it was enabled', function (): void {
        $wasPreventingLazyLoading = Model::preventsLazyLoading();

        Model::preventLazyLoading();

        try {
            Post::factory()->count(3)->create();

            livewire(PostsTableWithAuthorState::class)
                ->assertTableHasNoLazyLoading();

            expect(Model::preventsLazyLoading())->toBeTrue();
        } finally {
            Model::preventLazyLoading($wasPreventingLazyLoading);
        }
    });

    it('restores the previous lazy loading violation callback', function (): void {
        $wasPreventingLazyLoading = Model::preventsLazyLoading();

        $violations = [];

        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) use (&$violations): void {
            $violations[] = $relation;
        });

        try {
            Post::factory()->count(3)->create();

            $component = livewire(PostsTableWithAuthorState::class, ['eagerLoadAuthor' => false]);

            try {
                $component->assertTableHasNoLazyLoading();
            } catch (ExpectationFailedException) {
            }

            expect($violations)->toBeEmpty();

            Model::preventLazyLoading();

            Post::query()->get()->each(fn (Post $post) => $post->author);

            expect($violations)->not->toBeEmpty()
                ->each->toBe('author');
        } finally {
            Model::preventLazyLoading($wasPreventingLazyLoading);
            Model::handleLazyLoadingViolationUsing(null);
        }
    });
});

it('can chain the query assertions', function (): void {
    Post::factory()->count(3)->create();

    livewire(PostsTableWithAuthorState::class)
        ->assertTableRendersWithinQueries(50)
        ->assertTableHasNoLazyLoading()
        ->assertTableQueryCountDoesNotGrowWithRecords(
            fn (int $count) => Post::factory()->count($count)->create(),
        );
});
