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
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
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
});

describe('group summary rendering', function (): void {
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

describe('custom database summaries', function (): void {
    beforeEach(function (): void {
        $databaseConnection = DB::connection();

        config()->set('database.connections.native-summary', ['driver' => 'native-summary']);
        DB::extend('native-summary', static function () use ($databaseConnection): Connection {
            $connection = new class($databaseConnection->getPdo(), $databaseConnection->getDatabaseName(), $databaseConnection->getTablePrefix(), ['driver' => 'native-summary', 'name' => 'native-summary']) extends Connection
            {
                public function query()
                {
                    return new class($this, $this->getQueryGrammar(), $this->getPostProcessor()) extends Builder
                    {
                        public function from($table, $as = null)
                        {
                            if (! is_string($table)) {
                                throw new LogicException('This driver does not support SQL subqueries.');
                            }

                            return parent::from($table, $as);
                        }

                        public function selectRaw($expression, array $bindings = [])
                        {
                            throw new LogicException('This driver does not support raw SQL selections.');
                        }

                        public function toRawSql()
                        {
                            throw new LogicException('This driver does not support compiling SQL.');
                        }
                    };
                }
            };
            $connection->setQueryGrammar($databaseConnection->getQueryGrammar());
            $connection->setPostProcessor($databaseConnection->getPostProcessor());

            return $connection;
        });
    });

    it('preserves filters, scopes, page boundaries and summary callbacks without SQL subqueries', function (): void {
        foreach ([0, 2, 7, 11, 29, 53, 97] as $rating) {
            Post::factory()->create(['rating' => $rating, 'is_published' => in_array($rating, [2, 11, 53])]);
        }

        Post::factory()->create(['rating' => 199])->delete();

        $livewire = livewire(TestTableWithNativeSummaries::class)
            ->set('tableRecordsPerPage', 5)
            ->assertOk()
            ->assertTableColumnSummarySet('rating', 'sum', 199)
            ->assertTableColumnSummarySet('rating', 'average', 199 / 6)
            ->assertTableColumnSummarySet('rating', 'count', 6)
            ->assertTableColumnSummarySet('rating', 'range', [2, 97])
            ->assertTableColumnSummarySet('rating', 'published', 66)
            ->assertTableColumnSummarySet('rating', 'sum', 197, isCurrentPaginationPageOnly: true)
            ->assertTableColumnSummarySet('rating', 'average', 197 / 5, isCurrentPaginationPageOnly: true)
            ->assertTableColumnSummarySet('rating', 'count', 5, isCurrentPaginationPageOnly: true)
            ->assertTableColumnSummarySet('rating', 'range', [7, 97], isCurrentPaginationPageOnly: true)
            ->assertTableColumnSummarySet('rating', 'published', 64, isCurrentPaginationPageOnly: true)
            ->call('gotoPage', 2)
            ->assertTableColumnSummarySet('rating', 'sum', 2, isCurrentPaginationPageOnly: true)
            ->assertTableColumnSummarySet('rating', 'count', 1, isCurrentPaginationPageOnly: true)
            ->assertTableColumnSummarySet('rating', 'sum', 199);

        $iconCounts = $livewire->instance()->getTable()->getColumn('is_published')->getSummarizer('icons')
            ->query(Post::on('native-summary')->withoutGlobalScopes()->where('rating', 2)->orWhere('rating', 7))
            ->getState();

        expect(collect($iconCounts)->flatten()->all())->toBe([1, 1]);
    });

    it('preserves a selected and limited source before applying summary callbacks', function (): void {
        foreach ([2, 7, 11, 29, 53, 97] as $rating) {
            Post::factory()->create(['rating' => $rating, 'is_published' => in_array($rating, [2, 11, 53])]);
        }

        $column = livewire(TestTableWithNativeSummaries::class)->instance()->getTable()->getColumn('rating');
        $query = Post::on('native-summary')->select(['title', 'rating'])->orderBy('rating', 'desc')->offset(1)->limit(3);

        expect($column->getSummarizer('sum')->query($query)->getState())->toBe(93)
            ->and($column->getSummarizer('published')->query($query)->getState())->toBe(64)
            ->and($column->getSummarizer('range')->query($query)->getState())->toEqual([11, 53])
            ->and($column->getSummarizer('sum')->using(static fn (Builder $query): mixed => $query->max('rating') - $query->min('rating'))->getState())->toBe(42);

        expect($column->getSummarizer('sum')->using(null)->query(static fn (Builder $query): Builder => $query)->getState())->toBe(93);

        $summarizer = $column->getSummarizer('sum')->using(null)
            ->query(Post::on('native-summary')->where('is_published', true))
            ->query(static fn (Builder $query): Builder => $query->clone()->where('rating', 11)->orWhere('rating', 29));

        expect($summarizer->getState())->toBe(11);

        expect($summarizer->query(Post::on('native-summary')->withoutGlobalScopes())->query(static fn (Builder $query): Builder => $query)->getState())->toBe(199);
    });

    it('summarizes empty source windows', function (int $offset, int $limit): void {
        Post::factory()->create(['rating' => 13]);

        $column = livewire(TestTableWithNativeSummaries::class)->instance()->getTable()->getColumn('rating');
        $query = Post::on('native-summary')->withoutGlobalScopes()
            ->where('rating', 13)->orWhere('rating', 17)
            ->orderBy('id')->offset($offset)->limit($limit);

        expect($column->getSummarizer('sum')->query($query)->getState())->toBe(0)
            ->and($column->getSummarizer('average')->query($query)->getState())->toBeNull()
            ->and($column->getSummarizer('count')->query($query)->getState())->toBe(0)
            ->and($column->getSummarizer('range')->query($query)->getState())->toBe([null, null]);
    })->with([[0, 0], [10, 5]]);

    it('evaluates visibility for each native summary query', function (): void {
        Post::factory()->create(['rating' => 13]);

        $summarizer = livewire(TestTableWithNativeSummaries::class)->instance()->getTable()->getColumn('rating')->getSummarizer('sum')
            ->visible(static fn (Illuminate\Database\Eloquent\Builder $query): bool => $query->count() > 0);

        expect($summarizer->query(Post::on('native-summary')->where('rating', 13))->isVisible())->toBeTrue()
            ->and($summarizer->query(Post::on('native-summary')->where('rating', 17))->isVisible())->toBeFalse();
    });

    it('rejects transformed source queries rather than returning misleading native summaries', function (): void {
        $summarizer = livewire(TestTableWithNativeSummaries::class)->instance()->getTable()->getColumn('rating')->getSummarizer('sum');

        foreach ([
            Post::on('native-summary')->distinct(),
            Post::on('native-summary')->groupBy('rating'),
            Post::on('native-summary')->having('rating', '>', 0),
            Post::on('native-summary')->union(Post::on('native-summary')),
            Post::on('native-summary')->unionAll(Post::on('native-summary')),
        ] as $query) {
            expect(fn (): mixed => $summarizer->query($query)->getState())->toThrow(LogicException::class, 'Native database summaries do not support');
        }
    });

    it('rejects joins only when source pagination cannot preserve joined rows', function (): void {
        Post::factory()->create(['rating' => 13]);
        User::factory()->count(2)->create();

        $summarizer = livewire(TestTableWithNativeSummaries::class)->instance()->getTable()->getColumn('rating')->getSummarizer('sum');
        $query = Post::on('native-summary')->crossJoin('users')->orderBy('posts.id');

        expect($summarizer->query($query)->getState())->toBe(39)
            ->and(fn (): mixed => $summarizer->query($query->clone()->limit(1))->getState())->toThrow(LogicException::class, 'joins')
            ->and(fn (): mixed => $summarizer->query($query->clone()->offset(1))->getState())->toThrow(LogicException::class, 'joins');
    });

    it('renders summaries for ordinary and date groups without SQL grouping', function (string $group): void {
        Post::factory()->create(['title' => 'First', 'rating' => 13, 'created_at' => '2026-01-01 09:00:00']);
        Post::factory()->create(['title' => 'First', 'rating' => 17, 'created_at' => '2026-01-01 15:00:00']);
        Post::factory()->create(['title' => 'Second', 'rating' => 41, 'created_at' => '2026-01-02 09:00:00']);

        $livewire = livewire(TestTableWithNativeSummaries::class)->set('tableGrouping', $group)->assertOk();
        $table = $livewire->instance()->getTable();
        $query = $table->getGrouping()->scopeQuery($livewire->instance()->getAllTableSummaryQuery(), Post::query()->oldest('id')->first());

        expect($table->getColumn('rating')->getSummarizer('sum')->query($query)->getState())->toBe(30);
    })->with(['title', 'created_at']);
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

class TestTableWithNativeSummaries extends PostsTable
{
    public function table(Table $table): Table
    {
        return $table
            ->query(Post::on('native-summary')->where('rating', '>', 0))
            ->defaultSort('id', 'desc')
            ->groups([
                Tables\Grouping\Group::make('title'),
                Tables\Grouping\Group::make('created_at')->date(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\IconColumn::make('is_published')->boolean()->summarize(
                    Tables\Columns\Summarizers\Count::make('icons')->icons(),
                ),
                Tables\Columns\TextColumn::make('rating')->summarize([
                    Tables\Columns\Summarizers\Sum::make('sum'),
                    Tables\Columns\Summarizers\Average::make('average'),
                    Tables\Columns\Summarizers\Count::make('count'),
                    Tables\Columns\Summarizers\Range::make('range'),
                    Tables\Columns\Summarizers\Sum::make('published')->query(static fn (Builder $query): Builder => $query->where('is_published', true)),
                ]),
            ]);
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
