<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Facades\FilamentView;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Tables\View\TablesRenderHook;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\TableRenderHooksBrowserTest;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

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

describe('defer loading', function (): void {
    it('defaults `isLoadingDeferred()` to `false`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->isLoadingDeferred())->toBeFalse();
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

    it('computes heading tag from level', function (): void {
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

    it('preserves mapped sorting for a toggled-hidden `defaultSort()` column', function (): void {
        $firstPost = Post::factory()->create(['title' => 'Charlie']);
        $secondPost = Post::factory()->create(['title' => 'Alpha']);

        livewire(MappedDefaultSortTableTestComponent::class)
            ->assertCanSeeTableRecords([$secondPost, $firstPost], inOrder: true)
            ->set('tableSort', 'display_title:desc')
            ->assertSet('tableSort', 'display_title:desc')
            ->assertCanSeeTableRecords([$secondPost, $firstPost], inOrder: true);
    });
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

    it('uses the identifier for every table session key while preserving default keys and tenant-scoped filter keys', function (): void {
        /** @var PersistedTableTestComponent $defaultTable */
        $defaultTable = livewire(PersistedTableTestComponent::class)->instance();
        $defaultNamespace = $defaultTable::class;

        expect($defaultTable->getTablePerPageSessionKey())->toBe('tables.' . md5($defaultNamespace) . '_per_page')
            ->and($defaultTable->getTableSearchSessionKey())->toBe('tables.' . md5($defaultNamespace) . '_search')
            ->and($defaultTable->getTableColumnSearchesSessionKey())->toBe('tables.' . md5($defaultNamespace) . '_column_search')
            ->and($defaultTable->getTableSortSessionKey())->toBe('tables.' . md5($defaultNamespace) . '_sort')
            ->and($defaultTable->getTableGroupingSessionKey())->toBe('tables.' . md5($defaultNamespace) . '_grouping')
            ->and($defaultTable->getTableFiltersSessionKey())->toBe('tables.' . md5($defaultNamespace) . '_filters')
            ->and($defaultTable->getTableColumnsSessionKey())->toBe('tables.' . md5($defaultNamespace) . '_columns')
            ->and($defaultTable->getHasReorderedTableColumnsSessionKey())->toBe('tables.' . md5($defaultNamespace) . '_has_reordered_columns');

        /** @var QueryStringTableTestComponent $queryStringTable */
        $queryStringTable = livewire(QueryStringTableTestComponent::class)->instance();

        expect($queryStringTable->getTableSearchSessionKey())->toBe('tables.' . md5($queryStringTable::class) . '_search');

        /** @var PersistedTableTestComponent $identifiedTable */
        $identifiedTable = livewire(PersistedTableTestComponent::class, ['tableIdentifier' => 'first'])->instance();
        $identifiedNamespace = md5($identifiedTable::class) . '.' . md5('first');

        expect($identifiedTable->getTablePerPageSessionKey())->toBe("tables.{$identifiedNamespace}_per_page")
            ->and($identifiedTable->getTableSearchSessionKey())->toBe("tables.{$identifiedNamespace}_search")
            ->and($identifiedTable->getTableColumnSearchesSessionKey())->toBe("tables.{$identifiedNamespace}_column_search")
            ->and($identifiedTable->getTableSortSessionKey())->toBe("tables.{$identifiedNamespace}_sort")
            ->and($identifiedTable->getTableGroupingSessionKey())->toBe("tables.{$identifiedNamespace}_grouping")
            ->and($identifiedTable->getTableFiltersSessionKey())->toBe("tables.{$identifiedNamespace}_filters")
            ->and($identifiedTable->getTableColumnsSessionKey())->toBe("tables.{$identifiedNamespace}_columns")
            ->and($identifiedTable->getHasReorderedTableColumnsSessionKey())->toBe("tables.{$identifiedNamespace}_has_reordered_columns");

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

        expect($firstTenantFilterSessionKey)->toBe('tables.' . md5($defaultNamespace . "|{$firstTenant->getKey()}") . '.' . md5('first') . '_filters')
            ->and($firstTenantSearchSessionKey)->toBe('tables.' . md5($defaultNamespace . "|{$firstTenant->getKey()}") . '.' . md5('first') . '_search')
            ->and($firstTenantColumnSearchesSessionKey)->toBe('tables.' . md5($defaultNamespace . "|{$firstTenant->getKey()}") . '.' . md5('first') . '_column_search')
            ->and($secondTenantTable->getTableFiltersSessionKey())->toBe('tables.' . md5($defaultNamespace . "|{$secondTenant->getKey()}") . '.' . md5('first') . '_filters')
            ->and($secondTenantTable->getTableSearchSessionKey())->toBe('tables.' . md5($defaultNamespace . "|{$secondTenant->getKey()}") . '.' . md5('first') . '_search')
            ->and($secondTenantTable->getTableColumnSearchesSessionKey())->toBe('tables.' . md5($defaultNamespace . "|{$secondTenant->getKey()}") . '.' . md5('first') . '_column_search')
            ->and($secondTenantTable->getTableSortSessionKey())->toBe("tables.{$identifiedNamespace}_sort");

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
            ->call('resetTableColumnSearches')
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

    it('restores every persisted feature from the existing default session keys', function (): void {
        Post::factory()->count(3)->create();

        $defaultTable = livewire(PersistedTableTestComponent::class);
        $reorderedColumns = array_reverse($defaultTable->get('tableColumns'));
        $namespace = PersistedTableTestComponent::class;

        session()->put([
            'tables.' . md5($namespace) . '_per_page' => 5,
            'tables.' . md5($namespace) . '_search' => 'legacy search',
            'tables.' . md5($namespace) . '_column_search' => ['title' => 'legacy column search'],
            'tables.' . md5($namespace) . '_sort' => 'title:desc',
            'tables.' . md5($namespace) . '_grouping' => 'title:desc',
            'tables.' . md5($namespace) . '_filters' => ['is_published' => ['isActive' => true]],
            'tables.' . md5($namespace) . '_columns' => $reorderedColumns,
            'tables.' . md5($namespace) . '_has_reordered_columns' => true,
        ]);

        livewire(PersistedTableTestComponent::class)
            ->assertSet('tableRecordsPerPage', 5)
            ->assertSet('tableSearch', 'legacy search')
            ->assertSet('tableColumnSearches.title', 'legacy column search')
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
            'tables.' . md5($namespace) . '_search' => 'legacy search',
            'tables.' . md5($namespace) . '_column_search' => ['title' => 'legacy column search'],
        ]);

        Filament::setTenant(Team::factory()->create(), isQuiet: true);
        livewire(PersistedTableTestComponent::class)
            ->assertSet('tableSearch', '')
            ->assertSet('tableColumnSearches', []);

        Filament::setTenant(null);
        livewire(PersistedTableTestComponent::class)
            ->assertSet('tableSearch', 'legacy search')
            ->assertSet('tableColumnSearches.title', 'legacy column search');
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

        expect(substr_count($html, 'wire:target="sortTable(\'title\')"'))->toBe(3);
    });

    it('preserves the header sort loading indicator when the loading skeleton is enabled', function (): void {
        Post::factory()->create();

        $html = livewire(LoadingSkeletonSortableTableTestComponent::class)
            ->assertSuccessful()
            ->html();

        expect(substr_count($html, 'wire:target="sortTable(\'title\')"'))->toBe(3);
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

    it('provides unloaded and loaded records to content render hooks when table loading is deferred', function (): void {
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

        $page = visit(TableRenderHooksBrowserTest::getUrl(isAbsolute: false));
        $individualSearchInput = '.fi-ta-individual-search-row input';

        $page
            ->type($individualSearchInput, 'Sho')
            ->assertPresent('.fi-ta-records[inert]')
            ->assertMissing('tbody[inert] .fi-ta-individual-search-row input')
            ->assertPresent($individualSearchInput . ':focus')
            ->keys($individualSearchInput, 'r')
            ->assertValue($individualSearchInput, 'Shor')
            // Let the debounced search finish before opening another page.
            ->wait(2)
            ->assertMissing('.fi-ta-records[inert]');

        $page = visit(TableRenderHooksBrowserTest::getUrl(isAbsolute: false));

        $page
            ->click('Published')
            ->assertPresent('.fi-ta-content-loading .fi-ta-records[inert]')
            ->assertMissing('.fi-ta-records[inert]');

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
            ->defaultSort('email')
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
            ->deferLoading();
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
