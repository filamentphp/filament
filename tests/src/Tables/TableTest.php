<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Facades\FilamentView;
use Filament\Tables;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Tables\View\TablesRenderHook;
use Filament\Tests\Fixtures\Livewire\TicketDepartmentsReorderableTable;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\TableRenderHooksBrowserTest;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('striping', function (): void {
    it('defaults `isStriped()` to `false`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->isStriped())->toBeFalse();
    });

    it('can set `striped()`', function (): void {
        $table = livewire(StripedTableTestComponent::class)->instance()->getTable();

        expect($table->isStriped())->toBeTrue();
    });
});

describe('stacked on mobile', function (): void {
    it('defaults `isStackedOnMobile()` to `false`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->isStackedOnMobile())->toBeFalse();
    });
});

describe('lazy loading', function (): void {
    it('defaults `isLazyLoaded()` to `false`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->isLazyLoaded())->toBeFalse();
    });

    it('can enable and conditionally disable lazy loading', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable()->lazyLoaded();

        expect($table->isLazyLoaded())->toBeTrue()
            ->and($table->lazyLoaded(false)->isLazyLoaded())->toBeFalse()
            ->and($table->lazyLoaded()->lazyLoaded(static fn (): bool => false)->isLazyLoaded())->toBeFalse();
    });
});

describe('loading skeleton', function (): void {
    it('defaults `hasLoadingSkeleton()` to `false`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->hasLoadingSkeleton())->toBeFalse();
    });

    it('can set and evaluate `loadingSkeleton()`', function (): void {
        $table = livewire(LoadingSkeletonTableTestComponent::class)->instance()->getTable();

        expect($table->hasLoadingSkeleton())
            ->toBeTrue()
            ->and($table->loadingSkeleton(static fn (): bool => false)->hasLoadingSkeleton())
            ->toBeFalse();
    });

    it('can configure `loadingSkeleton()` globally', function (): void {
        Table::configureUsing(
            static fn (Table $table): Table => $table->loadingSkeleton(),
            function (): void {
                $table = livewire(TableTestComponent::class)->instance()->getTable();

                expect($table->hasLoadingSkeleton())->toBeTrue();
            },
        );
    });

    it('targets direct property updates and methods that change the displayed records', function (): void {
        expect(Table::LOADING_TARGETS)
            ->toContain('activeTab')
            ->toContain('resetTableColumnSearch')
            ->toContain('resetTableSearch')
            ->toContain('tableSort')
            ->toContain('setPage');
    });
});

describe('polling', function (): void {
    it('returns `null` for `getPollingInterval()` by default', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getPollingInterval())->toBeNull();
    });
});

describe('heading and description', function (): void {
    it('returns `null` for `getHeading()` by default', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getHeading())->toBeNull();
    });

    it('can set `heading()`', function (): void {
        $table = livewire(HeadingTableTestComponent::class)->instance()->getTable();

        expect($table->getHeading())->toBe('Posts');
    });

    it('returns `null` for `getDescription()` by default', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getDescription())->toBeNull();
    });

    it('can set `description()`', function (): void {
        $table = livewire(HeadingTableTestComponent::class)->instance()->getTable();

        expect($table->getDescription())->toBe('All blog posts');
    });
});

describe('empty state', function (): void {
    it('returns a default `getEmptyStateHeading()`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getEmptyStateHeading())->toBeString();
        expect((string) $table->getEmptyStateHeading())->not->toBeEmpty();
    });

    it('can set `emptyStateHeading()`', function (): void {
        $table = livewire(EmptyStateTableTestComponent::class)->instance()->getTable();

        expect($table->getEmptyStateHeading())->toBe('No posts yet');
    });

    it('can set `emptyStateDescription()`', function (): void {
        $table = livewire(EmptyStateTableTestComponent::class)->instance()->getTable();

        expect($table->getEmptyStateDescription())->toBe('Create your first post.');
    });
});

describe('query string identifier', function (): void {
    it('returns `null` for `getQueryStringIdentifier()` by default', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getQueryStringIdentifier())->toBeNull();
    });

    it('can set `queryStringIdentifier()`', function (): void {
        $table = livewire(QueryStringTableTestComponent::class)->instance()->getTable();

        expect($table->getQueryStringIdentifier())->toBe('posts');
    });

    it('falls back to `identifier()` for `getQueryStringIdentifier()`', function (): void {
        $table = livewire(PersistedTableTestComponent::class, ['tableIdentifier' => 'posts'])->instance()->getTable();

        expect($table->getIdentifier())->toBe('posts')
            ->and($table->getQueryStringIdentifier())->toBe('posts');
    });

    it('can override `identifier()` with `queryStringIdentifier()`', function (): void {
        $table = livewire(IdentifiedQueryStringTableTestComponent::class)->instance()->getTable();

        expect($table->getIdentifier())->toBe('persisted-posts')
            ->and($table->getQueryStringIdentifier())->toBe('url-posts');
    });

    it('can evaluate and unset `identifier()`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        $table->identifier(static fn (): string => 'posts');

        expect($table->getIdentifier())->toBe('posts');

        $table->identifier(null);

        expect($table->getIdentifier())->toBeNull();
    });
});

describe('headings', function (): void {
    it('defaults `getRootHeadingLevel()` to `2`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getRootHeadingLevel())->toBe(2);
    });

    it('can set `rootHeadingLevel()`', function (): void {
        $table = livewire(HeadingLevelTableTestComponent::class)->instance()->getTable();

        expect($table->getRootHeadingLevel())->toBe(3);
    });

    it('computes `getHeadingTag()` from the heading level', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getHeadingTag())->toBe('h2');
        expect($table->getHeadingTag(1))->toBe('h3');
    });

    it('clamps `getHeadingLevel()` to a minimum of `1`', function (int $rootHeadingLevel): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable()
            ->rootHeadingLevel($rootHeadingLevel);

        expect($table->getHeadingLevel())->toBe(1)
            ->and($table->getHeadingTag())->toBe('h1');
    })->with([0, -1]);
});

describe('record URL', function (): void {
    it('reports `hasCustomRecordUrl()` as `false` by default', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->hasCustomRecordUrl())->toBeFalse();
    });

    it('reports `hasCustomRecordUrl()` as `true` when set', function (): void {
        $table = livewire(RecordUrlTableTestComponent::class)->instance()->getTable();

        expect($table->hasCustomRecordUrl())->toBeTrue();
    });
});

describe('content grid', function (): void {
    it('returns `null` for `getContentGrid()` by default', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getContentGrid())->toBeNull();
    });
});

describe('sorting', function (): void {
    it('ignores a persisted sort on a toggled-hidden column until it is shown again', function (string $sortColumn): void {
        $firstUser = User::factory()->create(['name' => 'Charlie', 'email' => 'bravo@example.com']);
        $secondUser = User::factory()->create(['name' => 'Alpha', 'email' => 'charlie@example.com']);
        $thirdUser = User::factory()->create(['name' => 'Bravo', 'email' => 'alpha@example.com']);

        Post::factory()->count(2)->for($firstUser, 'author')->create();
        Post::factory()->for($thirdUser, 'author')->create();

        $component = livewire(ToggleableSortableTableTestComponent::class)
            ->sortTable($sortColumn)
            ->assertCanSeeTableRecords([$secondUser, $thirdUser, $firstUser], inOrder: true);

        $tableColumns = array_map(static function (array $column) use ($sortColumn): array {
            if ($column['name'] === $sortColumn) {
                $column['isToggled'] = false;
            }

            return $column;
        }, $component->get('tableColumns'));

        $component
            ->call('applyTableColumnManager', $tableColumns)
            ->assertCanSeeTableRecords([$thirdUser, $firstUser, $secondUser], inOrder: true);

        livewire(ToggleableSortableTableTestComponent::class)
            ->assertSet('tableSort', "{$sortColumn}:asc")
            ->assertCanSeeTableRecords([$thirdUser, $firstUser, $secondUser], inOrder: true)
            ->call('resetTableColumnManager')
            ->assertCanSeeTableRecords([$secondUser, $thirdUser, $firstUser], inOrder: true);
    })->with(['name', 'posts_count']);

    it('ignores a persisted custom-data sort on a toggled-hidden column until it is shown again', function (bool $usesSortTuple): void {
        $component = livewire(ToggleableSortableCustomDataTableTestComponent::class, ['usesSortTuple' => $usesSortTuple])
            ->sortTable('title', 'desc')
            ->assertCanSeeTableRecords([2, 1, 3], inOrder: true);

        $tableColumns = $component->get('tableColumns');
        $tableColumns[0]['isToggled'] = false;

        $component
            ->call('applyTableColumnManager', $tableColumns)
            ->assertCanSeeTableRecords([1, 2, 3], inOrder: true);

        livewire(ToggleableSortableCustomDataTableTestComponent::class, ['usesSortTuple' => $usesSortTuple])
            ->assertSet('tableSort', 'title:desc')
            ->assertCanSeeTableRecords([1, 2, 3], inOrder: true)
            ->call('resetTableColumnManager')
            ->assertCanSeeTableRecords([2, 1, 3], inOrder: true);
    })->with([
        'separate `$sortColumn` and `$sortDirection` injections' => [false],
        'combined `$sort` injection' => [true],
    ]);

    it('preserves selected mapped sorting for a toggled-hidden `defaultSort()` column', function (): void {
        $firstPost = Post::factory()->create(['title' => 'Charlie']);
        $secondPost = Post::factory()->create(['title' => 'Alpha']);

        livewire(MappedDefaultSortTableTestComponent::class)
            ->assertCanSeeTableRecords([$secondPost, $firstPost], inOrder: true)
            ->set('tableSort', 'display_title:desc')
            ->assertCanSeeTableRecords([$firstPost, $secondPost], inOrder: true);
    });

    it('applies `defaultSort()` to a toggled-hidden aggregate column', function (bool $usesDefaultSortClosure, string $direction): void {
        $firstUser = User::factory()->create(['name' => 'Charlie']);
        $secondUser = User::factory()->create(['name' => 'Alpha']);
        $thirdUser = User::factory()->create(['name' => 'Bravo']);

        Post::factory()->count(2)->for($firstUser, 'author')->create();
        Post::factory()->for($thirdUser, 'author')->create();

        $expectedUsers = $direction === 'desc'
            ? [$firstUser, $thirdUser, $secondUser]
            : [$secondUser, $thirdUser, $firstUser];

        $component = livewire(ToggleableSortableTableTestComponent::class, [
            'defaultSortColumn' => 'posts_count',
            'defaultSortDirection' => $direction,
            'usesDefaultSortClosure' => $usesDefaultSortClosure,
        ]);

        $tableColumns = $component->get('tableColumns');
        $tableColumns[1]['isToggled'] = false;

        $component
            ->call('applyTableColumnManager', $tableColumns)
            ->assertCanSeeTableRecords($expectedUsers, inOrder: true);
    })->with([
        'string ascending' => [false, 'asc'],
        'string descending' => [false, 'desc'],
        'Closure ascending' => [true, 'asc'],
        'Closure descending' => [true, 'desc'],
    ]);

    it('preserves the selected direction when a `defaultSort()` aggregate column is toggled off', function (string $direction): void {
        $firstUser = User::factory()->create(['name' => 'Charlie']);
        $secondUser = User::factory()->create(['name' => 'Alpha']);
        $thirdUser = User::factory()->create(['name' => 'Bravo']);

        Post::factory()->count(2)->for($firstUser, 'author')->create();
        Post::factory()->for($thirdUser, 'author')->create();

        $expectedUsers = $direction === 'desc'
            ? [$firstUser, $thirdUser, $secondUser]
            : [$secondUser, $thirdUser, $firstUser];

        $component = livewire(ToggleableSortableTableTestComponent::class, [
            'defaultSortColumn' => 'posts_count',
        ])->sortTable('posts_count', $direction);

        $tableColumns = $component->get('tableColumns');
        $tableColumns[1]['isToggled'] = false;

        $component
            ->call('applyTableColumnManager', $tableColumns)
            ->assertCanSeeTableRecords($expectedUsers, inOrder: true);
    })->with(['asc', 'desc']);
});

describe('pagination', function (): void {
    it('enforces pagination options by default and can evaluate an opt-out condition', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->enforcesPaginationPageOptions())->toBeTrue();

        $table->enforcePaginationPageOptions(static fn (): bool => false);

        expect($table->enforcesPaginationPageOptions())->toBeFalse();

        $table->enforcePaginationPageOptions();

        expect($table->enforcesPaginationPageOptions())->toBeTrue();
    });

    it('allows opting out to preserve unrestricted page sizes', function (): void {
        $posts = Post::factory()->count(13)->create();

        Table::configureUsing(
            static fn (Table $table): Table => $table
                ->enforcePaginationPageOptions(false)
                ->defaultSort('id'),
            during: function () use ($posts): void {
                livewire(TableTestComponent::class)
                    ->set('tableRecordsPerPage', 7)
                    ->assertSet('tableRecordsPerPage', 7)
                    ->assertCanSeeTableRecords($posts->take(7), inOrder: true)
                    ->assertCanNotSeeTableRecords($posts->skip(7));
            },
        );
    });

    it('keeps the browser page-size selector within the configured options by default', function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        Post::query()->delete();

        foreach (range(1, 13) as $number) {
            Post::factory()->create(['title' => sprintf('Pagination item %02d', $number)]);
        }

        $selector = 'nav select:visible';
        $page = visit(TableRenderHooksBrowserTest::getUrl(isAbsolute: false))
            ->assertValue($selector, '10')
            ->assertSee('Pagination item 10')
            ->assertDontSee('Pagination item 11')
            ->select($selector, '5')
            ->assertSee('Pagination item 05')
            ->assertDontSee('Pagination item 06');

        $page->script(<<<'JS'
            document.querySelectorAll('nav select').forEach(select => {
                select.add(new Option('Unlisted size', '1000000'))
            })
            JS);

        $page
            ->select($selector, '1000000')
            ->assertValue($selector, '5')
            ->assertSee('Pagination item 05')
            ->assertDontSee('Pagination item 06')
            ->select($selector, '25')
            ->assertSee('Pagination item 13')
            ->assertNoAccessibilityIssues();

        visit(TableRenderHooksBrowserTest::getUrl(isAbsolute: false))
            ->inDarkMode()
            ->assertValue($selector, '25')
            ->assertSee('Pagination item 13')
            ->assertNoAccessibilityIssues();
    });

    it('enforces configured page sizes before rendering and persisting updates', function (PaginationMode $mode, bool $persistsInSession): void {
        $posts = Post::factory()->count(13)->create();

        Table::configureUsing(
            static fn (Table $table): Table => $table
                ->paginationPageOptions(static fn (): array => [3, 8])
                ->defaultPaginationPageOption(8)
                ->paginationMode($mode)
                ->persistRecordsPerPageInSession($persistsInSession)
                ->defaultSort('id'),
            during: function () use ($posts, $persistsInSession): void {
                $component = livewire(TableTestComponent::class);
                $sessionKey = $component->instance()->getTablePerPageSessionKey();

                foreach ([7, 'all', 1000000, null, true, []] as $pageSize) {
                    $component
                        ->set('tableRecordsPerPage', $pageSize)
                        ->assertSuccessful()
                        ->assertSet('tableRecordsPerPage', 8)
                        ->assertCanSeeTableRecords($posts->take(8), inOrder: true)
                        ->assertCanNotSeeTableRecords($posts->skip(8));

                    expect(session()->has($sessionKey))->toBe($persistsInSession);

                    if ($persistsInSession) {
                        expect(session()->get($sessionKey))->toBe(8);
                    }
                }

                $component
                    ->set('tableRecordsPerPage', '3')
                    ->assertSet('tableRecordsPerPage', '3')
                    ->assertCanSeeTableRecords($posts->take(3), inOrder: true)
                    ->assertCanNotSeeTableRecords($posts->skip(3));

                livewire(TableTestComponent::class)
                    ->assertSet('tableRecordsPerPage', $persistsInSession ? '3' : 8);
            },
        );
    })->with([PaginationMode::Default, PaginationMode::Simple, PaginationMode::Cursor])->with([true, false]);

    it('allows `all` when it is explicitly listed', function (): void {
        $posts = Post::factory()->count(13)->create();

        Table::configureUsing(
            static fn (Table $table): Table => $table
                ->enforcePaginationPageOptions()
                ->paginated([3, 'all'])
                ->defaultSort('id'),
            during: function () use ($posts): void {
                livewire(TableTestComponent::class)
                    ->set('tableRecordsPerPage', 'all')
                    ->assertSet('tableRecordsPerPage', 'all')
                    ->assertCanSeeTableRecords($posts, inOrder: true);

                livewire(TableTestComponent::class)
                    ->assertSet('tableRecordsPerPage', 'all')
                    ->assertCanSeeTableRecords($posts, inOrder: true);
            },
        );
    });

    it('uses a fixed PHP page size when no pagination options are listed', function (string $componentClass, ?int $defaultPageSize, int $expectedPageSize): void {
        $posts = Post::factory()->count(13)->create();

        Table::configureUsing(
            static fn (Table $table): Table => $table
                ->enforcePaginationPageOptions()
                ->paginationPageOptions([])
                ->defaultPaginationPageOption($defaultPageSize)
                ->defaultSort('id'),
            during: function () use ($posts, $componentClass, $expectedPageSize): void {
                $component = livewire($componentClass)
                    ->assertSet('tableRecordsPerPage', $expectedPageSize);
                $sessionKey = $component->instance()->getTablePerPageSessionKey();

                foreach (['all', 11, null] as $pageSize) {
                    $component
                        ->set('tableRecordsPerPage', $pageSize)
                        ->assertSuccessful()
                        ->assertSet('tableRecordsPerPage', $expectedPageSize)
                        ->assertCanSeeTableRecords($posts->take($expectedPageSize), inOrder: true)
                        ->assertCanNotSeeTableRecords($posts->skip($expectedPageSize));

                    expect(session()->get($sessionKey))->toBe($expectedPageSize);
                }

                session()->put($sessionKey, 1000000);

                livewire($componentClass)
                    ->assertSet('tableRecordsPerPage', $expectedPageSize)
                    ->assertCanSeeTableRecords($posts->take($expectedPageSize), inOrder: true)
                    ->assertCanNotSeeTableRecords($posts->skip($expectedPageSize));
            },
        );
    })->with([
        'configured default' => [TableTestComponent::class, 7, 7],
        'initialized Livewire property' => [FixedPageSizeTableTestComponent::class, null, 7],
        'configured default overrides the initial property' => [FixedPageSizeTableTestComponent::class, 8, 8],
        'no PHP size specified' => [TableTestComponent::class, null, 10],
    ]);

    it('prevents client updates to the initial PHP page size', function (): void {
        $component = livewire(FixedPageSizeTableTestComponent::class);

        expect(fn () => $component->set('initialTableRecordsPerPage', 'all'))
            ->toThrow(CannotUpdateLockedPropertyException::class);
    });

    it('does not reuse an earlier client selection when a table becomes fixed-size', function (bool $startsEnforced): void {
        $posts = Post::factory()->count(13)->create();
        $enforcesOptions = $startsEnforced;
        $hasOptions = $startsEnforced;

        Table::configureUsing(
            static function (Table $table) use (&$enforcesOptions, &$hasOptions, $startsEnforced): Table {
                return $table
                    ->enforcePaginationPageOptions(static function () use (&$enforcesOptions): bool {
                        return $enforcesOptions;
                    })
                    ->paginationPageOptions(static function () use (&$hasOptions): array {
                        return $hasOptions ? [7, 'all'] : [];
                    })
                    ->defaultPaginationPageOption($startsEnforced ? 7 : null)
                    ->defaultSort('id');
            },
            during: function () use ($posts, &$enforcesOptions, &$hasOptions): void {
                $component = livewire(FixedPageSizeTableTestComponent::class)
                    ->set('tableRecordsPerPage', 'all')
                    ->assertCanSeeTableRecords($posts, inOrder: true);

                $enforcesOptions = true;
                $hasOptions = false;

                $component
                    ->set('tableRecordsPerPage', 8)
                    ->assertSet('tableRecordsPerPage', 7)
                    ->assertCanSeeTableRecords($posts->take(7), inOrder: true)
                    ->assertCanNotSeeTableRecords($posts->skip(7));
            },
        );
    })->with([
        'removing the options' => true,
        'enabling enforcement' => false,
    ]);

    it('validates session page sizes against the configured options', function (mixed $pageSize, int | string $expectedPageSize): void {
        Table::configureUsing(
            static fn (Table $table): Table => $table
                ->enforcePaginationPageOptions()
                ->paginated([3, 8])
                ->defaultPaginationPageOption(8),
            during: function () use ($pageSize, $expectedPageSize): void {
                $component = livewire(TableTestComponent::class);
                $sessionKey = $component->instance()->getTablePerPageSessionKey();
                session()->put($sessionKey, $pageSize);

                livewire(TableTestComponent::class)
                    ->assertSet('tableRecordsPerPage', $expectedPageSize);
            },
        );
    })->with([
        'numeric string' => ['3', '3'],
        'off-list value' => [7, 3],
        'boolean' => [true, 3],
    ]);

    it('respects scoped bounds when selecting the current page with `allowDuplicates()`', function (bool $usesOffset): void {
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->create();
        foreach ([3, 5, 7] as $quantity) {
            $ticket->departments()->attach($department, ['quantity' => $quantity]);
        }

        $expectedQuantity = $usesOffset ? 5 : 3;
        $expectedKey = DB::table('department_ticket')->where('quantity', $expectedQuantity)->value('id');

        Table::configureUsing(
            static fn (Table $table): Table => $table
                ->paginationPageOptions([10])
                ->defaultSort('department_ticket.id')
                ->selectCurrentPageOnly()
                ->modifyQueryUsing(static fn (Builder $query): Builder => $query->withGlobalScope(
                    'selectionBounds',
                    static fn (Builder $query): Builder => $query->limit(1)->offset($usesOffset ? 1 : 0),
                )),
            during: function () use ($ticket, $expectedQuantity, $expectedKey): void {
                $component = livewire(TicketDepartmentsReorderableTable::class, [
                    'ticketKey' => $ticket->getKey(),
                    'duplicates' => true,
                    'isPaginated' => true,
                ]);

                expect($component->instance()->getTableRecords()->pluck('quantity')->all())->toBe([$expectedQuantity]);
                expect($component->instance()->getAllSelectableTableRecordKeys())->toBe([(string) $expectedKey]);
            },
        );
    })->with([false, true]);

    it('uses pivot keys when selecting records across cursor pages with `allowDuplicates()`', function (bool $sortsByPivotKey): void {
        $ticket = Ticket::factory()->create();
        $otherTicket = Ticket::factory()->create();
        $department = Department::factory()->create();
        $otherTicket->departments()->attach($department);
        $otherTicket->departments()->attach($department);
        foreach ([3, 5, 7] as $quantity) {
            $ticket->departments()->attach($department, ['quantity' => $quantity]);
        }

        $expectedKey = DB::table('department_ticket')->where('quantity', 5)->value('id');

        Table::configureUsing(
            static fn (Table $table): Table => $table
                ->paginationPageOptions([1])
                ->paginationMode(PaginationMode::Cursor)
                ->selectCurrentPageOnly()
                ->defaultSort($sortsByPivotKey ? 'department_ticket.id' : null),
            during: function () use ($ticket, $expectedKey): void {
                $component = livewire(TicketDepartmentsReorderableTable::class, [
                    'ticketKey' => $ticket->getKey(),
                    'duplicates' => true,
                    'isPaginated' => true,
                ]);
                $records = $component->instance()->getTableRecords();
                expect($records->pluck('quantity')->all())->toBe([3]);

                $component->call('setPage', $records->nextCursor()->encode(), $component->instance()->getTablePaginationPageName());

                expect($component->instance()->getTableRecords()->pluck('quantity')->all())->toBe([5]);
                expect($component->instance()->getAllSelectableTableRecordKeys())->toBe([(string) $expectedKey]);
            },
        );
    })->with([false, true]);
});

describe('session persistence', function (): void {
    it('can toggle all session persistence with `persistInSession()`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->persistsRecordsPerPageInSession())->toBeTrue();

        $table->persistInSession(false);

        expect($table->persistsFiltersInSession())->toBeFalse()
            ->and($table->persistsSearchInSession())->toBeFalse()
            ->and($table->persistsColumnSearchesInSession())->toBeFalse()
            ->and($table->persistsSortInSession())->toBeFalse()
            ->and($table->persistsGroupInSession())->toBeFalse()
            ->and($table->persistsColumnsInSession())->toBeFalse()
            ->and($table->persistsRecordsPerPageInSession())->toBeFalse();

        $table->persistInSession();

        expect($table->persistsFiltersInSession())->toBeTrue()
            ->and($table->persistsSearchInSession())->toBeTrue()
            ->and($table->persistsColumnSearchesInSession())->toBeTrue()
            ->and($table->persistsSortInSession())->toBeTrue()
            ->and($table->persistsGroupInSession())->toBeTrue()
            ->and($table->persistsColumnsInSession())->toBeTrue()
            ->and($table->persistsRecordsPerPageInSession())->toBeTrue();
    });

    it('uses SHA-256 scopes and the identifier for every table session key while preserving tenant scoping', function (): void {
        /** @var PersistedTableTestComponent $defaultTable */
        $defaultTable = livewire(PersistedTableTestComponent::class)->instance();
        $defaultNamespace = $defaultTable::class;

        expect($defaultTable->getTablePerPageSessionKey())->toBe('filament.tables.' . hash('sha256', $defaultNamespace) . '.per_page')
            ->and($defaultTable->getTableSearchSessionKey())->toBe('filament.tables.' . hash('sha256', $defaultNamespace) . '.search')
            ->and($defaultTable->getTableColumnSearchesSessionKey())->toBe('filament.tables.' . hash('sha256', $defaultNamespace) . '.column_search')
            ->and($defaultTable->getTableSortSessionKey())->toBe('filament.tables.' . hash('sha256', $defaultNamespace) . '.sort')
            ->and($defaultTable->getTableGroupingSessionKey())->toBe('filament.tables.' . hash('sha256', $defaultNamespace) . '.grouping')
            ->and($defaultTable->getTableFiltersSessionKey())->toBe('filament.tables.' . hash('sha256', $defaultNamespace) . '.filters')
            ->and($defaultTable->getTableColumnsSessionKey())->toBe('filament.tables.' . hash('sha256', $defaultNamespace) . '.columns')
            ->and($defaultTable->getHasReorderedTableColumnsSessionKey())->toBe('filament.tables.' . hash('sha256', $defaultNamespace) . '.has_reordered_columns');

        /** @var QueryStringTableTestComponent $queryStringTable */
        $queryStringTable = livewire(QueryStringTableTestComponent::class)->instance();

        expect($queryStringTable->getTableSearchSessionKey())->toBe('filament.tables.' . hash('sha256', $queryStringTable::class) . '.search');

        /** @var PersistedTableTestComponent $identifiedTable */
        $identifiedTable = livewire(PersistedTableTestComponent::class, ['tableIdentifier' => 'first'])->instance();
        $identifiedNamespace = hash('sha256', $identifiedTable::class) . '.' . hash('sha256', 'first');

        expect($identifiedTable->getTablePerPageSessionKey())->toBe("filament.tables.{$identifiedNamespace}.per_page")
            ->and($identifiedTable->getTableSearchSessionKey())->toBe("filament.tables.{$identifiedNamespace}.search")
            ->and($identifiedTable->getTableColumnSearchesSessionKey())->toBe("filament.tables.{$identifiedNamespace}.column_search")
            ->and($identifiedTable->getTableSortSessionKey())->toBe("filament.tables.{$identifiedNamespace}.sort")
            ->and($identifiedTable->getTableGroupingSessionKey())->toBe("filament.tables.{$identifiedNamespace}.grouping")
            ->and($identifiedTable->getTableFiltersSessionKey())->toBe("filament.tables.{$identifiedNamespace}.filters")
            ->and($identifiedTable->getTableColumnsSessionKey())->toBe("filament.tables.{$identifiedNamespace}.columns")
            ->and($identifiedTable->getHasReorderedTableColumnsSessionKey())->toBe("filament.tables.{$identifiedNamespace}.has_reordered_columns");

        /** @var PersistedTableTestComponent $secondIdentifiedTable */
        $secondIdentifiedTable = livewire(PersistedTableTestComponent::class, ['tableIdentifier' => 'second'])->instance();

        expect($secondIdentifiedTable->getTableSearchSessionKey())->not->toBe($identifiedTable->getTableSearchSessionKey());

        $firstTenant = Team::factory()->create();
        $secondTenant = Team::factory()->create();

        Filament::setTenant($firstTenant, isQuiet: true);
        /** @var PersistedTableTestComponent $firstTenantTable */
        $firstTenantTable = livewire(PersistedTableTestComponent::class, ['tableIdentifier' => 'first'])->instance();
        $firstTenantFilterSessionKey = $firstTenantTable->getTableFiltersSessionKey();
        $firstTenantSearchSessionKey = $firstTenantTable->getTableSearchSessionKey();
        $firstTenantColumnSearchesSessionKey = $firstTenantTable->getTableColumnSearchesSessionKey();

        Filament::setTenant($secondTenant, isQuiet: true);
        /** @var PersistedTableTestComponent $secondTenantTable */
        $secondTenantTable = livewire(PersistedTableTestComponent::class, ['tableIdentifier' => 'first'])->instance();

        $firstTenantNamespace = strlen($defaultNamespace) . ':' . $defaultNamespace . strlen((string) $firstTenant->getKey()) . ':' . $firstTenant->getKey();
        $secondTenantNamespace = strlen($defaultNamespace) . ':' . $defaultNamespace . strlen((string) $secondTenant->getKey()) . ':' . $secondTenant->getKey();

        expect($firstTenantFilterSessionKey)->toBe('filament.tables.' . hash('sha256', $firstTenantNamespace) . '.' . hash('sha256', 'first') . '.filters')
            ->and($firstTenantSearchSessionKey)->toBe('filament.tables.' . hash('sha256', $firstTenantNamespace) . '.' . hash('sha256', 'first') . '.search')
            ->and($firstTenantColumnSearchesSessionKey)->toBe('filament.tables.' . hash('sha256', $firstTenantNamespace) . '.' . hash('sha256', 'first') . '.column_search')
            ->and($secondTenantTable->getTableFiltersSessionKey())->toBe('filament.tables.' . hash('sha256', $secondTenantNamespace) . '.' . hash('sha256', 'first') . '.filters')
            ->and($secondTenantTable->getTableSearchSessionKey())->toBe('filament.tables.' . hash('sha256', $secondTenantNamespace) . '.' . hash('sha256', 'first') . '.search')
            ->and($secondTenantTable->getTableColumnSearchesSessionKey())->toBe('filament.tables.' . hash('sha256', $secondTenantNamespace) . '.' . hash('sha256', 'first') . '.column_search')
            ->and($secondTenantTable->getTableSortSessionKey())->toBe("filament.tables.{$identifiedNamespace}.sort");

        Filament::setTenant(null);
    });

    it('separates and resets every persisted feature for same-class identified tables across reloads', function (): void {
        Post::factory()->count(3)->create();

        $firstTable = livewire(PersistedTableTestComponent::class, ['tableIdentifier' => 'first']);
        $reorderedColumns = array_reverse($firstTable->get('tableColumns'));
        $hasReorderedColumnsSessionKey = $firstTable->instance()->getHasReorderedTableColumnsSessionKey();

        $firstTable
            ->set('tableRecordsPerPage', 5)
            ->set('tableSearch', 'first search')
            ->set('tableColumnSearches.title', 'first column search')
            ->set('tableSort', 'title:desc')
            ->set('tableGrouping', 'title:desc')
            ->filterTable('is_published')
            ->call('applyTableColumnManager', $reorderedColumns, true);

        expect(session()->get($hasReorderedColumnsSessionKey))->toBeTrue();

        livewire(PersistedTableTestComponent::class, ['tableIdentifier' => 'second'])
            ->assertSet('tableRecordsPerPage', 10)
            ->assertSet('tableSearch', '')
            ->assertSet('tableColumnSearches', [])
            ->assertSet('tableSort', null)
            ->assertSet('tableGrouping', null)
            ->assertSet('tableFilters.is_published.isActive', false)
            ->assertSet('tableColumns', fn (array $columns): bool => $columns !== $reorderedColumns);

        livewire(PersistedTableTestComponent::class, ['tableIdentifier' => 'first'])
            ->assertSet('tableRecordsPerPage', 5)
            ->assertSet('tableSearch', 'first search')
            ->assertSet('tableColumnSearches.title', 'first column search')
            ->assertSet('tableSort', 'title:desc')
            ->assertSet('tableGrouping', 'title:desc')
            ->assertSet('tableFilters.is_published.isActive', true)
            ->assertSet('tableColumns', $reorderedColumns)
            ->set('tableRecordsPerPage', 10)
            ->call('resetTableSearch')
            ->call('resetTableColumnSearch', 'title')
            ->set('tableSort', null)
            ->set('tableGrouping', null)
            ->resetTableFilters()
            ->call('resetTableColumnManager');

        expect(session()->get($hasReorderedColumnsSessionKey))->toBeFalse();

        livewire(PersistedTableTestComponent::class, ['tableIdentifier' => 'first'])
            ->assertSet('tableRecordsPerPage', 10)
            ->assertSet('tableSearch', '')
            ->assertSet('tableColumnSearches', [])
            ->assertSet('tableSort', null)
            ->assertSet('tableGrouping', null)
            ->assertSet('tableFilters.is_published.isActive', false)
            ->assertSet('tableColumns', fn (array $columns): bool => $columns !== $reorderedColumns);
    });

    it('restores every persisted feature from the default session keys', function (): void {
        Post::factory()->count(3)->create();

        $defaultTable = livewire(PersistedTableTestComponent::class);
        $reorderedColumns = array_reverse($defaultTable->get('tableColumns'));
        $namespace = PersistedTableTestComponent::class;

        session()->put([
            'filament.tables.' . hash('sha256', $namespace) . '.per_page' => 5,
            'filament.tables.' . hash('sha256', $namespace) . '.search' => 'saved search',
            'filament.tables.' . hash('sha256', $namespace) . '.column_search' => ['title' => 'saved column search'],
            'filament.tables.' . hash('sha256', $namespace) . '.sort' => 'title:desc',
            'filament.tables.' . hash('sha256', $namespace) . '.grouping' => 'title:desc',
            'filament.tables.' . hash('sha256', $namespace) . '.filters' => ['is_published' => ['isActive' => true]],
            'filament.tables.' . hash('sha256', $namespace) . '.columns' => $reorderedColumns,
            'filament.tables.' . hash('sha256', $namespace) . '.has_reordered_columns' => true,
        ]);

        livewire(PersistedTableTestComponent::class)
            ->assertSet('tableRecordsPerPage', 5)
            ->assertSet('tableSearch', 'saved search')
            ->assertSet('tableColumnSearches.title', 'saved column search')
            ->assertSet('tableSort', 'title:desc')
            ->assertSet('tableGrouping', 'title:desc')
            ->assertSet('tableFilters.is_published.isActive', true)
            ->assertSet('tableColumns', $reorderedColumns);
    });

    it('keeps persisted criteria separated and structural preferences shared between tenants', function (?string $tableIdentifier): void {
        $firstTenant = Team::factory()->create();
        $secondTenant = Team::factory()->create();

        Filament::setTenant($firstTenant, isQuiet: true);
        $firstTenantTable = livewire(PersistedTableTestComponent::class, ['tableIdentifier' => $tableIdentifier]);
        $reorderedColumns = array_reverse($firstTenantTable->get('tableColumns'));

        $firstTenantTable
            ->set('tableRecordsPerPage', 5)
            ->set('tableSearch', 'first search')
            ->set('tableColumnSearches.title', 'first column search')
            ->set('tableSort', 'title:desc')
            ->set('tableGrouping', 'title:desc')
            ->call('applyTableColumnManager', $reorderedColumns, true)
            ->filterTable('is_published');

        Filament::setTenant($secondTenant, isQuiet: true);
        livewire(PersistedTableTestComponent::class, ['tableIdentifier' => $tableIdentifier])
            ->assertSet('tableRecordsPerPage', 5)
            ->assertSet('tableSearch', '')
            ->assertSet('tableColumnSearches', [])
            ->assertSet('tableSort', 'title:desc')
            ->assertSet('tableGrouping', 'title:desc')
            ->assertSet('tableFilters.is_published.isActive', false)
            ->assertSet('tableColumns', $reorderedColumns)
            ->set('tableSearch', 'second search')
            ->set('tableColumnSearches.title', 'second column search');

        Filament::setTenant($firstTenant, isQuiet: true);
        livewire(PersistedTableTestComponent::class, ['tableIdentifier' => $tableIdentifier])
            ->assertSet('tableRecordsPerPage', 5)
            ->assertSet('tableSearch', 'first search')
            ->assertSet('tableColumnSearches.title', 'first column search')
            ->assertSet('tableSort', 'title:desc')
            ->assertSet('tableGrouping', 'title:desc')
            ->assertSet('tableFilters.is_published.isActive', true)
            ->assertSet('tableColumns', $reorderedColumns);

        Filament::setTenant($secondTenant, isQuiet: true);
        livewire(PersistedTableTestComponent::class, ['tableIdentifier' => $tableIdentifier])
            ->assertSet('tableSearch', 'second search')
            ->assertSet('tableColumnSearches.title', 'second column search')
            ->assertSet('tableFilters.is_published.isActive', false);

        Filament::setTenant(null);
    })->with([
        'without `identifier()`' => [null],
        'with `identifier()`' => ['posts'],
    ]);

    it('starts persisted searches fresh per tenant without consuming existing unscoped state', function (): void {
        $namespace = PersistedTableTestComponent::class;

        session()->put([
            'filament.tables.' . hash('sha256', $namespace) . '.search' => 'saved search',
            'filament.tables.' . hash('sha256', $namespace) . '.column_search' => ['title' => 'saved column search'],
        ]);

        Filament::setTenant(Team::factory()->create(), isQuiet: true);
        livewire(PersistedTableTestComponent::class)
            ->assertSet('tableSearch', '')
            ->assertSet('tableColumnSearches', []);

        Filament::setTenant(null);
        livewire(PersistedTableTestComponent::class)
            ->assertSet('tableSearch', 'saved search')
            ->assertSet('tableColumnSearches.title', 'saved column search');
    });

    it('keeps tenant and identifier dimensions unambiguous when persisting filters', function (): void {
        $tenant = Team::factory()->create();

        Filament::setTenant($tenant, isQuiet: true);
        livewire(PersistedTableTestComponent::class)
            ->filterTable('is_published');

        Filament::setTenant(null);
        livewire(PersistedTableTestComponent::class, ['tableIdentifier' => (string) $tenant->getKey()])
            ->assertSet('tableFilters.is_published.isActive', false);

        $firstTenant = (new Team(['id' => 'first|second']))->setKeyType('string');
        $secondTenant = (new Team(['id' => 'first']))->setKeyType('string');

        expect($firstTenant->getKey())->toBe('first|second')
            ->and($secondTenant->getKey())->toBe('first');

        Filament::setTenant($firstTenant, isQuiet: true);
        livewire(PersistedTableTestComponent::class, ['tableIdentifier' => 'third'])
            ->filterTable('is_published');

        Filament::setTenant($secondTenant, isQuiet: true);
        livewire(PersistedTableTestComponent::class, ['tableIdentifier' => 'second|third'])
            ->assertSet('tableFilters.is_published.isActive', false);

        Filament::setTenant($firstTenant, isQuiet: true);
        livewire(PersistedTableTestComponent::class, ['tableIdentifier' => 'third'])
            ->assertSet('tableFilters.is_published.isActive', true);

        Filament::setTenant(null);
    });
});

describe('rendering', function (): void {
    it('can render the table', function (): void {
        Post::factory()->count(3)->create();

        livewire(TableTestComponent::class)
            ->assertSuccessful();
    });

    it('can render an empty table', function (): void {
        livewire(TableTestComponent::class)
            ->assertSuccessful();
    });

    it('can render a striped table', function (): void {
        Post::factory()->count(3)->create();

        livewire(StripedTableTestComponent::class)
            ->assertSuccessful();
    });

    it('can render a table with heading and description', function (): void {
        Post::factory()->count(3)->create();

        livewire(HeadingTableTestComponent::class)
            ->assertSuccessful();
    });

    it('can render a table with custom empty state', function (): void {
        livewire(EmptyStateTableTestComponent::class)
            ->assertSuccessful();
    });

    it('renders a delayed loading state for updates that change the displayed records', function (): void {
        Post::factory()->create();

        $html = livewire(TableTestComponent::class)
            ->assertSuccessful()
            ->html();

        expect($html)
            ->toContain('data-testid="table-loading-state"')
            ->toContain('wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') . '.block')
            ->not->toContain('wire:loading.class.delay.' . config('filament.livewire_loading_delay', 'default') . '="fi-ta-content-loading"')
            ->not->toContain('wire:loading.attr.delay.' . config('filament.livewire_loading_delay', 'default') . '="inert"')
            ->toContain('wire:target="' . implode(',', Table::LOADING_TARGETS) . '"')
            ->and(strpos($html, 'data-testid="table-loading-state"'))
            ->toBeLessThan(strpos($html, 'class="fi-ta-table"'));
    });

    it('renders a delayed loading skeleton when enabled', function (): void {
        Post::factory()->create();

        $html = livewire(LoadingSkeletonTableTestComponent::class)
            ->assertSuccessful()
            ->html();

        expect($html)
            ->toContain('wire:loading.class.delay.' . config('filament.livewire_loading_delay', 'default') . '="fi-ta-content-loading"');
        expect($html)
            ->toContain('wire:loading.attr.delay.' . config('filament.livewire_loading_delay', 'default') . '="inert"');
    });

    it('preserves the header sort loading indicator when the loading skeleton is disabled', function (): void {
        Post::factory()->create();

        $html = livewire(SortableTableTestComponent::class)
            ->assertSuccessful()
            ->html();

        expect(substr_count($html, 'wire:target="sortTable(\'title\')"'))->toBe(2);
    });

    it('preserves the header sort loading indicator when the loading skeleton is enabled', function (): void {
        Post::factory()->create();

        $html = livewire(LoadingSkeletonSortableTableTestComponent::class)
            ->assertSuccessful()
            ->html();

        expect(substr_count($html, 'wire:target="sortTable(\'title\')"'))->toBe(2);
    });

    it('replaces the empty state icon with a loading indicator during updates that change the displayed records', function (): void {
        $html = livewire(EmptyStateTableTestComponent::class)
            ->assertSuccessful()
            ->html();

        expect($html)
            ->toContain('wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default'))
            ->toContain('wire:loading.delay.' . config('filament.livewire_loading_delay', 'default'))
            ->toContain('wire:target="' . implode(',', Table::LOADING_TARGETS) . '"')
            ->toContain('fi-loading-indicator')
            ->not->toContain('wire:loading.class.delay.' . config('filament.livewire_loading_delay', 'default') . '="fi-ta-content-loading"');
    });

    it('can render `CONTENT_BEFORE` and `CONTENT_AFTER` hooks around the table content', function (): void {
        $contentBeforeData = null;
        $contentAfterData = null;

        registerTableContentRenderHooks(TableTestComponent::class, $contentBeforeData, $contentAfterData);

        Post::factory()->count(3)->create();

        $component = livewire(TableTestComponent::class)
            ->assertSuccessful();

        $html = $component->html();

        $contentBeforePosition = strpos($html, 'data-testid="table-content-before-hook"');
        $tableContentPosition = strpos($html, 'class="fi-ta-content-ctn fi-fixed-positioning-context"');
        $contentAfterPosition = strpos($html, 'data-testid="table-content-after-hook"');
        $paginationPosition = strpos($html, '<nav');

        expect($contentBeforePosition)->not->toBeFalse()
            ->and($tableContentPosition)->not->toBeFalse()
            ->and($contentAfterPosition)->not->toBeFalse()
            ->and($paginationPosition)->not->toBeFalse()
            ->and($contentBeforePosition)->toBeLessThan($tableContentPosition)
            ->and($tableContentPosition)->toBeLessThan($contentAfterPosition)
            ->and($contentAfterPosition)->toBeLessThan($paginationPosition)
            ->and($contentBeforeData)->toHaveKeys(['hasPagination', 'records', 'table'])
            ->and($contentBeforeData['hasPagination'])->toBeTrue()
            ->and($contentBeforeData['records'])->toBeInstanceOf(LengthAwarePaginator::class)
            ->and($contentBeforeData['table'])->toBeInstanceOf(Table::class)
            ->and($contentAfterData['hasPagination'])->toBeTrue()
            ->and($contentAfterData['records'])->toBe($contentBeforeData['records'])
            ->and($contentAfterData['table'])->toBe($contentBeforeData['table']);
    });

    it('can render `CONTENT_BEFORE` and `CONTENT_AFTER` hooks around the empty state', function (): void {
        $contentBeforeData = null;
        $contentAfterData = null;

        registerTableContentRenderHooks(EmptyStateTableTestComponent::class, $contentBeforeData, $contentAfterData);

        $html = livewire(EmptyStateTableTestComponent::class)
            ->assertSuccessful()
            ->html();

        $contentBeforePosition = strpos($html, 'data-testid="table-content-before-hook"');
        $emptyStatePosition = strpos($html, 'No posts yet');
        $contentAfterPosition = strpos($html, 'data-testid="table-content-after-hook"');

        expect($contentBeforePosition)->not->toBeFalse()
            ->and($emptyStatePosition)->not->toBeFalse()
            ->and($contentAfterPosition)->not->toBeFalse()
            ->and($contentBeforePosition)->toBeLessThan($emptyStatePosition)
            ->and($emptyStatePosition)->toBeLessThan($contentAfterPosition)
            ->and($contentBeforeData['hasPagination'])->toBeFalse()
            ->and($contentBeforeData['records'])->toBeInstanceOf(LengthAwarePaginator::class)
            ->and($contentBeforeData['records'])->toBeEmpty()
            ->and($contentAfterData['records'])->toBe($contentBeforeData['records']);
    });

    it('provides unloaded and loaded records to content render hooks with lazy loading', function (): void {
        $contentBeforeData = null;
        $contentAfterData = null;

        registerTableContentRenderHooks(DeferredLoadingTableTestComponent::class, $contentBeforeData, $contentAfterData);

        Post::factory()->create();

        $component = livewire(DeferredLoadingTableTestComponent::class)
            ->assertSuccessful();

        expect($contentBeforeData['hasPagination'])->toBeFalse()
            ->and($contentBeforeData['records'])->toBeNull()
            ->and($contentAfterData['records'])->toBeNull();

        $component->call('loadTable');

        expect($contentBeforeData['hasPagination'])->toBeTrue()
            ->and($contentBeforeData['records'])->toBeInstanceOf(LengthAwarePaginator::class)
            ->and($contentBeforeData['records'])->toHaveCount(1)
            ->and($contentAfterData['records'])->toBe($contentBeforeData['records']);
    });

    it('provides a collection and no pagination to content render hooks when pagination is disabled', function (): void {
        $contentBeforeData = null;
        $contentAfterData = null;

        registerTableContentRenderHooks(UnpaginatedTableTestComponent::class, $contentBeforeData, $contentAfterData);

        Post::factory()->count(3)->create();

        livewire(UnpaginatedTableTestComponent::class)
            ->assertSuccessful();

        expect($contentBeforeData['hasPagination'])->toBeFalse()
            ->and($contentBeforeData['records'])->toBeInstanceOf(Collection::class)
            ->and($contentBeforeData['records'])->toHaveCount(3)
            ->and($contentAfterData['records'])->toBe($contentBeforeData['records']);
    });

    it('keeps `CONTENT_BEFORE` and `CONTENT_AFTER` around the table content after an update', function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        Post::query()->delete();

        Post::factory()->count(2)->create();

        $hookOrder = '[data-testid="table-content-before-hook"] + .fi-ta-content-ctn + [data-testid="table-content-after-hook"] + nav.fi-pagination';

        visit(TableRenderHooksBrowserTest::getUrl(isAbsolute: false))
            ->assertDontSee('Created by render hook test')
            ->assertPresent($hookOrder)
            ->click('[data-testid="create-post"]')
            ->assertSee('Created by render hook test')
            ->assertPresent($hookOrder)
            ->assertNoAccessibilityIssues();

        visit(TableRenderHooksBrowserTest::getUrl(isAbsolute: false))
            ->inDarkMode()
            ->assertPresent($hookOrder)
            ->assertNoAccessibilityIssues();
    });

    it('renders accessible table loading states in light and dark modes', function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        Post::query()->delete();

        Post::factory()->create(['title' => 'Short']);
        Post::factory()->create(['title' => 'A much longer title']);

        visit(TableRenderHooksBrowserTest::getUrl(isAbsolute: false))
            ->assertAttribute('[data-testid="table-loading-state"]', 'role', 'status')
            ->assertAttribute('[data-testid="table-loading-state"]', 'aria-live', 'polite')
            ->assertNoAccessibilityIssues();

        visit(TableRenderHooksBrowserTest::getUrl(isAbsolute: false))
            ->inDarkMode()
            ->assertPresent('[data-testid="table-loading-state"]')
            ->assertNoAccessibilityIssues();

        Post::query()->delete();

        visit(TableRenderHooksBrowserTest::getUrl(isAbsolute: false))
            ->assertPresent('.fi-ta-empty-state-icon-bg .fi-loading-indicator')
            ->assertNoAccessibilityIssues();

        visit(TableRenderHooksBrowserTest::getUrl(isAbsolute: false))
            ->inDarkMode()
            ->assertPresent('.fi-ta-empty-state-icon-bg .fi-loading-indicator')
            ->assertNoAccessibilityIssues();
    });

});

function registerTableContentRenderHooks(string $scope, ?array &$contentBeforeData, ?array &$contentAfterData): void
{
    FilamentView::registerRenderHook(
        TablesRenderHook::CONTENT_BEFORE,
        static function (array $data) use (&$contentBeforeData): string {
            $contentBeforeData = $data;

            return '<div data-testid="table-content-before-hook"></div>';
        },
        scopes: $scope,
    );

    FilamentView::registerRenderHook(
        TablesRenderHook::CONTENT_AFTER,
        static function (array $data) use (&$contentAfterData): string {
            $contentAfterData = $data;

            return '<div data-testid="table-content-after-hook"></div>';
        },
        scopes: $scope,
    );
}

class TableTestComponent extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class StripedTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->striped();
    }
}

class FixedPageSizeTableTestComponent extends TableTestComponent
{
    public function mount(): void
    {
        $this->tableRecordsPerPage = 7;
    }
}

class LoadingSkeletonTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->loadingSkeleton();
    }
}

class SortableTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable(),
            ]);
    }
}

class LoadingSkeletonSortableTableTestComponent extends SortableTableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->loadingSkeleton();
    }
}

class ToggleableSortableTableTestComponent extends TableTestComponent
{
    public string $defaultSortColumn = 'email';

    public string $defaultSortDirection = 'asc';

    public bool $usesDefaultSortClosure = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('posts_count')
                    ->counts('posts')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort(
                $this->usesDefaultSortClosure ? fn (): string => $this->defaultSortColumn : $this->defaultSortColumn,
                $this->defaultSortDirection,
            )
            ->persistSortInSession();
    }
}

class ToggleableSortableCustomDataTableTestComponent extends TableTestComponent
{
    public bool $usesSortTuple = false;

    public function table(Table $table): Table
    {
        $records = collect([
            1 => ['id' => 1, 'title' => 'Bravo'],
            2 => ['id' => 2, 'title' => 'Charlie'],
            3 => ['id' => 3, 'title' => 'Alpha'],
        ]);

        return $table
            ->records($this->usesSortTuple
                ? static fn (array $sort): Collection => $records->sortBy($sort[0] ?? 'id', descending: $sort[1] === 'desc')
                : static fn (?string $sortColumn, ?string $sortDirection): Collection => $records->sortBy($sortColumn ?? 'id', descending: $sortDirection === 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('id'),
            ])
            ->persistSortInSession();
    }
}

class MappedDefaultSortTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('display_title')
                    ->state(static fn (Post $record): string => $record->title)
                    ->sortable(['title'])
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('display_title');
    }
}

class HeadingTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->heading('Posts')
            ->description('All blog posts');
    }
}

class EmptyStateTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->emptyStateHeading('No posts yet')
            ->emptyStateDescription('Create your first post.');
    }
}

class DeferredLoadingTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->lazyLoaded();
    }
}

class UnpaginatedTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->paginated(false);
    }
}

class QueryStringTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->queryStringIdentifier('posts');
    }
}

class IdentifiedQueryStringTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->identifier('persisted-posts')
            ->queryStringIdentifier('url-posts');
    }
}

class PersistedTableTestComponent extends TableTestComponent
{
    public ?string $tableIdentifier = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->identifier($this->tableIdentifier)
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('content')
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('is_published'),
            ])
            ->groups([
                Group::make('title'),
            ])
            ->paginationPageOptions([5, 10])
            ->defaultPaginationPageOption(10)
            ->reorderableColumns()
            ->persistInSession();
    }
}

class HeadingLevelTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->rootHeadingLevel(3);
    }
}

class RecordUrlTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->recordUrl(static fn (Post $record): string => "/posts/{$record->getKey()}");
    }
}
