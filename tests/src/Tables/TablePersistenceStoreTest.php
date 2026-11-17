<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Contracts\TablePersistenceStore;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Persistence\SessionTablePersistenceStore;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    PersistenceStoreTableTestComponent::$store = new ArrayTablePersistenceStoreForTesting;
    PersistenceStoreTableTestComponent::$configure = null;
});

describe('session table persistence store', function (): void {
    it('uses the concrete session adapter without a default contract binding', function (): void {
        expect(app()->bound(TablePersistenceStore::class))->toBeFalse();

        $table = livewire(PersistenceStoreTableTestComponent::class)->instance()->getTable();

        expect($table->getPersistenceStore())->toBeInstanceOf(SessionTablePersistenceStore::class);
    });

    it('uses `app()` to resolve the concrete fallback rather than a generic contract binding', function (): void {
        $sessionStore = new SessionTablePersistenceStore;
        app()->instance(SessionTablePersistenceStore::class, $sessionStore);
        app()->instance(TablePersistenceStore::class, PersistenceStoreTableTestComponent::$store);

        $table = livewire(PersistenceStoreTableTestComponent::class)->instance()->getTable();

        expect($table->getPersistenceStore())->toBe($sessionStore);
    });

    it('reads and overwrites values in the session', function (): void {
        $store = app(SessionTablePersistenceStore::class);
        session()->put('filament.tables.test.search', 'from session');

        expect($store->exists('filament.tables.test.search'))->toBeTrue()
            ->and($store->get('filament.tables.test.search'))->toBe('from session');

        $store->put('filament.tables.test.search', 'first search');
        $store->put('filament.tables.test.search', 'second search');

        expect($store->get('filament.tables.test.search'))->toBe('second search')
            ->and(session()->get('filament.tables.test.search'))->toBe('second search');
    });

    it('uses the default only for missing keys, not stored `null`', function (): void {
        $store = app(SessionTablePersistenceStore::class);

        expect($store->exists('filament.tables.test.grouping'))->toBeFalse()
            ->and($store->get('filament.tables.test.grouping'))->toBeNull()
            ->and($store->get('filament.tables.test.grouping', 'default'))->toBe('default');

        $store->put('filament.tables.test.grouping', null);

        expect($store->exists('filament.tables.test.grouping'))->toBeTrue()
            ->and($store->get('filament.tables.test.grouping', 'default'))->toBeNull();
    });

    it('forgets existing, `null`, and missing values', function (mixed $value): void {
        $store = app(SessionTablePersistenceStore::class);
        $store->put('filament.tables.test.value', $value);
        $store->forget('filament.tables.test.value');
        $store->forget('filament.tables.test.missing');

        expect($store->exists('filament.tables.test.value'))->toBeFalse()
            ->and($store->get('filament.tables.test.value', 'default'))->toBe('default')
            ->and(session()->exists('filament.tables.test.value'))->toBeFalse()
            ->and($store->exists('filament.tables.test.missing'))->toBeFalse();
    })->with([5, null]);
});

describe('configuring table persistence', function (): void {
    it('supports a nullable table store and evaluated closures', function (): void {
        $component = livewire(PersistenceStoreTableTestComponent::class)->instance();
        $table = $component->getTable();
        $store = PersistenceStoreTableTestComponent::$store;

        expect($table->persistenceStore($store))->toBe($table)
            ->and($table->getPersistenceStore())->toBe($store);

        $table->persistenceStore(static function ($livewire, Table $table) use ($component, $store): TablePersistenceStore {
            expect($livewire)->toBe($component)
                ->and($table)->toBe($component->getTable());

            return $store;
        });

        expect($table->getPersistenceStore())->toBe($store);

        $table->persistenceStore(static fn (): ?TablePersistenceStore => null);

        expect($table->getPersistenceStore())->toBeInstanceOf(SessionTablePersistenceStore::class);

        $table->persistenceStore($store)->persistenceStore(null);

        expect($table->getPersistenceStore())->toBeInstanceOf(SessionTablePersistenceStore::class);
    });

    it('inherits the current default regardless of configuration order', function (string $feature, bool $setsDefaultFirst): void {
        $table = livewire(PersistenceStoreTableTestComponent::class)->instance()->getTable();
        $store = PersistenceStoreTableTestComponent::$store;
        $setter = "persist{$feature}InStore";
        $getter = "get{$feature}PersistenceStore";

        if ($setsDefaultFirst) {
            $table->persistenceStore($store)->{$setter}();
        } else {
            $table->{$setter}()->persistenceStore($store);
        }

        expect($table->{$getter}())->toBe($store);

        $replacementStore = new ArrayTablePersistenceStoreForTesting;
        $table->persistenceStore($replacementStore);

        expect($table->{$getter}())->toBe($replacementStore);

        $table->persistenceStore(null);

        expect($table->{$getter}())->toBeInstanceOf(SessionTablePersistenceStore::class)
            ->and($table->{"persists{$feature}InSession"}())->toBeTrue();
    })->with(['Search', 'ColumnSearches', 'Filters', 'Sort', 'Group', 'Columns', 'RecordsPerPage'])->with([true, false]);

    it('lets `null` feature overrides inherit the table store', function (string $feature, bool $usesClosure): void {
        $table = livewire(PersistenceStoreTableTestComponent::class)->instance()->getTable();
        $store = PersistenceStoreTableTestComponent::$store;
        $setter = "persist{$feature}InStore";
        $getter = "get{$feature}PersistenceStore";

        $table->persistenceStore($store)
            ->{$setter}(persistenceStore: new ArrayTablePersistenceStoreForTesting)
            ->{$setter}(persistenceStore: $usesClosure ? static fn (): ?TablePersistenceStore => null : null);

        expect($table->{$getter}())->toBe($store);

        $replacementStore = new ArrayTablePersistenceStoreForTesting;
        $table->persistenceStore($replacementStore);

        expect($table->{$getter}())->toBe($replacementStore);
    })->with(['Search', 'ColumnSearches', 'Filters', 'Sort', 'Group', 'Columns', 'RecordsPerPage'])->with([true, false]);

    it('keeps explicit feature stores and session pins when the default changes', function (string $feature): void {
        $table = livewire(PersistenceStoreTableTestComponent::class)->instance()->getTable();
        $store = PersistenceStoreTableTestComponent::$store;
        $storeSetter = "persist{$feature}InStore";
        $sessionSetter = "persist{$feature}InSession";
        $getter = "get{$feature}PersistenceStore";
        $sessionPredicate = "persists{$feature}InSession";

        $table->{$storeSetter}(persistenceStore: $store)
            ->persistenceStore(new ArrayTablePersistenceStoreForTesting);

        expect($table->{$getter}())->toBe($store)
            ->and($table->{$sessionPredicate}())->toBeFalse();

        $table->{$sessionSetter}()->persistenceStore($store);

        expect($table->{$getter}())->toBeInstanceOf(SessionTablePersistenceStore::class)
            ->and($table->{$sessionPredicate}())->toBeTrue();
    })->with(['Search', 'ColumnSearches', 'Filters', 'Sort', 'Group', 'Columns', 'RecordsPerPage']);

    it('lets the last route win, including evaluated `false` without reviving the older route', function (string $feature, bool $sessionLast, bool $enabled): void {
        $table = livewire(PersistenceStoreTableTestComponent::class)->instance()->getTable();
        $store = PersistenceStoreTableTestComponent::$store;
        $storeSetter = "persist{$feature}InStore";
        $sessionSetter = "persist{$feature}InSession";
        $predicate = "shouldPersist{$feature}";
        $sessionPredicate = "persists{$feature}InSession";
        $getter = "get{$feature}PersistenceStore";

        if ($sessionLast) {
            $table->{$storeSetter}(persistenceStore: $store)
                ->{$sessionSetter}(static fn (): bool => $enabled);
        } else {
            $table->{$sessionSetter}()
                ->{$storeSetter}(static fn (): bool => $enabled, $store);
        }

        expect($table->{$predicate}())->toBe($enabled)
            ->and($table->{$sessionPredicate}())->toBe($enabled && $sessionLast);

        if ($enabled) {
            if ($sessionLast) {
                expect($table->{$getter}())->toBeInstanceOf(SessionTablePersistenceStore::class);
            } else {
                expect($table->{$getter}())->toBe($store);
            }
        }
    })->with(['Search', 'ColumnSearches', 'Filters', 'Sort', 'Group', 'Columns', 'RecordsPerPage'])->with([true, false])->with([true, false]);

    it('retains session-backed column and page-size defaults until explicitly opted into a store', function (): void {
        PersistenceStoreTableTestComponent::$configure = static fn (Table $table): Table => $table
            ->persistenceStore(PersistenceStoreTableTestComponent::$store);

        $component = livewire(PersistenceStoreTableTestComponent::class);
        $columns = array_reverse($component->get('tableColumns'));
        $component->set('tableRecordsPerPage', 5)
            ->set('tableSearch', 'not persisted')
            ->call('applyTableColumnManager', $columns, true);
        $instance = $component->instance();

        expect(session()->get($instance->getTablePerPageSessionKey()))->toBe(5)
            ->and(session()->get($instance->getTableColumnsSessionKey()))->toBe($columns)
            ->and(PersistenceStoreTableTestComponent::$store->values)->toBe([]);

        livewire(PersistenceStoreTableTestComponent::class)
            ->assertSet('tableRecordsPerPage', 5)
            ->assertSet('tableColumns', $columns)
            ->assertSet('tableSearch', '');

        PersistenceStoreTableTestComponent::$configure = static fn (Table $table): Table => $table
            ->persistenceStore(PersistenceStoreTableTestComponent::$store)
            ->persistColumnsInStore()
            ->persistRecordsPerPageInStore();

        livewire(PersistenceStoreTableTestComponent::class)
            ->assertSet('tableRecordsPerPage', 10)
            ->set('tableRecordsPerPage', 5);

        expect(PersistenceStoreTableTestComponent::$store->get($instance->getTablePerPageSessionKey()))->toBe(5);
    });

    it('does not resolve any store or access persisted data for disabled features', function (): void {
        PersistenceStoreTableTestComponent::$configure = static fn (Table $table): Table => $table
            ->persistenceStore(static fn (): TablePersistenceStore => throw new LogicException('Disabled table store was resolved.'))
            ->persistInSession()
            ->persistInStore(false, static fn (): TablePersistenceStore => throw new LogicException('Disabled feature store was resolved.'));

        $component = livewire(PersistenceStoreTableTestComponent::class);
        $defaultColumns = $component->get('tableColumns');
        $columns = array_reverse($defaultColumns);
        $component->set('tableSearch', 'search')
            ->set('tableColumnSearches.title', 'column search')
            ->set('tableSort', 'title:desc')
            ->set('tableGrouping', 'title:desc')
            ->filterTable('is_published')
            ->set('tableRecordsPerPage', 5)
            ->call('applyTableColumnManager', $columns, true);

        livewire(PersistenceStoreTableTestComponent::class)
            ->assertSet('tableSearch', '')
            ->assertSet('tableColumnSearches', [])
            ->assertSet('tableSort', null)
            ->assertSet('tableGrouping', null)
            ->assertSet('tableFilters.is_published.isActive', false)
            ->assertSet('tableRecordsPerPage', 10)
            ->assertSet('tableColumns', $defaultColumns);

        expect(PersistenceStoreTableTestComponent::$store->values)->toBe([]);
    });

    it('coherently resolves closures for feature writes and invalidates them on setters', function (bool $inheritsStore): void {
        $resolveStore = static function ($livewire, Table $table): TablePersistenceStore {
            expect($livewire->getTable())->toBe($table);

            return new ArrayTablePersistenceStoreForTesting;
        };
        PersistenceStoreTableTestComponent::$configure = static fn (Table $table): Table => $table
            ->persistInStore(false)
            ->persistenceStore($inheritsStore ? $resolveStore : null)
            ->persistSearchInStore(persistenceStore: $inheritsStore ? null : $resolveStore);

        $component = livewire(PersistenceStoreTableTestComponent::class)
            ->set('tableSearch', 'coherent search')
            ->instance();
        $table = $component->getTable();
        $store = $table->getSearchPersistenceStore();
        $key = $component->getTableSearchSessionKey();

        expect($store->get($key))->toBe('coherent search')
            ->and($table->getSearchPersistenceStore())->toBe($store);

        $replacementStore = new ArrayTablePersistenceStoreForTesting;
        if ($inheritsStore) {
            $table->persistenceStore($replacementStore);
        } else {
            $table->persistSearchInStore(persistenceStore: $replacementStore);
        }

        expect($table->getSearchPersistenceStore())->toBe($replacementStore)
            ->and($store->get($key))->toBe('coherent search');
    })->with([true, false]);
});

describe('persisting and reloading table features', function (): void {
    it('roundtrips all seven features and retains existing key overrides', function (string $componentClass): void {
        Post::factory()->count(3)->create();
        PersistenceStoreTableTestComponent::$configure = static fn (Table $table): Table => $table
            ->persistInStore(persistenceStore: PersistenceStoreTableTestComponent::$store);

        $component = livewire($componentClass);
        $columns = array_reverse($component->get('tableColumns'));
        $component->set('tableRecordsPerPage', 5)
            ->set('tableSearch', 'saved search')
            ->set('tableColumnSearches.title', 'saved column search')
            ->call('sortTable', 'title', 'desc')
            ->set('tableGrouping', 'title:desc')
            ->filterTable('is_published')
            ->call('applyTableColumnManager', $columns, true);

        $instance = $component->instance();
        $expectedValues = [
            $instance->getTablePerPageSessionKey() => 5,
            $instance->getTableSearchSessionKey() => 'saved search',
            $instance->getTableColumnSearchesSessionKey() => ['title' => 'saved column search'],
            $instance->getTableSortSessionKey() => 'title:desc',
            $instance->getTableGroupingSessionKey() => 'title:desc',
            $instance->getTableFiltersSessionKey() => ['is_published' => ['isActive' => true]],
            $instance->getTableColumnsSessionKey() => $columns,
            $instance->getHasReorderedTableColumnsSessionKey() => true,
        ];

        foreach ($expectedValues as $key => $value) {
            expect(PersistenceStoreTableTestComponent::$store->get($key))->toBe($value)
                ->and(session()->exists($key))->toBeFalse();
        }

        livewire($componentClass)
            ->assertSet('tableRecordsPerPage', 5)
            ->assertSet('tableSearch', 'saved search')
            ->assertSet('tableColumnSearches.title', 'saved column search')
            ->assertSet('tableSort', 'title:desc')
            ->assertSet('tableGrouping', 'title:desc')
            ->assertSet('tableFilters.is_published.isActive', true)
            ->assertSet('tableColumns', $columns);
    })->with([
        PersistenceStoreTableTestComponent::class,
        LegacyFeatureKeysPersistenceTableTestComponent::class,
        LegacySharedKeyPersistenceTableTestComponent::class,
        SharedPersistenceKeyTableTestComponent::class,
    ]);

    it('preserves the existing key bytes when reading preexisting state', function (): void {
        $namespace = 'filament.tables.' . hash('sha256', PersistenceStoreTableTestComponent::class) . '.';
        session()->put([
            $namespace . 'search' => 'existing search',
            $namespace . 'column_search' => ['title' => 'existing column search'],
            $namespace . 'filters' => ['is_published' => ['isActive' => true]],
            $namespace . 'sort' => 'title:desc',
            $namespace . 'grouping' => 'title:desc',
            $namespace . 'per_page' => 5,
        ]);
        PersistenceStoreTableTestComponent::$configure = static fn (Table $table): Table => $table->persistInSession();

        $component = livewire(PersistenceStoreTableTestComponent::class)
            ->assertSet('tableSearch', 'existing search')
            ->assertSet('tableColumnSearches.title', 'existing column search')
            ->assertSet('tableFilters.is_published.isActive', true)
            ->assertSet('tableSort', 'title:desc')
            ->assertSet('tableGrouping', 'title:desc')
            ->assertSet('tableRecordsPerPage', 5);
        $columns = array_reverse($component->get('tableColumns'));
        session()->put($namespace . 'columns', $columns);
        session()->put($namespace . 'has_reordered_columns', true);

        livewire(PersistenceStoreTableTestComponent::class)->assertSet('tableColumns', $columns);
    });

    it('honors the last search setter on writes and fresh Livewire mounts', function (bool $sessionLast, bool $enabled): void {
        PersistenceStoreTableTestComponent::$configure = static function (Table $table) use ($sessionLast, $enabled): Table {
            $table->persistInStore(false)->persistenceStore(PersistenceStoreTableTestComponent::$store);

            return $sessionLast
                ? $table->persistSearchInStore()->persistSearchInSession(static fn (): bool => $enabled)
                : $table->persistSearchInSession()->persistSearchInStore(static fn (): bool => $enabled);
        };
        $component = livewire(PersistenceStoreTableTestComponent::class)
            ->set('tableSearch', 'selected route');
        $key = $component->instance()->getTableSearchSessionKey();
        $selectedStore = $sessionLast ? app(SessionTablePersistenceStore::class) : PersistenceStoreTableTestComponent::$store;
        $otherStore = $sessionLast ? PersistenceStoreTableTestComponent::$store : app(SessionTablePersistenceStore::class);

        expect($selectedStore->exists($key))->toBe($enabled)
            ->and($otherStore->exists($key))->toBeFalse();

        if ($enabled) {
            expect($selectedStore->get($key))->toBe('selected route');
        }

        livewire(PersistenceStoreTableTestComponent::class)
            ->assertSet('tableSearch', $enabled ? 'selected route' : '');
    })->with([true, false])->with([true, false]);

    it('independently reads and writes mixed session and custom destinations', function (bool $searchInSession): void {
        PersistenceStoreTableTestComponent::$configure = static function (Table $table) use ($searchInSession): Table {
            $table->persistenceStore(PersistenceStoreTableTestComponent::$store)->persistInStore();

            return $searchInSession
                ? $table->persistSearchInSession()->persistColumnsInSession()->persistRecordsPerPageInSession()
                : $table->persistColumnSearchesInSession()->persistFiltersInSession()->persistSortInSession()->persistGroupInSession();
        };

        $component = livewire(PersistenceStoreTableTestComponent::class);
        $instance = $component->instance();
        $columns = array_reverse($component->get('tableColumns'));
        $selectedValues = [
            $instance->getTableSearchSessionKey() => ['saved search', $searchInSession],
            $instance->getTableColumnsSessionKey() => [$columns, $searchInSession],
            $instance->getHasReorderedTableColumnsSessionKey() => [true, $searchInSession],
            $instance->getTablePerPageSessionKey() => [5, $searchInSession],
            $instance->getTableColumnSearchesSessionKey() => [['title' => 'saved column search'], ! $searchInSession],
            $instance->getTableFiltersSessionKey() => [['is_published' => ['isActive' => true]], ! $searchInSession],
            $instance->getTableSortSessionKey() => ['title:desc', ! $searchInSession],
            $instance->getTableGroupingSessionKey() => ['title:desc', ! $searchInSession],
        ];

        // Seed competing values so a reload cannot accidentally read the other destination.
        foreach ($selectedValues as $key => [$value, $inSession]) {
            if ($inSession) {
                PersistenceStoreTableTestComponent::$store->put($key, null);
            } else {
                session()->put($key, null);
            }
        }

        $component->set('tableSearch', 'saved search')
            ->set('tableColumnSearches.title', 'saved column search')
            ->set('tableSort', 'title:desc')
            ->set('tableGrouping', 'title:desc')
            ->filterTable('is_published')
            ->set('tableRecordsPerPage', 5)
            ->call('applyTableColumnManager', $columns, true);

        foreach ($selectedValues as $key => [$value, $inSession]) {
            $selectedStore = $inSession ? app(SessionTablePersistenceStore::class) : PersistenceStoreTableTestComponent::$store;
            $otherStore = $inSession ? PersistenceStoreTableTestComponent::$store : app(SessionTablePersistenceStore::class);

            expect($selectedStore->get($key))->toBe($value)
                ->and($otherStore->exists($key))->toBeTrue()
                ->and($otherStore->get($key, 'missing'))->toBeNull();
        }

        livewire(PersistenceStoreTableTestComponent::class)
            ->assertSet('tableSearch', 'saved search')
            ->assertSet('tableColumnSearches.title', 'saved column search')
            ->assertSet('tableSort', 'title:desc')
            ->assertSet('tableGrouping', 'title:desc')
            ->assertSet('tableFilters.is_published.isActive', true)
            ->assertSet('tableRecordsPerPage', 5)
            ->assertSet('tableColumns', $columns);
    })->with([true, false]);

    it('keeps an explicit feature store separate from both the table default and session', function (): void {
        $searchStore = new ArrayTablePersistenceStoreForTesting;
        PersistenceStoreTableTestComponent::$configure = static fn (Table $table): Table => $table
            ->persistenceStore(PersistenceStoreTableTestComponent::$store)
            ->persistInStore()
            ->persistSearchInStore(persistenceStore: $searchStore)
            ->persistFiltersInSession();
        $component = livewire(PersistenceStoreTableTestComponent::class);
        $instance = $component->instance();
        $searchKey = $instance->getTableSearchSessionKey();
        $sortKey = $instance->getTableSortSessionKey();
        $filtersKey = $instance->getTableFiltersSessionKey();
        PersistenceStoreTableTestComponent::$store->put($searchKey, 'wrong default search');
        session()->put($searchKey, 'wrong session search');
        $searchStore->put($sortKey, 'title:asc');

        $component->set('tableSearch', 'feature search')
            ->set('tableSort', 'title:desc')
            ->filterTable('is_published');

        expect($searchStore->get($searchKey))->toBe('feature search')
            ->and(PersistenceStoreTableTestComponent::$store->get($searchKey))->toBe('wrong default search')
            ->and(session()->get($searchKey))->toBe('wrong session search')
            ->and(PersistenceStoreTableTestComponent::$store->get($sortKey))->toBe('title:desc')
            ->and($searchStore->get($sortKey))->toBe('title:asc')
            ->and(session()->get($filtersKey))->toBe(['is_published' => ['isActive' => true]])
            ->and(PersistenceStoreTableTestComponent::$store->exists($filtersKey))->toBeFalse()
            ->and($searchStore->exists($filtersKey))->toBeFalse();

        livewire(PersistenceStoreTableTestComponent::class)
            ->assertSet('tableSearch', 'feature search')
            ->assertSet('tableSort', 'title:desc')
            ->assertSet('tableFilters.is_published.isActive', true);
    });

    it('uses the neutral columns key for session and custom stores', function (bool $inSession): void {
        PersistenceStoreTableTestComponent::$configure = static fn (Table $table): Table => $inSession
            ? $table
            : $table->persistColumnsInStore(persistenceStore: PersistenceStoreTableTestComponent::$store);

        $component = livewire(ColumnsPersistenceKeyTableTestComponent::class);
        $columns = array_reverse($component->get('tableColumns'));
        $component->call('applyTableColumnManager', $columns, true);

        $store = $inSession ? app(SessionTablePersistenceStore::class) : PersistenceStoreTableTestComponent::$store;

        expect($store->get('custom.columns'))->toBe($columns);

        livewire(ColumnsPersistenceKeyTableTestComponent::class)->assertSet('tableColumns', $columns);
    })->with([true, false]);

    it('retains neutral and legacy column-loading extension hooks', function (string $componentClass, bool $inSession): void {
        PersistenceStoreTableTestComponent::$configure = static fn (Table $table): Table => $inSession
            ? $table->persistInSession()
            : $table->persistInStore(persistenceStore: PersistenceStoreTableTestComponent::$store);

        livewire($componentClass)
            ->assertSet('tableColumns.0.name', 'title')
            ->assertSet('tableColumns.0.isToggled', false);
    })->with([
        [LegacyColumnLoadingPersistenceTableTestComponent::class, true],
        [ColumnLoadingPersistenceTableTestComponent::class, true],
        [ColumnLoadingPersistenceTableTestComponent::class, false],
    ]);

    it('keeps a persisted `null` grouping instead of restoring the default group', function (): void {
        PersistenceStoreTableTestComponent::$configure = static fn (Table $table): Table => $table
            ->defaultGroup('title')
            ->persistInStore(persistenceStore: PersistenceStoreTableTestComponent::$store);

        $component = livewire(PersistenceStoreTableTestComponent::class)
            ->assertSet('tableGrouping', 'title:asc')
            ->set('tableGrouping', null);
        $key = $component->instance()->getTableGroupingSessionKey();

        expect(PersistenceStoreTableTestComponent::$store->exists($key))->toBeTrue()
            ->and(PersistenceStoreTableTestComponent::$store->get($key, 'missing'))->toBeNull();

        livewire(PersistenceStoreTableTestComponent::class)->assertSet('tableGrouping', null);
    });

    it('forgets an invalid persisted page size only from its selected store', function (bool $inSession): void {
        PersistenceStoreTableTestComponent::$configure = static function (Table $table) use ($inSession): Table {
            $table->persistenceStore(PersistenceStoreTableTestComponent::$store);

            return $inSession ? $table->persistInSession() : $table->persistInStore();
        };
        $key = livewire(PersistenceStoreTableTestComponent::class)->instance()->getTablePerPageSessionKey();
        $selectedStore = $inSession ? app(SessionTablePersistenceStore::class) : PersistenceStoreTableTestComponent::$store;
        $otherStore = $inSession ? PersistenceStoreTableTestComponent::$store : app(SessionTablePersistenceStore::class);
        $selectedStore->put($key, 7);
        $otherStore->put($key, 10);

        livewire(PersistenceStoreTableTestComponent::class)->assertSet('tableRecordsPerPage', 5);

        expect($selectedStore->exists($key))->toBeFalse()
            ->and($otherStore->get($key))->toBe(10);
    })->with([true, false]);

    it('falls back to empty state when nullable features hold `null`', function (): void {
        PersistenceStoreTableTestComponent::$configure = static fn (Table $table): Table => $table
            ->persistInStore(persistenceStore: PersistenceStoreTableTestComponent::$store);
        $component = livewire(PersistenceStoreTableTestComponent::class)->instance();

        foreach ([
            $component->getTableSearchSessionKey(),
            $component->getTableColumnSearchesSessionKey(),
            $component->getTableSortSessionKey(),
            $component->getTableFiltersSessionKey(),
        ] as $key) {
            PersistenceStoreTableTestComponent::$store->put($key, null);
        }

        livewire(PersistenceStoreTableTestComponent::class)
            ->assertSuccessful()
            ->assertSet('tableSearch', '')
            ->assertSet('tableColumnSearches', [])
            ->assertSet('tableSort', null)
            ->assertSet('tableFilters.is_published.isActive', false);
    });
});

class ArrayTablePersistenceStoreForTesting implements TablePersistenceStore
{
    /** @var array<string, mixed> */
    public array $values = [];

    public function exists(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->exists($key) ? $this->values[$key] : $default;
    }

    public function put(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($this->values[$key]);
    }
}

class PersistenceStoreTableTestComponent extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public static ArrayTablePersistenceStoreForTesting $store;

    public static ?Closure $configure = null;

    public function table(Table $table): Table
    {
        $table->query(Post::query())
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
            ->reorderableColumns();

        return static::$configure ? (static::$configure)($table) : $table;
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class LegacyFeatureKeysPersistenceTableTestComponent extends PersistenceStoreTableTestComponent
{
    public function getTableSearchSessionKey(): string
    {
        return 'legacy.search';
    }

    public function getTableColumnSearchesSessionKey(): string
    {
        return 'legacy.column_searches';
    }

    public function getTableFiltersSessionKey(): string
    {
        return 'legacy.filters';
    }

    public function getTableSortSessionKey(): string
    {
        return 'legacy.sort';
    }

    public function getTableGroupingSessionKey(): string
    {
        return 'legacy.grouping';
    }

    public function getTableColumnsSessionKey(): string
    {
        return 'legacy.columns';
    }

    public function getHasReorderedTableColumnsSessionKey(): string
    {
        return 'legacy.has_reordered_columns';
    }

    public function getTablePerPageSessionKey(): string
    {
        return 'legacy.per_page';
    }
}

class LegacySharedKeyPersistenceTableTestComponent extends PersistenceStoreTableTestComponent
{
    public function getTableSessionKey(string $key, bool $isTenantScoped = false): string
    {
        return 'legacy.shared.' . parent::getTableSessionKey($key, $isTenantScoped);
    }
}

class SharedPersistenceKeyTableTestComponent extends PersistenceStoreTableTestComponent
{
    public function getTablePersistenceKey(string $key, bool $isTenantScoped = false): string
    {
        return 'custom.shared.' . parent::getTablePersistenceKey($key, $isTenantScoped);
    }
}

class ColumnsPersistenceKeyTableTestComponent extends PersistenceStoreTableTestComponent
{
    public function getTableColumnsPersistenceKey(): string
    {
        return 'custom.columns';
    }
}

class LegacyColumnLoadingPersistenceTableTestComponent extends PersistenceStoreTableTestComponent
{
    protected function loadTableColumnsFromSession(): array
    {
        $columns = parent::loadTableColumnsFromSession();
        $columns[0]['isToggled'] = false;

        return $columns;
    }
}

class ColumnLoadingPersistenceTableTestComponent extends PersistenceStoreTableTestComponent
{
    protected function loadTableColumnsFromPersistenceStore(): array
    {
        $columns = parent::loadTableColumnsFromPersistenceStore();
        $columns[0]['isToggled'] = false;

        return $columns;
    }
}
