<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Contracts\TableStateStore;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\State\SessionTableStateStore;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    ArrayTableStateStoreForTesting::reset();
});

describe('session table state store', function (): void {
    it('is bound to the `TableStateStore` contract by default', function (): void {
        expect(app(TableStateStore::class))->toBeInstanceOf(SessionTableStateStore::class);
    });

    it('can put and get values from the session', function (): void {
        $store = new SessionTableStateStore;

        $store->put('filament.tables.test.search', 'foo');

        expect($store->get('filament.tables.test.search'))->toBe('foo')
            ->and(session()->get('filament.tables.test.search'))->toBe('foo');
    });

    it('returns the default value when the key is missing', function (): void {
        $store = new SessionTableStateStore;

        expect($store->get('filament.tables.test.missing'))->toBeNull()
            ->and($store->get('filament.tables.test.missing', 'default'))->toBe('default');
    });

    it('reports that a key exists even when its value is `null`', function (): void {
        $store = new SessionTableStateStore;

        expect($store->has('filament.tables.test.grouping'))->toBeFalse();

        $store->put('filament.tables.test.grouping', null);

        expect($store->has('filament.tables.test.grouping'))->toBeTrue();
    });

    it('can forget values', function (): void {
        $store = new SessionTableStateStore;

        $store->put('filament.tables.test.per_page', 5);
        $store->forget('filament.tables.test.per_page');

        expect($store->has('filament.tables.test.per_page'))->toBeFalse()
            ->and(session()->has('filament.tables.test.per_page'))->toBeFalse();
    });

    it('overwrites existing values', function (): void {
        $store = new SessionTableStateStore;

        $store->put('filament.tables.test.sort', 'title:asc');
        $store->put('filament.tables.test.sort', 'title:desc');

        expect($store->get('filament.tables.test.sort'))->toBe('title:desc');
    });

    it('can forget a key that holds `null`', function (): void {
        $store = new SessionTableStateStore;

        $store->put('filament.tables.test.grouping', null);
        $store->forget('filament.tables.test.grouping');

        expect($store->has('filament.tables.test.grouping'))->toBeFalse();
    });

    it('does not fail when forgetting a missing key', function (): void {
        $store = new SessionTableStateStore;

        $store->forget('filament.tables.test.missing');

        expect($store->has('filament.tables.test.missing'))->toBeFalse();
    });

    it('reads values that were written directly to the session', function (): void {
        session()->put('filament.tables.test.search', 'from session');

        $store = new SessionTableStateStore;

        expect($store->has('filament.tables.test.search'))->toBeTrue()
            ->and($store->get('filament.tables.test.search'))->toBe('from session');
    });
});

describe('configuring the state store', function (): void {
    it('defaults to the store bound in the container', function (): void {
        $table = livewire(StateStoreDefaultTableTestComponent::class)->instance()->getTable();

        expect($table->getStateStore())->toBeInstanceOf(SessionTableStateStore::class);
    });

    it('uses the container binding when it is overridden', function (): void {
        app()->bind(TableStateStore::class, ArrayTableStateStoreForTesting::class);

        $table = livewire(StateStoreDefaultTableTestComponent::class)->instance()->getTable();

        expect($table->getStateStore())->toBeInstanceOf(ArrayTableStateStoreForTesting::class);
    });

    it('can set a store instance with `persistStateUsing()`', function (): void {
        $store = new ArrayTableStateStoreForTesting;

        $table = livewire(StateStoreDefaultTableTestComponent::class)->instance()->getTable();

        expect($table->persistStateUsing($store))->toBe($table)
            ->and($table->getStateStore())->toBe($store);
    });

    it('can set a store using a closure with `persistStateUsing()`', function (): void {
        $store = new ArrayTableStateStoreForTesting;

        $table = livewire(StateStoreDefaultTableTestComponent::class)->instance()->getTable()
            ->persistStateUsing(fn (): TableStateStore => $store);

        expect($table->getStateStore())->toBe($store);
    });

    it('falls back to the container binding when a closure returns `null`', function (): void {
        $table = livewire(StateStoreDefaultTableTestComponent::class)->instance()->getTable()
            ->persistStateUsing(fn (): ?TableStateStore => null);

        expect($table->getStateStore())->toBeInstanceOf(SessionTableStateStore::class);
    });

    it('falls back to the container binding when the store is reset to `null`', function (): void {
        $table = livewire(StateStoreDefaultTableTestComponent::class)->instance()->getTable()
            ->persistStateUsing(new ArrayTableStateStoreForTesting)
            ->persistStateUsing(null);

        expect($table->getStateStore())->toBeInstanceOf(SessionTableStateStore::class);
    });

    it('injects the Livewire component into the `persistStateUsing()` closure', function (): void {
        $component = livewire(StateStoreDefaultTableTestComponent::class)->instance();
        $injectedLivewire = null;

        $component->getTable()->persistStateUsing(function ($livewire) use (&$injectedLivewire): TableStateStore {
            $injectedLivewire = $livewire;

            return new ArrayTableStateStoreForTesting;
        });

        expect($component->getTable()->getStateStore())->toBeInstanceOf(ArrayTableStateStoreForTesting::class)
            ->and($injectedLivewire)->toBe($component);
    });
});

describe('persisting table state in a custom store', function (): void {
    it('writes the initial persisted state to the custom store instead of the session on mount', function (): void {
        /** @var StateStoreTableTestComponent $component */
        $component = livewire(StateStoreTableTestComponent::class)->instance();

        $keys = [
            $component->getTableFiltersSessionKey(),
            $component->getTableGroupingSessionKey(),
            $component->getTableSearchSessionKey(),
            $component->getTableColumnSearchesSessionKey(),
            $component->getTableSortSessionKey(),
        ];

        foreach ($keys as $key) {
            expect(ArrayTableStateStoreForTesting::$values)->toHaveKey($key)
                ->and(session()->has($key))->toBeFalse();
        }
    });

    it('writes the search to the custom store', function (): void {
        $component = livewire(StateStoreTableTestComponent::class)
            ->set('tableSearch', 'saved search');

        $key = $component->instance()->getTableSearchSessionKey();

        expect(ArrayTableStateStoreForTesting::$values[$key])->toBe('saved search')
            ->and(session()->has($key))->toBeFalse();
    });

    it('writes the column searches to the custom store', function (): void {
        $component = livewire(StateStoreTableTestComponent::class)
            ->set('tableColumnSearches.title', 'saved column search');

        $key = $component->instance()->getTableColumnSearchesSessionKey();

        expect(ArrayTableStateStoreForTesting::$values[$key]['title'] ?? null)->toBe('saved column search')
            ->and(session()->has($key))->toBeFalse();
    });

    it('writes the sort to the custom store', function (): void {
        $component = livewire(StateStoreTableTestComponent::class)
            ->set('tableSort', 'title:desc');

        $key = $component->instance()->getTableSortSessionKey();

        expect(ArrayTableStateStoreForTesting::$values[$key])->toBe('title:desc')
            ->and(session()->has($key))->toBeFalse();
    });

    it('writes the grouping to the custom store', function (): void {
        $component = livewire(StateStoreTableTestComponent::class)
            ->set('tableGrouping', 'title:desc');

        $key = $component->instance()->getTableGroupingSessionKey();

        expect(ArrayTableStateStoreForTesting::$values[$key])->toBe('title:desc')
            ->and(session()->has($key))->toBeFalse();
    });

    it('writes the filters to the custom store', function (): void {
        $component = livewire(StateStoreTableTestComponent::class)
            ->filterTable('is_published');

        $key = $component->instance()->getTableFiltersSessionKey();

        expect(ArrayTableStateStoreForTesting::$values[$key]['is_published']['isActive'] ?? null)->toBeTrue()
            ->and(session()->has($key))->toBeFalse();
    });

    it('writes the records per page to the custom store', function (): void {
        $component = livewire(StateStoreTableTestComponent::class)
            ->set('tableRecordsPerPage', 5);

        $key = $component->instance()->getTablePerPageSessionKey();

        expect(ArrayTableStateStoreForTesting::$values[$key])->toBe(5)
            ->and(session()->has($key))->toBeFalse();
    });

    it('writes the column manager state to the custom store', function (): void {
        $component = livewire(StateStoreTableTestComponent::class);
        $reorderedColumns = array_reverse($component->get('tableColumns'));

        $component->call('applyTableColumnManager', $reorderedColumns, true);

        $columnsKey = $component->instance()->getTableColumnsSessionKey();
        $hasReorderedColumnsKey = $component->instance()->getHasReorderedTableColumnsSessionKey();

        expect(ArrayTableStateStoreForTesting::$values[$columnsKey])->toBe($reorderedColumns)
            ->and(ArrayTableStateStoreForTesting::$values[$hasReorderedColumnsKey])->toBeTrue()
            ->and(session()->has($columnsKey))->toBeFalse()
            ->and(session()->has($hasReorderedColumnsKey))->toBeFalse();
    });

    it('restores every persisted feature from the custom store across reloads', function (): void {
        Post::factory()->count(3)->create();

        $component = livewire(StateStoreTableTestComponent::class);
        $reorderedColumns = array_reverse($component->get('tableColumns'));

        $component
            ->set('tableRecordsPerPage', 5)
            ->set('tableSearch', 'first search')
            ->set('tableColumnSearches.title', 'first column search')
            ->set('tableSort', 'title:desc')
            ->set('tableGrouping', 'title:desc')
            ->filterTable('is_published')
            ->call('applyTableColumnManager', $reorderedColumns, true);

        livewire(StateStoreTableTestComponent::class)
            ->assertSet('tableRecordsPerPage', 5)
            ->assertSet('tableSearch', 'first search')
            ->assertSet('tableColumnSearches.title', 'first column search')
            ->assertSet('tableSort', 'title:desc')
            ->assertSet('tableGrouping', 'title:desc')
            ->assertSet('tableFilters.is_published.isActive', true)
            ->assertSet('tableColumns', $reorderedColumns);
    });

    it('restores every persisted feature from values already in the custom store', function (): void {
        $component = livewire(StateStoreTableTestComponent::class)->instance();
        $reorderedColumns = array_reverse($component->tableColumns);

        ArrayTableStateStoreForTesting::$values = [
            $component->getTablePerPageSessionKey() => 5,
            $component->getTableSearchSessionKey() => 'saved search',
            $component->getTableColumnSearchesSessionKey() => ['title' => 'saved column search'],
            $component->getTableSortSessionKey() => 'title:desc',
            $component->getTableGroupingSessionKey() => 'title:desc',
            $component->getTableFiltersSessionKey() => ['is_published' => ['isActive' => true]],
            $component->getTableColumnsSessionKey() => $reorderedColumns,
            $component->getHasReorderedTableColumnsSessionKey() => true,
        ];

        livewire(StateStoreTableTestComponent::class)
            ->assertSet('tableRecordsPerPage', 5)
            ->assertSet('tableSearch', 'saved search')
            ->assertSet('tableColumnSearches.title', 'saved column search')
            ->assertSet('tableSort', 'title:desc')
            ->assertSet('tableGrouping', 'title:desc')
            ->assertSet('tableFilters.is_published.isActive', true)
            ->assertSet('tableColumns', $reorderedColumns);
    });

    it('does not read persisted state from the session when a custom store is used', function (): void {
        $component = livewire(StateStoreTableTestComponent::class)->instance();

        ArrayTableStateStoreForTesting::reset();

        session()->put([
            $component->getTablePerPageSessionKey() => 5,
            $component->getTableSearchSessionKey() => 'session search',
            $component->getTableColumnSearchesSessionKey() => ['title' => 'session column search'],
            $component->getTableSortSessionKey() => 'title:desc',
            $component->getTableGroupingSessionKey() => 'title:desc',
            $component->getTableFiltersSessionKey() => ['is_published' => ['isActive' => true]],
        ]);

        livewire(StateStoreTableTestComponent::class)
            ->assertSet('tableRecordsPerPage', 10)
            ->assertSet('tableSearch', '')
            ->assertSet('tableColumnSearches', [])
            ->assertSet('tableSort', null)
            ->assertSet('tableGrouping', null)
            ->assertSet('tableFilters.is_published.isActive', false);
    });

    it('uses a store bound in the container for every table', function (): void {
        app()->instance(TableStateStore::class, new ArrayTableStateStoreForTesting);

        $component = livewire(StateStoreDefaultTableTestComponent::class)
            ->set('tableSearch', 'container search');

        $key = $component->instance()->getTableSearchSessionKey();

        expect(ArrayTableStateStoreForTesting::$values[$key])->toBe('container search')
            ->and(session()->has($key))->toBeFalse();

        livewire(StateStoreDefaultTableTestComponent::class)
            ->assertSet('tableSearch', 'container search');
    });

    it('does not write to the custom store when persistence is disabled', function (): void {
        livewire(StateStoreTableTestComponent::class, ['persists' => false])
            ->set('tableSearch', 'search')
            ->set('tableSort', 'title:desc')
            ->set('tableRecordsPerPage', 5);

        expect(ArrayTableStateStoreForTesting::$values)->toBe([]);
    });

    it('forgets an invalid persisted records per page value from the custom store', function (): void {
        $key = livewire(StateStoreTableTestComponent::class)->instance()->getTablePerPageSessionKey();

        ArrayTableStateStoreForTesting::$values[$key] = 7;

        livewire(StateStoreTableTestComponent::class)
            ->assertSet('tableRecordsPerPage', 5);

        expect(ArrayTableStateStoreForTesting::$values)->not->toHaveKey($key)
            ->and(ArrayTableStateStoreForTesting::$forgotten)->toContain($key);
    });

    it('keeps a persisted `null` grouping over the default group using `has()`', function (): void {
        livewire(StateStoreTableTestComponent::class, ['hasDefaultGroup' => true])
            ->assertSet('tableGrouping', 'title:asc')
            ->set('tableGrouping', null);

        $key = livewire(StateStoreTableTestComponent::class, ['hasDefaultGroup' => true])
            ->assertSet('tableGrouping', null)
            ->instance()
            ->getTableGroupingSessionKey();

        expect(ArrayTableStateStoreForTesting::$values)->toHaveKey($key)
            ->and(ArrayTableStateStoreForTesting::$values[$key])->toBeNull();
    });

    it('writes the sort to the custom store when using `sortTable()`', function (): void {
        $component = livewire(StateStoreTableTestComponent::class)
            ->call('sortTable', 'title', 'desc');

        $key = $component->instance()->getTableSortSessionKey();

        expect(ArrayTableStateStoreForTesting::$values[$key])->toBe('title:desc')
            ->and(session()->has($key))->toBeFalse();
    });

    it('only writes the features that are persisted to the custom store', function (): void {
        $component = livewire(StateStoreSearchOnlyTableTestComponent::class)
            ->set('tableSearch', 'only search')
            ->set('tableSort', 'title:desc')
            ->set('tableRecordsPerPage', 5);

        $instance = $component->instance();

        expect(ArrayTableStateStoreForTesting::$values)->toBe([
            $instance->getTableSearchSessionKey() => 'only search',
        ]);

        livewire(StateStoreSearchOnlyTableTestComponent::class)
            ->assertSet('tableSearch', 'only search')
            ->assertSet('tableSort', null)
            ->assertSet('tableRecordsPerPage', 10);
    });

    it('does not persist the column manager state to the custom store when columns are not persisted', function (): void {
        $component = livewire(StateStoreSearchOnlyTableTestComponent::class);
        $reorderedColumns = array_reverse($component->get('tableColumns'));

        $component->call('applyTableColumnManager', $reorderedColumns, true);

        expect(ArrayTableStateStoreForTesting::$values)
            ->not->toHaveKey($component->instance()->getTableColumnsSessionKey())
            ->not->toHaveKey($component->instance()->getHasReorderedTableColumnsSessionKey());
    });

    it('keeps using the session for tables without a custom store', function (): void {
        livewire(StateStoreTableTestComponent::class)
            ->set('tableSearch', 'array store search');

        $key = livewire(StateStoreDefaultTableTestComponent::class)
            ->assertSet('tableSearch', '')
            ->instance()
            ->getTableSearchSessionKey();

        expect(session()->get($key))->toBe('');
    });

    it('applies the default group when nothing is persisted in the custom store', function (): void {
        livewire(StateStoreTableTestComponent::class, ['hasDefaultGroup' => true])
            ->assertSet('tableGrouping', 'title:asc');
    });

    it('falls back to empty state when the custom store holds `null` values', function (): void {
        $component = livewire(StateStoreTableTestComponent::class)->instance();

        ArrayTableStateStoreForTesting::$values = [
            $component->getTableSearchSessionKey() => null,
            $component->getTableColumnSearchesSessionKey() => null,
            $component->getTableSortSessionKey() => null,
            $component->getTableFiltersSessionKey() => null,
        ];

        livewire(StateStoreTableTestComponent::class)
            ->assertSuccessful()
            ->assertSet('tableSearch', '')
            ->assertSet('tableColumnSearches', [])
            ->assertSet('tableSort', null)
            ->assertSet('tableFilters.is_published.isActive', false);
    });
});

class ArrayTableStateStoreForTesting implements TableStateStore
{
    /**
     * @var array<string, mixed>
     */
    public static array $values = [];

    /**
     * @var array<string>
     */
    public static array $forgotten = [];

    public static function reset(): void
    {
        static::$values = [];
        static::$forgotten = [];
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, static::$values);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? static::$values[$key] : $default;
    }

    public function put(string $key, mixed $value): void
    {
        static::$values[$key] = $value;
    }

    public function forget(string $key): void
    {
        static::$forgotten[] = $key;

        unset(static::$values[$key]);
    }
}

class StateStoreDefaultTableTestComponent extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
            ])
            ->persistInSession();
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class StateStoreTableTestComponent extends StateStoreDefaultTableTestComponent
{
    public bool $persists = true;

    public bool $hasDefaultGroup = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
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
            ->defaultGroup($this->hasDefaultGroup ? 'title' : null)
            ->paginationPageOptions([5, 10])
            ->defaultPaginationPageOption(10)
            ->reorderableColumns()
            ->persistInSession($this->persists)
            ->persistStateUsing(new ArrayTableStateStoreForTesting);
    }
}

class StateStoreSearchOnlyTableTestComponent extends StateStoreDefaultTableTestComponent
{
    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('content')
                    ->toggleable(),
            ])
            ->paginationPageOptions([5, 10])
            ->defaultPaginationPageOption(10)
            ->reorderableColumns()
            ->persistColumnsInSession(false)
            ->persistRecordsPerPageInSession(false)
            ->persistSearchInSession()
            ->persistStateUsing(new ArrayTableStateStoreForTesting);
    }
}
