<?php

namespace Filament\Tests\Support;

use Filament\Actions\Testing\TestAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\State\LazyLoadedState;
use Filament\Support\State\LazyLoadedStateGroup;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Pages\LazyLoadedRepeatableEntryBrowserTest;
use Filament\Tests\Fixtures\Pages\LazyLoadedStateBrowserTest;
use Filament\Tests\TestCase;
use InvalidArgumentException;
use stdClass;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('defers state resolution and preserves formatting when loading columns and entries', function (string $type): void {
    $loads = 0;
    $component = $type::make('value')
        ->state(function () use (&$loads): string {
            $loads++;

            return 'alpha,beta';
        })
        ->separator(',')
        ->formatStateUsing(static fn (string $state): string => strtoupper($state))
        ->lazyLoadedState();

    if ($component instanceof TextEntry) {
        $component->container(Schema::make(Livewire::make()));
    } else {
        $component->table(livewire(LazyLoadedStateBrowserTest::class)->instance()->getTable())->record(Post::factory()->create());
    }

    expect($component->toHtml())->not->toContain('ALPHA')
        ->and($component->getState())->toBeNull()
        ->and($loads)->toBe(0);

    $html = app(LazyLoadedState::class)->render(['value' => $component]);

    expect($html['value'])->toContain('ALPHA', 'BETA')
        ->and($loads)->toBe(1);

    $component->lazyLoadedState(false);

    expect($component->getState())->toBe(['alpha', 'beta']);
})->with(['column' => [TextColumn::class], 'entry' => [TextEntry::class]]);

it('uses a grouped null value instead of falling back to record state', function (string $type): void {
    $component = $type::make('value')
        ->default('Empty result')
        ->lazyLoadedState(group: LazyLoadedStateGroup::make('null')->state(static fn (): array => ['value' => null]));

    if ($component instanceof TextEntry) {
        $component->container(Schema::make(Livewire::make())->constantState(['value' => 'Record fallback']));
    } else {
        $component->table(livewire(LazyLoadedStateBrowserTest::class)->instance()->getTable())
            ->record(['__key' => 'example', 'value' => 'Record fallback']);
    }

    $html = app(LazyLoadedState::class)->render(['value' => $component]);

    expect($html['value'])->toContain('Empty result')->not->toContain('Record fallback');
})->with(['column' => [TextColumn::class], 'entry' => [TextEntry::class]]);

it('loads a column or row with one keyed loader and no eager state callbacks', function (): void {
    $livewire = livewire(LazyLoadedStateBrowserTest::class)->assertSuccessful();
    LazyLoadedStateBrowserTest::$groupLoads = 0;

    $livewire->call('loadTableColumnStates', [
        'alpha' => ['column' => 'column', 'record' => '11'],
        'beta' => ['column' => 'column', 'record' => '37'],
    ])->assertReturned(fn (array $html): bool => str_contains($html['alpha'], 'Column Alpha / 2') && str_contains($html['beta'], 'Column Beta / 2'));

    expect(LazyLoadedStateBrowserTest::$groupLoads)->toBe(1);

    $livewire->call('loadTableColumnStates', [
        'first' => ['column' => 'first', 'record' => '37'],
        'second' => ['column' => 'second', 'record' => '37'],
    ])->assertReturned(fn (array $html): bool => str_contains($html['first'], 'First Beta') && str_contains($html['second'], 'Second Beta'));

    expect(LazyLoadedStateBrowserTest::$groupLoads)->toBe(2);
});

it('loads grouped entries with zero and null values through their normal formatters and defaults', function (): void {
    $livewire = livewire(LazyLoadedStateBrowserTest::class);
    LazyLoadedStateBrowserTest::$groupLoads = 0;

    $livewire->call('callSchemaComponentMethod', 'infolist.total', 'loadState', [[
        'total' => ['entry' => 'infolist.total'],
        'missing' => ['entry' => 'infolist.missing'],
    ]])->assertReturned(fn (array $html): bool => str_contains($html['total'], 'Total: 0') && str_contains($html['missing'], 'No result'));

    expect(LazyLoadedStateBrowserTest::$groupLoads)->toBe(1);
});

it('reconstructs only the requested lazy parents for nested entries and actions', function (bool $grouped): void {
    $livewire = livewire(LazyLoadedRepeatableEntryBrowserTest::class, ['grouped' => $grouped]);
    LazyLoadedRepeatableEntryBrowserTest::$parentLoads = 0;
    LazyLoadedRepeatableEntryBrowserTest::$packageLoads = 0;
    LazyLoadedRepeatableEntryBrowserTest::$unrelatedLoads = 0;

    $parentKey = 'infolist.deliveries.orders';
    $packageKey = "{$parentKey}.17.packages";
    $childKey = "{$packageKey}.box.reference";

    $livewire->call('callSchemaComponentMethod', $parentKey, 'loadState', [[
        'orders' => ['entry' => $parentKey],
    ]])->assertReturned(static fn (array $html): bool => str_contains($html['orders'], 'Parcel 1048') && str_contains($html['orders'], $packageKey));

    expect(LazyLoadedRepeatableEntryBrowserTest::$parentLoads)->toBe(1)
        ->and(LazyLoadedRepeatableEntryBrowserTest::$packageLoads)->toBe(0);

    $livewire->call('callSchemaComponentMethod', $packageKey, 'loadState', [[
        'packages' => ['entry' => $packageKey],
    ]])->assertReturned(static fn (array $html): bool => str_contains($html['packages'], $childKey));

    expect(LazyLoadedRepeatableEntryBrowserTest::$parentLoads)->toBe(2)
        ->and(LazyLoadedRepeatableEntryBrowserTest::$packageLoads)->toBe(1);

    $livewire->call('callSchemaComponentMethod', $childKey, 'loadState', [[
        'reference' => ['entry' => $childKey],
    ]])->assertReturned(static fn (array $html): bool => str_contains($html['reference'], 'Parcel 1048 / box v1'));

    expect(LazyLoadedRepeatableEntryBrowserTest::$parentLoads)->toBe(3)
        ->and(LazyLoadedRepeatableEntryBrowserTest::$packageLoads)->toBe(2)
        ->and(LazyLoadedRepeatableEntryBrowserTest::$unrelatedLoads)->toBe(0);

    $livewire->mountAction(TestAction::make('selectParcel')->schemaComponent('deliveries.orders.39.packages.box.reference', 'infolist'))
        ->assertActionMounted(TestAction::make('selectParcel')->schemaComponent('deliveries.orders.39.packages.box.reference', 'infolist'))
        ->callMountedAction()
        ->assertSet('selectedParcel', 'Parcel 1056');
})->with(['independent parent' => false, 'grouped parent' => true]);

it('loads lazy item schemas beneath nested lazy parents before resolving their entry state', function (bool $grouped): void {
    $livewire = livewire(LazyLoadedRepeatableEntryBrowserTest::class, ['grouped' => $grouped, 'lazyItemSchemas' => true]);
    LazyLoadedRepeatableEntryBrowserTest::$unrelatedLoads = 0;

    $livewire->call('callSchemaComponentMethod', 'infolist.deliveries.orders', 'loadState', [[
        'orders' => ['entry' => 'infolist.deliveries.orders'],
    ]])->assertReturned(static fn (array $html): bool => str_contains($html['orders'], 'schema.infolist.deliveries.orders.17'));

    foreach (['infolist.deliveries.orders.17', 'infolist.deliveries.orders.17.packages.box'] as $schemaKey) {
        $livewire->call('loadLazySchema', $schemaKey);

        expect($livewire->get('loadedLazySchemas'))->toHaveKey($schemaKey, true);
    }

    $childKey = 'infolist.deliveries.orders.17.packages.box.reference';

    $livewire->call('callSchemaComponentMethod', $childKey, 'loadState', [[
        'reference' => ['entry' => $childKey],
    ]])->assertReturned(static fn (array $html): bool => str_contains($html['reference'], 'Parcel 1048 / box v1'));

    expect(LazyLoadedRepeatableEntryBrowserTest::$unrelatedLoads)->toBe(0);
})->with(['independent parent' => false, 'grouped parent' => true]);

it('preserves grouped parent defaults when reconstructing descendants', function (bool $useDefaultOrders): void {
    $livewire = livewire(LazyLoadedRepeatableEntryBrowserTest::class, [
        'useDefaultOrders' => $useDefaultOrders,
        'useDefaultPackages' => ! $useDefaultOrders,
    ]);
    $parentKey = 'infolist.deliveries.orders';
    $itemKey = $useDefaultOrders ? 'fallback' : '17';
    $packageKey = "{$parentKey}.{$itemKey}.packages";
    $childKey = "{$packageKey}.box.reference";
    $expectedReference = $useDefaultOrders ? 'Fallback parcel / box v1' : 'Fallback box';

    $livewire->call('callSchemaComponentMethod', $parentKey, 'loadState', [[
        'orders' => ['entry' => $parentKey],
    ]])->assertReturned(static fn (array $html): bool => str_contains($html['orders'], $packageKey));

    $livewire->call('callSchemaComponentMethod', $packageKey, 'loadState', [[
        'packages' => ['entry' => $packageKey],
    ]])->assertReturned(static fn ($html): bool => is_array($html) && str_contains($html['packages'], $childKey));

    $livewire->call('callSchemaComponentMethod', $childKey, 'loadState', [[
        'reference' => ['entry' => $childKey],
    ]])->assertReturned(static fn ($html): bool => is_array($html) && str_contains($html['reference'], $expectedReference));
})->with(['null parent state' => true, 'empty nested parent state' => false]);

it('uses current parent state and visibility when resolving descendants', function (): void {
    $livewire = livewire(LazyLoadedRepeatableEntryBrowserTest::class);
    $childKey = 'infolist.deliveries.orders.17.packages.box.reference';

    $livewire->set('revision', 2)
        ->call('callSchemaComponentMethod', $childKey, 'loadState', [[
            'reference' => ['entry' => $childKey],
        ]])->assertReturned(static fn (array $html): bool => str_contains($html['reference'], 'Parcel 1048 / box v2'));

    $livewire->set('emptyOrders', true)
        ->call('callSchemaComponentMethod', $childKey, 'loadState', [[]])
        ->assertReturned(null);

    $livewire->set('emptyOrders', false)->set('hideOrders', true);
    LazyLoadedRepeatableEntryBrowserTest::$parentLoads = 0;
    LazyLoadedRepeatableEntryBrowserTest::$packageLoads = 0;

    $livewire->call('callSchemaComponentMethod', $childKey, 'loadState', [[]])->assertReturned(null);

    expect(LazyLoadedRepeatableEntryBrowserTest::$parentLoads)->toBe(0)
        ->and(LazyLoadedRepeatableEntryBrowserTest::$packageLoads)->toBe(0);
});

it('resolves model groups from the current filtered page with the original row context', function (): void {
    $first = Post::factory()->create(['title' => 'First result']);
    $second = Post::factory()->create(['title' => 'Second result']);
    $third = Post::factory()->create(['title' => 'Third result']);

    $livewire = livewire(LazyLoadedStateModelTable::class);

    $livewire->call('loadTableColumnStates', [
        'first' => ['column' => 'title', 'record' => (string) $first->getKey()],
        'second' => ['column' => 'title', 'record' => (string) $second->getKey()],
    ])->assertReturned(fn (array $html): bool => str_contains($html['first'], '1: First result') && str_contains($html['second'], '2: Second result'));

    livewire(LazyLoadedStateModelTable::class)
        ->call('loadTableColumnStates', ['third' => ['column' => 'title', 'record' => (string) $third->getKey()]])
        ->assertNotFound();

    $livewire->set('tableSearch', 'Third result')
        ->call('loadTableColumnStates', ['third' => ['column' => 'title', 'record' => (string) $third->getKey()]])
        ->assertReturned(fn (array $html): bool => str_contains($html['third'], '1: Third result'));
});

it('rejects targets which are not configured for the requested lazy group', function (string $method, array $targets): void {
    $arguments = ($method === 'callSchemaComponentMethod')
        ? ['infolist.independent', 'loadState', [$targets]]
        : [$targets];

    livewire(LazyLoadedStateBrowserTest::class)
        ->call($method, ...$arguments)
        ->assertForbidden();
})->with([
    'eager entry' => ['callSchemaComponentMethod', ['value' => ['entry' => 'infolist.eager']]],
    'hidden entry' => ['callSchemaComponentMethod', ['value' => ['entry' => 'infolist.private']]],
    'eager column' => ['loadTableColumnStates', ['value' => ['column' => 'name', 'record' => '11']]],
    'hidden column' => ['loadTableColumnStates', ['value' => ['column' => 'private', 'record' => '11']]],
    'different rows' => ['loadTableColumnStates', [
        'alpha' => ['column' => 'first', 'record' => '11'],
        'beta' => ['column' => 'second', 'record' => '37'],
    ]],
    'independent columns' => ['loadTableColumnStates', [
        'alpha' => ['column' => 'individual', 'record' => '11'],
        'beta' => ['column' => 'individual', 'record' => '37'],
    ]],
]);

it('rejects missing and duplicate group keys instead of silently falling back to eager state', function (bool $duplicate): void {
    $group = LazyLoadedStateGroup::make('invalid')->state(static fn (): array => []);
    $first = TextColumn::make('first')->lazyLoadedState(group: $group);
    $second = TextColumn::make($duplicate ? 'first' : 'second')->lazyLoadedState(group: $group);

    expect(fn (): array => app(LazyLoadedState::class)->render(['first' => $first, 'second' => $second]))
        ->toThrow(InvalidArgumentException::class, $duplicate ? 'duplicate key' : 'did not return key');
})->with([true, false]);

it('respects ancestor table layout visibility for each record before running a group loader', function (): void {
    $livewire = livewire(LazyLoadedStateVisibilityPage::class);
    LazyLoadedStateBrowserTest::$groupLoads = 0;

    $livewire->call('loadTableColumnStates', [
        'visible' => ['column' => 'restricted', 'record' => '11'],
    ])->assertReturned(fn (array $html): bool => str_contains($html['visible'], 'Visible Alpha'));

    expect(LazyLoadedStateBrowserTest::$groupLoads)->toBe(1);

    $livewire->call('loadTableColumnStates', [
        'hidden' => ['column' => 'restricted', 'record' => '37'],
    ])->assertForbidden();

    expect(LazyLoadedStateBrowserTest::$groupLoads)->toBe(1);
});

it('rejects lazy entry targets inside hidden schemas before running a group loader', function (string $schemaName): void {
    $livewire = livewire(LazyLoadedStateVisibilityPage::class);
    LazyLoadedStateBrowserTest::$groupLoads = 0;

    $livewire->call('callSchemaComponentMethod', 'infolist.independent', 'loadState', [[
        'hidden' => ['entry' => "{$schemaName}.restricted"],
    ]])->assertForbidden();

    expect(LazyLoadedStateBrowserTest::$groupLoads)->toBe(0);
})->with(['hiddenInfolist', 'hiddenChildInfolist', 'hiddenAncestorInfolist']);

class LazyLoadedStateVisibilityPage extends LazyLoadedStateBrowserTest
{
    public function table(Table $table): Table
    {
        return parent::table($table)->columns([
            Stack::make([
                Stack::make([
                    TextColumn::make('restricted')
                        ->lazyLoadedState(group: LazyLoadedStateGroup::make('restricted')->state(function (array $record): array {
                            static::$groupLoads++;

                            return ['restricted' => "Visible {$record['name']}"];
                        })),
                ]),
            ])->hidden(static fn (array $record, stdClass $rowLoop): bool => ($record['id'] !== 11) || ($rowLoop->iteration !== 1)),
        ]);
    }

    public function hiddenInfolist(Schema $schema): Schema
    {
        return $schema->hidden()->components([$this->restrictedEntry()]);
    }

    public function hiddenChildInfolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema(Schema::make()->hidden()->components([$this->restrictedEntry()])),
        ]);
    }

    public function hiddenAncestorInfolist(Schema $schema): Schema
    {
        return $schema->hidden()->components([
            Section::make()->schema([$this->restrictedEntry()]),
        ]);
    }

    protected function restrictedEntry(): TextEntry
    {
        return TextEntry::make('restricted')->lazyLoadedState(group: LazyLoadedStateGroup::make('restricted')->state(function (): array {
            static::$groupLoads++;

            return ['restricted' => 'Hidden schema value'];
        }));
    }
}

class LazyLoadedStateModelTable extends LazyLoadedStateBrowserTest
{
    public function table(Table $table): Table
    {
        $group = LazyLoadedStateGroup::make('models')
            ->state(static fn (array $records): array => array_map(static fn (Post $record): string => $record->title, $records));

        return $table
            ->query(Post::query())
            ->defaultSort('id')
            ->paginated([2])
            ->defaultPaginationPageOption(2)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->lazyLoadedState(group: $group, key: static fn (Post $record): string => (string) $record->getKey())
                    ->formatStateUsing(static fn (string $state, stdClass $rowLoop): string => "{$rowLoop->iteration}: {$state}"),
            ]);
    }
}
