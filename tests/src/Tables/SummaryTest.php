<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithCursorPagination;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\QueryBuilderItem;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('averages', function (): void {
    it('can average values in a column', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'average', $posts->avg('rating'));
    });

    it('can average values in a column on this pagination page', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'average', $posts->take(10)->avg('rating'), isCurrentPaginationPageOnly: true);
    });

    it('can average subset of values in a column', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'published_average', $posts->where('is_published', true)->avg('rating'));
    });

    it('can average subset of values in a column on this pagination page', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'published_average', $posts->take(10)->where('is_published', true)->avg('rating'), isCurrentPaginationPageOnly: true);
    });
});

describe('counts', function (): void {
    it('can count rows', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'count', $posts->count());
    });

    it('can count rows on this pagination page', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'count', $posts->take(10)->count(), isCurrentPaginationPageOnly: true);
    });

    it('can count subset of rows', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('is_published', 'published_count', $posts->where('is_published', true)->count());
    });

    it('can count subset of rows on this pagination page', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('is_published', 'published_count', $posts->take(10)->where('is_published', true)->count(), isCurrentPaginationPageOnly: true);
    });
});

describe('ranges', function (): void {
    it('can get the range of values in a column', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'range', [$posts->min('rating'), $posts->max('rating')]);
    });

    it('can get the range of values in a column on this pagination page', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'range', [$posts->take(10)->min('rating'), $posts->take(10)->max('rating')], isCurrentPaginationPageOnly: true);
    });

    it('can get the range of a subset of values in a column', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'published_range', [$posts->where('is_published', true)->min('rating'), $posts->where('is_published', true)->max('rating')]);
    });

    it('can get the range of a subset of values in a column on this pagination page', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'published_range', [$posts->take(10)->where('is_published', true)->min('rating'), $posts->take(10)->where('is_published', true)->max('rating')], isCurrentPaginationPageOnly: true);
    });

    it('can get the range of values from a relationship in a column', function (): void {
        $posts = Post::factory()->count(20)->create();
        $posts->each(
            static fn (Post $post, int $index) => $post->author()->update(['name' => sprintf('Author %02d', $index)]),
        );

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('author.name', 'range', [$posts->min('author.name'), $posts->max('author.name')]);
    });

    it('can get the range of values from a relationship in a column on this pagination page', function (): void {
        $posts = Post::factory()->count(20)->create();
        $posts->each(
            static fn (Post $post, int $index) => $post->author()->update(['name' => sprintf('Author %02d', $index)]),
        );

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('author.name', 'range', [$posts->take(10)->min('author.name'), $posts->take(10)->max('author.name')], isCurrentPaginationPageOnly: true);
    });

    it('can scope relationship summaries with `query()` on soft-deletable models', function (): void {
        Post::factory()
            ->for(User::factory()->state(['name' => 'Published author']), 'author')
            ->create(['is_published' => true]);
        Post::factory()
            ->for(User::factory()->state(['name' => 'Draft author']), 'author')
            ->create(['is_published' => false]);
        Post::factory()
            ->for(User::factory()->state(['name' => 'Deleted author']), 'author')
            ->trashed()
            ->create(['is_published' => true]);

        $livewire = livewire(PostsTable::class);

        // Keep `SoftDeletingScope` active instead of letting `TrashedFilter` remove it.
        $livewire->instance()->getTable()->filters([]);

        $livewire->assertTableColumnSummarySet('author.name', 'published_range', ['Published author', 'Published author']);
    });

    it('retains search, filters and page limits for relationship summaries', function (): void {
        foreach (range(1, 12) as $index) {
            Post::factory()->for(User::factory()->state(['name' => sprintf('Author %02d', $index)]), 'author')
                ->create(['title' => 'Included', 'is_published' => true]);
        }

        Post::factory()->for(User::factory()->state(['name' => 'Excluded by search']), 'author')
            ->create(['title' => 'Other', 'is_published' => true]);
        Post::factory()->for(User::factory()->state(['name' => 'Excluded by filter']), 'author')
            ->create(['title' => 'Included', 'is_published' => false]);

        livewire(PostsTable::class)
            ->searchTable('Included')
            ->filterTable('is_published')
            ->assertTableColumnSummarySet('author.name', 'range', ['Author 01', 'Author 12'])
            ->assertTableColumnSummarySet('author.name', 'range', ['Author 01', 'Author 10'], isCurrentPaginationPageOnly: true);
    });

    it('summarizes relationships from ordered table queries with and without a limit', function (): void {
        Post::factory()->for(User::factory()->state(['name' => 'First author']), 'author')->create();
        Post::factory()->for(User::factory()->state(['name' => 'Second author']), 'author')->create();

        $summarizer = Tables\Columns\Summarizers\Range::make();
        Tables\Columns\TextColumn::make('author.name')->summarize($summarizer);

        expect($summarizer->query(Post::query()->orderBy('posts.id'))->getState())
            ->toEqual(['First author', 'Second author'])
            ->and($summarizer->query(Post::query()->orderByDesc('posts.id')->limit(1))->getState())
            ->toEqual(['Second author', 'Second author']);
    });
});

describe('sums', function (): void {
    it('can sum values in a column', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'sum', $posts->sum('rating'));
    });

    it('can sum values in a column on this pagination page', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'sum', $posts->take(10)->sum('rating'), isCurrentPaginationPageOnly: true);
    });

    it('can sum subset of values in a column', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'published_sum', $posts->where('is_published', true)->sum('rating'));
    });

    it('can sum subset of values in a column on this pagination page', function (): void {
        $posts = Post::factory()->count(20)->create();

        livewire(PostsTable::class)
            ->assertCanSeeTableRecords($posts->take(10))
            ->assertTableColumnSummarySet('rating', 'published_sum', $posts->take(10)->where('is_published', true)->sum('rating'), isCurrentPaginationPageOnly: true);
    });

    it('only summarizes rows admitted by the forward relationship and table query', function (string $relationship, int $expected): void {
        $author = User::factory()->create();
        Post::factory()->for($author, 'author')->create(['rating' => 3, 'is_published' => false]);
        Post::factory()->for($author, 'author')->create(['rating' => 7, 'is_published' => true]);
        Post::factory()->for($author, 'author')->trashed()->create(['rating' => 50]);
        Post::factory()->create(['rating' => 90]);

        $summarizer = Tables\Columns\Summarizers\Sum::make();
        Tables\Columns\TextColumn::make("{$relationship}.rating")
            ->inverseRelationship('author')
            ->summarize($summarizer);

        expect($summarizer->query(User::query()->whereKey($author))->getState())->toEqual($expected);

        $summarizer->using(fn (QueryBuilder $query) => $query->sum('posts.rating'));

        expect($summarizer->getState())->toEqual($expected);
    })->with(['published posts' => ['publishedPosts', 7], 'latest post' => ['latestPost', 7]]);

    it('only summarizes pivot-constrained related rows', function (): void {
        $owner = User::factory()->create();
        $otherOwner = User::factory()->create();
        $ownedTeam = Team::factory()->create(['budget' => 13]);
        $memberTeam = Team::factory()->create(['budget' => 41]);
        $owner->teams()->attach($ownedTeam, ['role' => 'owner']);
        $owner->teams()->attach($memberTeam, ['role' => 'member']);
        $otherOwner->teams()->attach($memberTeam, ['role' => 'owner']);

        $summarizer = Tables\Columns\Summarizers\Sum::make();
        Tables\Columns\TextColumn::make('ownedTeams.budget')->summarize($summarizer);

        expect($summarizer->query(User::query()->whereKey($owner))->getState())->toEqual(13);
    });

    it('retains constraints through nested relationships without multiplying shared rows', function (): void {
        $author = User::factory()->create();
        $team = Team::factory()->create(['budget' => 13]);
        $otherTeam = Team::factory()->create(['budget' => 41]);
        $author->teams()->attach($team, ['role' => 'owner']);
        $author->teams()->attach($otherTeam, ['role' => 'member']);
        $posts = Post::factory()->for($author, 'author')->count(2)->create();

        $summarizer = Tables\Columns\Summarizers\Sum::make();
        Tables\Columns\TextColumn::make('author.ownedTeams.budget')->summarize($summarizer);

        expect($summarizer->query(Post::query()->whereKey($posts->modelKeys()))->getState())->toEqual(13);
    });

    it('summarizes the forward self-relationship without guessing its inverse', function (): void {
        $connection = QueryBuilderItem::query()->getConnection();
        $originalPrefix = $connection->getTablePrefix();
        $connection->setTablePrefix('fo_');

        try {
            $parent = QueryBuilderItem::query()->create(['length' => 100]);
            $child = $parent->children()->create(['length' => 7]);
            $child->children()->create(['length' => 29]);
            $otherParent = QueryBuilderItem::query()->create(['length' => 200]);
            $otherParent->children()->create(['length' => 61]);

            $summarizer = Tables\Columns\Summarizers\Sum::make();
            Tables\Columns\TextColumn::make('children.length')->summarize($summarizer);

            expect($summarizer->query(QueryBuilderItem::query()->whereKey($parent))->getState())->toEqual(7);
        } finally {
            $connection->setTablePrefix($originalPrefix);
        }
    });
});

describe('group summary rendering', function (): void {
    it('retains the current owner when grouping a `BelongsToMany` table by a relationship', function (): void {
        $owner = User::factory()->create();
        $otherOwner = User::factory()->create();
        $sharedTeam = Team::factory()->create(['budget' => 13]);
        $otherTeam = Team::factory()->create(['company_id' => $sharedTeam->company_id, 'budget' => 71]);
        $owner->teams()->attach($sharedTeam, ['role' => 'owner']);
        $otherOwner->teams()->attach([$sharedTeam->id, $otherTeam->id], ['role' => 'member']);

        $livewire = livewire(PostsTable::class)->instance();
        $summarizer = Tables\Columns\Summarizers\Sum::make('sum');
        $livewire->getTable()
            ->query(null)
            ->relationship(fn () => $owner->teams()->withPivot('id'))
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('budget')->summarize($summarizer),
            ])
            ->defaultGroup(Tables\Grouping\Group::make('company.name'));

        $group = $livewire->getTableGrouping();
        $query = $livewire->getTable()->getQuery();
        $state = $livewire->getTableSummarySelectedState(
            $query,
            fn ($query) => $group->groupQuery($query, app(Team::class)),
        );

        $summarizer->selectedState(array_values($state)[0]);

        expect($summarizer->getState())->toEqual(13);
    });

    it('renders summaries for groups with non-text-safe keys', function (string $groupKey): void {
        $driver = DB::connection()->getDriverName();

        if (($driver === 'pgsql') || (($driver !== 'sqlite') && ! mb_check_encoding($groupKey, 'UTF-8'))) {
            $this->markTestSkipped('The database text column does not support this group key.');
        }

        Post::factory()->create(['title' => $groupKey, 'rating' => 7]);
        Post::factory()->create(['title' => $groupKey, 'rating' => 11]);
        Post::factory()->create(['title' => 'ordinary', 'rating' => 3]);

        $livewire = livewire(TestTableWithNonTextGroupKeys::class)->assertOk();
        $column = $livewire->instance()->getTable()->getColumn('rating');
        $query = $livewire->instance()->getTableGrouping()->scopeQueryByKey(Post::query(), $groupKey);

        expect($column->getSummarizers($query))->toHaveKey('sum')
            ->and($column->getSummarizer('sum')->query($query)->selectedState([])->getState())->toEqual(18);
    })->with([
        'null byte' => ["group\0key"],
        'invalid UTF-8' => ["group\xFFkey"],
    ]);

    it('caches summary visibility by query structure and typed bindings', function (): void {
        $evaluations = 0;
        $summarizer = Tables\Columns\Summarizers\Sum::make()
            ->hidden(function () use (&$evaluations): bool {
                return (++$evaluations % 2) === 0;
            });
        $queries = [
            null,
            Post::query()->where('title', 'first'),
            Post::query()->where('title', 'second'),
            Post::query()->where('content', 'second'),
            Post::query()->where('rating', 1),
            Post::query()->where('rating', '1'),
            Post::query()->where('rating', true),
            Post::query()->where('rating', 1.0),
            Post::query()->where('rating', null),
            Post::query()->whereIn('title', ['a', 'bc']),
            Post::query()->whereIn('title', ['ab', 'c']),
            Post::query()->whereIn('title', ['bc', 'a']),
            Post::query()->where('title', "group\0key"),
            Post::query()->where('title', "group\xFFkey"),
            Post::query()->where('title', "group\xFEkey"),
            Post::query()->where('title', "quote'\\?\r\n☃"),
            Post::query()->where('created_at', new DateTimeImmutable('2026-01-02 03:04:05 UTC')),
            Post::query()->withGlobalScope('title', static fn (Builder $query) => $query->where('title', 'scoped first')),
            Post::query()->withGlobalScope('title', static fn (Builder $query) => $query->where('title', 'scoped second')),
            Post::query()->withoutGlobalScopes()->beforeQuery(static fn (QueryBuilder $query) => $query->where('rating', 7)),
            Post::query()->withoutGlobalScopes()->beforeQuery(static fn (QueryBuilder $query) => $query->where('rating', 9)),
        ];
        $connection = DB::connection();
        $connection->enableQueryLog();
        $connection->flushQueryLog();

        try {
            foreach ($queries as $index => $query) {
                $expected = ($index % 2) === 1;

                expect($summarizer->query($query)->isHidden())->toBe($expected)
                    ->and($summarizer->query($query ? clone $query : null)->isVisible())->toBe(! $expected)
                    ->and($evaluations)->toBe($index + 1);
            }

            foreach (array_reverse($queries, preserve_keys: true) as $index => $query) {
                expect($summarizer->query($query)->isHidden())->toBe(($index % 2) === 1);
            }

            expect($evaluations)->toBe(count($queries));
            expect($connection->getQueryLog())->toBeEmpty();
        } finally {
            $connection->disableQueryLog();
        }
    });

    it('does not mutate the summary query when caching visibility', function (): void {
        $query = Post::query()->withoutGlobalScopes();
        $query->getQuery()->beforeQuery(static fn (QueryBuilder $query) => $query->where('rating', 7));
        $evaluations = 0;
        $summarizer = Tables\Columns\Summarizers\Sum::make()
            ->query($query)
            ->visible(function () use (&$evaluations): bool {
                $evaluations++;

                return true;
            });

        expect($summarizer->isVisible())->toBeTrue()
            ->and($summarizer->isVisible())->toBeTrue()
            ->and($evaluations)->toBe(1)
            ->and($query->getQuery()->getBindings())->toBeEmpty()
            ->and((clone $query)->toSql())->toContain('where')
            ->and($query->getQuery()->getBindings())->toBeEmpty();
    });

    it('supports non-serializable string bindings in summary visibility queries', function (): void {
        Post::factory()->create(['title' => 'group', 'rating' => 13]);
        $binding = new class implements Stringable
        {
            public function __toString(): string
            {
                return 'group';
            }
        };
        $evaluations = 0;
        $column = Tables\Columns\TextColumn::make('rating')->summarize(
            Tables\Columns\Summarizers\Sum::make('sum')
                ->visible(function () use (&$evaluations): bool {
                    $evaluations++;

                    return true;
                }),
        );
        $query = Post::query()->where('title', $binding);

        expect($column->getSummarizers($query))->toHaveKey('sum')
            ->and($column->getSummarizer('sum')->query($query)->getState())->toEqual(13)
            ->and($column->getSummarizers(Post::query()->where('title', clone $binding)))->toHaveKey('sum')
            ->and($evaluations)->toBe(1);
    });

    it('renders group summaries when page and all-table summaries are disabled', function (): void {
        Post::factory()->count(5)->create(['is_published' => true]);

        livewire(TestTableWithGroupSummariesOnly::class)
            ->assertSeeHtml('fi-ta-summary-row')
            ->assertDontSeeHtml('fi-ta-summary-header-row');
    });

    it('renders the trailing group summary when the next page starts a different group', function (): void {
        Post::factory()->count(10)->create(['title' => 'A']);
        Post::factory()->create(['title' => 'B']);

        livewire(PostsTable::class)
            ->set('tableRecordsPerPage', 10)
            ->set('tableGrouping', 'title')
            ->assertSee('A summary')
            ->assertDontSee('B summary');
    });

    it('does not render the trailing group summary when the next page continues the same group', function (): void {
        Post::factory()->count(15)->create(['title' => 'A']);

        livewire(PostsTable::class)
            ->set('tableRecordsPerPage', 10)
            ->set('tableGrouping', 'title')
            ->assertDontSee('A summary');
    });

    it('renders the trailing group summary with cursor pagination when the next page starts a different group', function (): void {
        Post::factory()->count(10)->create(['title' => 'A']);
        Post::factory()->create(['title' => 'B']);

        livewire(PostsTableWithCursorPagination::class)
            ->set('tableRecordsPerPage', 10)
            ->set('tableGrouping', 'title')
            ->assertSee('A summary')
            ->assertDontSee('B summary');
    });

    it('does not render the trailing group summary with cursor pagination when the next page continues the same group', function (): void {
        Post::factory()->count(15)->create(['title' => 'A']);

        livewire(PostsTableWithCursorPagination::class)
            ->set('tableRecordsPerPage', 10)
            ->set('tableGrouping', 'title')
            ->assertDontSee('A summary');
    });
});

describe('responsive summary rendering', function (): void {
    it('renders summaries with unsupported responsive breakpoints', function (): void {
        Post::factory()->create();

        livewire(TestTableWithResponsiveSummaryFallbacks::class)
            ->assertOk();
    });

    it('renders summaries when all columns use `hidden()`', function (): void {
        Post::factory()->create();

        livewire(TestTableWithResponsiveSummaryFallbacks::class)
            ->set('hideAllColumns', true)
            ->assertOk();
    });
});

describe('custom data summaries', function (): void {
    it('discovers configured summaries without evaluating visibility when no query is supplied', function (): void {
        $summarizer = Tables\Columns\Summarizers\Summarizer::make('summary')
            ->visible(fn (Builder $query) => throw new LogicException('Structural discovery evaluated visibility'));
        $hiddenSummarizer = Tables\Columns\Summarizers\Summarizer::make('hidden')->hidden();
        $column = Tables\Columns\TextColumn::make('rating')->summarize([$summarizer, $hiddenSummarizer]);

        expect($column->getSummarizers())->toBe(['summary' => $summarizer, 'hidden' => $hiddenSummarizer])
            ->and($column->getSummarizers(null))->toBe($column->getSummarizers())
            ->and($column->getSummarizer('summary'))->toBe($summarizer)
            ->and($column->getSummarizer('hidden'))->toBe($hiddenSummarizer)
            ->and($column->hasSummary())->toBeTrue()
            ->and($column->hasSummary(null))->toBeTrue()
            ->and($column->getVisibleSummarizers(null))->toBe([]);
    });

    it('does not execute or render hidden summarizers without a query', function (): void {
        livewire(TestTableWithCustomDataSummaries::class)
            ->assertSee('Visible custom summary')
            ->assertSee('Nullable query summary')
            ->assertSee('Typed component summary')
            ->assertDontSee('Hidden custom summary')
            ->assertDontSee('Invisible custom summary')
            ->assertDontSee('Required query summary');
    });

    it('does not render summary rows when all custom-data summarizers are hidden', function (): void {
        Tables\Columns\Summarizers\Summarizer::configureUsing(
            fn (Tables\Columns\Summarizers\Summarizer $summarizer) => $summarizer->hidden(),
            during: function (): void {
                $livewire = livewire(TestTableWithCustomDataSummaries::class)
                    ->assertDontSeeHtml('fi-ta-summary-row');

                expect($livewire->instance()->getTable()->hasSummary(null))->toBeTrue();
            },
        );
    });

    it('preserves summarizer lookup, typed visibility and query callbacks when the query is null', function (): void {
        Post::factory()->create(['is_published' => true, 'rating' => 7]);
        Post::factory()->create(['is_published' => false, 'rating' => 13]);
        $summarizer = Tables\Columns\Summarizers\Summarizer::make('summary')
            ->visible(fn (?Builder $query): bool => $query === null)
            ->query(fn ($query) => $query)
            ->using(fn ($query): int => $query?->sum('rating') ?? 17);
        $column = Tables\Columns\TextColumn::make('rating')->summarize($summarizer);

        expect($column->getVisibleSummarizers(Post::query()))->toBe([])
            ->and($column->getVisibleSummarizers(null))->toBe(['summary' => $summarizer])
            ->and($column->getSummarizers(Post::query()))->toBe([])
            ->and($column->getSummarizers())->toBe(['summary' => $summarizer])
            ->and($column->getSummarizers(null))->toBe($column->getSummarizers())
            ->and($column->hasSummary(null))->toBeTrue()
            ->and($column->getSummarizer('summary'))->toBe($summarizer)
            ->and($summarizer->hasQueryModificationCallback())->toBeTrue()
            ->and($summarizer->getState())->toBe(20)
            ->and($column->getSummarizers(fn ($query) => $query->where('is_published', true)))->toBe([])
            ->and($summarizer->getState())->toBe(7);
    });
});

class TestTableWithCustomDataSummaries extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): array => [1 => ['name' => 'Item', 'amount' => 7]])
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('amount')->summarize([
                    Tables\Columns\Summarizers\Summarizer::make('visible')
                        ->label('Visible custom summary')
                        ->visible(fn (): bool => true)
                        ->using(fn ($query): int => $query === null ? 7 : 0),
                    Tables\Columns\Summarizers\Summarizer::make('nullable')
                        ->label('Nullable query summary')
                        ->visible(fn (?Builder $query): bool => $query === null)
                        ->using(fn (): int => 11),
                    Tables\Columns\Summarizers\Summarizer::make('component')
                        ->label('Typed component summary')
                        ->visible(fn (self $livewire): bool => $livewire instanceof self)
                        ->using(fn (): int => 19),
                    Tables\Columns\Summarizers\Summarizer::make('required_query')
                        ->label('Required query summary')
                        ->visible(fn (Builder $query): bool => $query->exists())
                        ->using(fn () => throw new LogicException('Query-dependent summarizer executed')),
                    Tables\Columns\Summarizers\Summarizer::make('hidden')
                        ->label('Hidden custom summary')
                        ->hidden()
                        ->using(fn () => throw new LogicException('Hidden summarizer executed')),
                    Tables\Columns\Summarizers\Summarizer::make('invisible')
                        ->label('Invisible custom summary')
                        ->visible(false)
                        ->using(fn () => throw new LogicException('Invisible summarizer executed')),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithNonTextGroupKeys extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->groups([
                Tables\Grouping\Group::make('title')
                    ->getTitleFromRecordUsing(static fn (Post $record): string => bin2hex($record->title)),
            ])
            ->defaultGroup('title');
    }
}

class TestTableWithResponsiveSummaryFallbacks extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public bool $hideAllColumns = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->hidden(fn (): bool => $this->hideAllColumns)
                    ->visibleFrom('unknown'),
                Tables\Columns\TextColumn::make('rating')
                    ->hidden(fn (): bool => $this->hideAllColumns)
                    ->hiddenFrom('base')
                    ->summarize(Tables\Columns\Summarizers\Count::make()),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithGroupSummariesOnly extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('rating')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make('sum'),
                    ]),
            ])
            ->defaultGroup(
                Tables\Grouping\Group::make('is_published'),
            )
            ->summaries(pageCondition: false, allTableCondition: false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
