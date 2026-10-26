<?php

use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\SelectablePostsTable;
use Filament\Tests\Fixtures\Livewire\TicketDepartmentsReorderableTable;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

describe('action lifecycle and configuration', function (): void {
    it('can call a bulk action', function (): void {
        $posts = Post::factory()->count(10)->create();

        livewire(PostsTable::class)
            ->selectTableRecords($posts)
            ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk());

        foreach ($posts as $post) {
            assertSoftDeleted($post);
        }

        $posts = Post::factory()->count(10)->create();

        livewire(PostsTable::class)
            ->callTableBulkAction(DeleteBulkAction::class, $posts);

        foreach ($posts as $post) {
            assertSoftDeleted($post);
        }
    });

    it('can call a bulk action with data', function (): void {
        $posts = Post::factory()->count(10)->create();

        livewire(PostsTable::class)
            ->selectTableRecords($posts)
            ->callAction(TestAction::make('data')->table()->bulk(), data: [
                'payload' => $payload = Str::random(),
            ])
            ->assertHasNoFormErrors()
            ->assertDispatched('data-called', data: [
                'payload' => $payload,
            ]);

        livewire(PostsTable::class)
            ->callTableBulkAction('data', records: $posts, data: [
                'payload' => $payload = Str::random(),
            ])
            ->assertHasNoTableBulkActionErrors()
            ->assertDispatched('data-called', data: [
                'payload' => $payload,
            ]);
    });

    it('can validate a bulk action\'s data', function (): void {
        $posts = Post::factory()->count(10)->create();

        livewire(PostsTable::class)
            ->selectTableRecords($posts)
            ->callAction(TestAction::make('data')->table()->bulk(), data: [
                'payload' => null,
            ])
            ->assertHasFormErrors(['payload' => ['required']])
            ->assertNotDispatched('data-called');

        livewire(PostsTable::class)
            ->callTableBulkAction('data', records: $posts, data: [
                'payload' => null,
            ])
            ->assertHasTableBulkActionErrors(['payload' => ['required']])
            ->assertNotDispatched('data-called');
    });

    it('can set default bulk action data when mounted', function (): void {
        $posts = Post::factory()->count(10)->create();

        livewire(PostsTable::class)
            ->selectTableRecords($posts)
            ->mountAction(TestAction::make('data')->table()->bulk())
            ->assertSchemaStateSet([
                'foo' => 'bar',
            ]);

        livewire(PostsTable::class)
            ->mountTableBulkAction('data', records: $posts)
            ->assertTableBulkActionDataSet([
                'foo' => 'bar',
            ])
            ->assertTableBulkActionDataSet(function (array $data): bool {
                return $data['foo'] === 'bar';
            });
    });

    it('can call a bulk action with arguments', function (): void {
        $posts = Post::factory()->count(10)->create();

        livewire(PostsTable::class)
            ->selectTableRecords($posts)
            ->callAction(TestAction::make('arguments')->arguments([
                'payload' => $payload = Str::random(),
            ])->table()->bulk())
            ->assertDispatched('arguments-called', arguments: [
                'payload' => $payload,
            ]);

        livewire(PostsTable::class)
            ->callTableBulkAction('arguments', records: $posts, arguments: [
                'payload' => $payload = Str::random(),
            ])
            ->assertDispatched('arguments-called', arguments: [
                'payload' => $payload,
            ]);
    });

    it('can call a bulk action and halt', function (): void {
        $posts = Post::factory()->count(10)->create();

        livewire(PostsTable::class)
            ->selectTableRecords($posts)
            ->callAction(TestAction::make('halt')->table()->bulk())
            ->assertDispatched('halt-called')
            ->assertActionHalted(TestAction::make('halt')->table()->bulk());

        livewire(PostsTable::class)
            ->callTableBulkAction('halt', records: $posts)
            ->assertDispatched('halt-called')
            ->assertTableBulkActionHalted('halt');
    });

    it('can hide a bulk action', function (): void {
        livewire(PostsTable::class)
            ->assertActionVisible(TestAction::make('visible')->table()->bulk())
            ->assertActionHidden(TestAction::make('hidden')->table()->bulk());

        livewire(PostsTable::class)
            ->assertTableBulkActionVisible('visible')
            ->assertTableBulkActionHidden('hidden');
    });

    it('can disable a bulk action', function (): void {
        livewire(PostsTable::class)
            ->assertActionEnabled(TestAction::make('enabled')->table()->bulk())
            ->assertActionDisabled(TestAction::make('disabled')->table()->bulk());

        livewire(PostsTable::class)
            ->assertTableBulkActionEnabled('enabled')
            ->assertTableBulkActionDisabled('disabled');
    });

    it('can have an icon', function (): void {
        livewire(PostsTable::class)
            ->assertActionHasIcon(TestAction::make('hasIcon')->table()->bulk(), Heroicon::PencilSquare)
            ->assertActionDoesNotHaveIcon(TestAction::make('hasIcon')->table()->bulk(), Heroicon::Trash);

        livewire(PostsTable::class)
            ->assertTableBulkActionHasIcon('hasIcon', Heroicon::PencilSquare)
            ->assertTableBulkActionDoesNotHaveIcon('hasIcon', Heroicon::Trash);
    });

    it('can have a label', function (): void {
        livewire(PostsTable::class)
            ->assertActionHasLabel(TestAction::make('hasLabel')->table()->bulk(), 'My Action')
            ->assertActionDoesNotHaveLabel(TestAction::make('hasLabel')->table()->bulk(), 'My Other Action');

        livewire(PostsTable::class)
            ->assertTableBulkActionHasLabel('hasLabel', 'My Action')
            ->assertTableBulkActionDoesNotHaveLabel('hasLabel', 'My Other Action');
    });

    it('can have a color', function (): void {
        livewire(PostsTable::class)
            ->assertActionHasColor(TestAction::make('hasColor')->table()->bulk(), 'primary')
            ->assertActionDoesNotHaveColor(TestAction::make('hasColor')->table()->bulk(), 'gray');

        livewire(PostsTable::class)
            ->assertTableBulkActionHasColor('hasColor', 'primary')
            ->assertTableBulkActionDoesNotHaveColor('hasColor', 'gray');
    });

    it('can state whether a bulk action exists', function (): void {
        livewire(PostsTable::class)
            ->assertActionExists(TestAction::make('exists')->table()->bulk())
            ->assertActionDoesNotExist(TestAction::make('doesNotExist')->table()->bulk());

        livewire(PostsTable::class)
            ->assertTableBulkActionExists('exists')
            ->assertTableBulkActionDoesNotExist('doesNotExist');
    });

    it('can state whether bulk actions exist in order', function (): void {
        livewire(PostsTable::class)
            ->assertTableBulkActionsExistInOrder(['exists', 'existsInOrder']);
    });
});

describe('selection limits', function (): void {
    it('limits bulk selections to 10000 records by default', function (): void {
        foreach (array_chunk(array_fill(0, 10001, ['title' => 'Selectable post']), 1000) as $posts) {
            DB::table('posts')->insert($posts);
        }

        $expectedKeys = Post::query()->orderBy('id')->pluck('id')->take(10000)->all();

        livewire(SelectablePostsTable::class)
            ->set('isTrackingDeselectedTableRecords', true)
            ->callTableBulkAction('customBulk', [])
            ->assertDispatched('customBulk-called', records: $expectedKeys);
    });

    it('can override or disable the default `maxSelectableRecords()`', function (): void {
        $table = livewire(PostsTable::class)->instance()->getTable();

        expect($table->getMaxSelectableRecords())->toBe(10000)
            ->and($table->maxSelectableRecords(static fn (): int => 20000)->getMaxSelectableRecords())->toBe(20000)
            ->and($table->maxSelectableRecords(null)->getMaxSelectableRecords())->toBeNull();
    });

    it('enforces `maxSelectableRecords()` after applying scopes and reports the bounded total', function (string $mode, bool $tracksDeselectedRecords, ?int $scopeLimit): void {
        $posts = Post::factory()->count(8)->create();
        $expectedKeys = $posts->slice(1, min(3, $scopeLimit ?? 3))->values()->modelKeys();

        Table::configureUsing(
            static fn (Table $table): Table => $table
                ->maxSelectableRecords(3)
                ->modifyQueryUsing(static fn (Builder $query): Builder => $query->withGlobalScope(
                    'selection',
                    static fn (Builder $query): Builder => $query->whereKeyNot($posts->first()->getKey())->when(
                        $scopeLimit !== null,
                        static fn (Builder $query): Builder => $query->limit($scopeLimit),
                    ),
                )),
            during: static fn () => BulkAction::configureUsing(
                static fn (BulkAction $action): BulkAction => $action
                    ->fetchSelectedRecords($mode !== 'ids')
                    ->chunkSelectedRecords($mode === 'chunked' ? 2 : null)
                    ->action(function (Collection | LazyCollection $records, BulkAction $action) use ($mode, $expectedKeys): void {
                        expect($mode === 'ids' ? $records->all() : $records->pluck('id')->all())->toEqual($expectedKeys);
                        expect($action->getTotalSelectedRecordsCount())->toBe(count($expectedKeys));
                    }),
                during: function () use ($posts, $tracksDeselectedRecords): void {
                    $component = livewire(PostsTable::class);

                    if ($tracksDeselectedRecords) {
                        $component->set('isTrackingDeselectedTableRecords', true);
                    }

                    $component->callTableBulkAction('exists', $tracksDeselectedRecords ? [] : $posts);
                },
            ),
        );
    })->with(['eager', 'ids', 'chunked'])->with([false, true])->with([null, 2, 7]);

    it('preserves aggregate values and sorting in bounded bulk selections and query writes', function (string $mode, bool $deselectsWhenFiltered, bool $hidesAggregate, bool $sortsByAggregate): void {
        $users = User::factory()->count(4)->create();
        $publishedPostCounts = [0, 2, 1, 3];

        foreach ([1, 5, 2, 4] as $index => $postCount) {
            Post::factory()->count($publishedPostCounts[$index])->create(['author_id' => $users[$index]->getKey(), 'is_published' => true]);
            Post::factory()->count($postCount - $publishedPostCounts[$index])->create(['author_id' => $users[$index]->getKey(), 'is_published' => false]);
        }

        $expectedKeys = $sortsByAggregate ? [$users[1]->getKey(), $users[3]->getKey()] : [$users[0]->getKey(), $users[1]->getKey()];
        $expectedPostCounts = array_combine($expectedKeys, $sortsByAggregate ? [5, 4] : [1, 5]);
        $expectedPublishedPostCounts = array_combine($expectedKeys, $sortsByAggregate ? [2, 3] : [0, 2]);

        Table::configureUsing(
            static fn (Table $table): Table => $table->maxSelectableRecords(2)->defaultSort($sortsByAggregate ? 'posts_count' : 'users.id', $sortsByAggregate ? 'desc' : 'asc')->deselectAllRecordsWhenFiltered($deselectsWhenFiltered),
            during: static fn () => BulkAction::configureUsing(
                static fn (BulkAction $action): BulkAction => $action
                    ->fetchSelectedRecords($mode !== 'ids')
                    ->chunkSelectedRecords($mode === 'chunked' ? 1 : null)
                    ->action(function (Collection | LazyCollection $records, BulkAction $action) use ($mode, $expectedKeys, $expectedPostCounts, $expectedPublishedPostCounts): void {
                        expect($mode === 'ids' ? $records->all() : $records->pluck('id')->all())->toEqualCanonicalizing($expectedKeys);

                        if ($mode !== 'ids') {
                            expect($records->pluck('posts_count', 'id')->all())->toEqual($expectedPostCounts);
                            expect($records->pluck('published_posts_count', 'id')->all())->toEqual($expectedPublishedPostCounts);
                        }

                        expect($action->getTotalSelectedRecordsCount())->toBe(2);
                        expect($action->getSelectedRecordsQuery()->update(['name' => 'Selected']))->toBe(2);
                    }),
                during: static fn () => livewire(AggregateBulkSelectionTable::class, ['hidesAggregate' => $hidesAggregate])
                    ->set('isTrackingDeselectedTableRecords', true)
                    ->callTableBulkAction('inspect', []),
            ),
        );

        expect(User::query()->where('name', 'Selected')->pluck('id')->all())->toEqualCanonicalizing($expectedKeys);
    })->with(['eager', 'ids', 'chunked'])->with([false, true])->with([false, true])->with([false, true]);

    it('preserves aggregates in nested `getFilteredTableQuery()` calls and after sorting fails', function (): void {
        $user = User::factory()->create();
        Post::factory()->count(3)->create(['author_id' => $user->getKey()]);
        $component = livewire(AggregateBulkSelectionTable::class)->set('isTrackingDeselectedTableRecords', true)->instance();

        $component->getTable()->defaultSort(function (Builder $query) use ($component): Builder {
            expect($component->getFilteredTableQuery()->first()->posts_count)->toEqual(3);

            return (clone $query)->addSelect('users.*')->selectRaw('? as selection_marker', ['kept'])->orderBy('users.id');
        });

        $record = $component->getSelectedTableRecordsQuery()->first();
        expect($record->posts_count)->toEqual(3);
        expect($record->selection_marker)->toBe('kept');

        $component->getTable()->defaultSort(static function (): never {
            throw new LogicException('Expected sort failure');
        });

        expect(fn () => $component->getSelectedTableRecordsQuery())->toThrow(LogicException::class, 'Expected sort failure');
        $component->getTable()->defaultSort('users.id');
        expect($component->getFilteredTableQuery()->first()->posts_count)->toEqual(3);
    });

    it('preserves descending bounded selection membership and totals when chunking', function (bool $usesScopeOrder, int $offset): void {
        $posts = Post::factory()->count(8)->create();
        $minimumKey = $posts[5]->getKey();
        $expectedKeys = $posts->reverse()->slice($offset, 3)->filter(static fn (Post $post): bool => $post->getKey() >= $minimumKey)->values()->modelKeys();

        Table::configureUsing(
            static fn (Table $table): Table => $table
                ->maxSelectableRecords(3)
                ->defaultSort('id', 'desc')
                ->modifyQueryUsing(static fn (Builder $query): Builder => $query->withGlobalScope(
                    'selection',
                    static fn (Builder $query): Builder => $query->limit(7)->offset($offset)->when($usesScopeOrder, static fn (Builder $query): Builder => $query->reorder('id', 'desc')),
                ))
                ->checkIfRecordIsSelectableUsing(static fn (Post $record): bool => $record->getKey() >= $minimumKey),
            during: static fn () => BulkAction::configureUsing(
                static fn (BulkAction $action): BulkAction => $action->chunkSelectedRecords(2)
                    ->action(function (LazyCollection $records, BulkAction $action) use ($expectedKeys): void {
                        expect($records->pluck('id')->all())->toEqualCanonicalizing($expectedKeys);
                        expect($action->getTotalSelectedRecordsCount())->toBe(count($expectedKeys));
                    }),
                during: static fn () => livewire(PostsTable::class)
                    ->set('isTrackingDeselectedTableRecords', true)
                    ->callTableBulkAction('exists', []),
            ),
        );
    })->with([false, true])->with([0, 1]);

    it('bounds joined bulk writes by selected keys while preserving relationship constraints', function (string $operation, bool $duplicates, bool $fetchesRecords, ?int $scopeLimit): void {
        $ticket = Ticket::factory()->create();
        $otherTicket = Ticket::factory()->create();
        $departments = Department::factory()->count(8)->create(['name' => 'Original']);
        $ticket->departments()->attach($departments->modelKeys());
        $otherTicket->departments()->attach(Department::factory()->create(['name' => 'Other']));
        $ticket->departments()->attach(Department::factory()->create(['name' => 'Trashed', 'deleted_at' => now()]));
        $expectedKeys = $departments->slice(1, 3)->values()->modelKeys();

        Table::configureUsing(
            static fn (Table $table): Table => $table
                ->maxSelectableRecords(3)
                ->defaultSort('departments.id')
                ->modifyQueryUsing(static fn (Builder $query): Builder => $query->withGlobalScope(
                    'selection',
                    static fn (Builder $query): Builder => $query->where('departments.id', '!=', $departments->first()->getKey())->when(
                        $scopeLimit !== null,
                        static fn (Builder $query): Builder => $query->limit($scopeLimit),
                    ),
                )),
            during: static fn () => BulkAction::configureUsing(
                static fn (BulkAction $action): BulkAction => $action
                    ->fetchSelectedRecords($fetchesRecords)
                    ->after(function (BulkAction $action) use ($operation): void {
                        $query = $action->getSelectedRecordsQuery();
                        $affected = $operation === 'update'
                            ? $query->update(['name' => 'Updated'])
                            : $query->toBase()->delete();

                        expect($affected)->toBe(3);
                    }),
                during: static fn () => livewire(TicketDepartmentsReorderableTable::class, ['ticketKey' => $ticket->getKey(), 'duplicates' => $duplicates])
                    ->set('isTrackingDeselectedTableRecords', true)
                    ->callTableBulkAction('customBulk', []),
            ),
        );

        if ($operation === 'update') {
            expect(Department::query()->where('name', 'Updated')->orderBy('id')->pluck('id')->all())->toBe($expectedKeys);
        } else {
            expect(Department::query()->whereKey($expectedKeys)->count())->toBe(0);
        }

        expect(Department::query()->where('name', 'Original')->orderBy('id')->pluck('id')->all())
            ->toBe(array_values(array_diff($departments->modelKeys(), $expectedKeys)));
        expect(Department::query()->where('name', 'Other')->count())->toBe(1);
        expect(Department::onlyTrashed()->where('name', 'Trashed')->count())->toBe(1);
    })->with(['update', 'delete'])->with([false, true])->with([false, true])->with([null, 7]);

    it('processes bounded string-key selections beyond SQL Server parameter limits', function (bool $pivotKeys, bool $checksSelectability, int $limit): void {
        if (DB::connection()->getDriverName() !== 'sqlsrv') {
            $this->markTestSkipped('SQL Server parameter limit regression.');
        }

        $keys = ['000-leading-zero', "漢字Ω'O'Reilly", "key') OR 1=1 --", ...array_map(static fn (int $index): string => sprintf('00000000-0000-4000-8000-%012d', $index), range(1, $limit - 1))];

        foreach (array_chunk($keys, 200) as $batch) {
            DB::table('departments')->insert(array_map(static fn (string $key): array => ['name' => $key], $batch));
        }

        $expectedKeys = array_values(array_filter(array_slice($keys, 0, $limit), static fn (string $key): bool => (! $checksSelectability) || ($key !== $keys[3])));
        $relationship = null;
        $ticket = null;

        if ($pivotKeys) {
            Schema::create('bulk_selection_links', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->unsignedBigInteger('ticket_id');
                $table->unsignedBigInteger('department_id');
                $table->unsignedInteger('sort');
            });

            $ticket = Ticket::factory()->create();
            $departments = DB::table('departments')->orderBy('id')->get();

            foreach ($departments->chunk(200) as $batch) {
                DB::table('bulk_selection_links')->insert($batch->map(static fn (object $department): array => [
                    'id' => $department->name,
                    'ticket_id' => $ticket->getKey(),
                    'department_id' => $department->id,
                    'sort' => $department->id,
                ])->all());
            }

            $relationship = $ticket->belongsToMany(Department::class, 'bulk_selection_links', 'ticket_id', 'department_id')
                ->using(StringKeyBulkSelectionPivot::class)
                ->withPivot(['id', 'sort']);
        }

        Table::configureUsing(
            static fn (Table $table): Table => $table
                ->maxSelectableRecords($limit)
                ->relationship($relationship ? static fn () => $relationship : null)
                ->defaultSort($pivotKeys ? 'bulk_selection_links.sort' : 'departments.id')
                ->checkIfRecordIsSelectableUsing($checksSelectability ? static fn (Department $record): bool => $record->name !== $keys[3] : null),
            during: function () use ($pivotKeys, $ticket, $keys, $expectedKeys): void {
                foreach ([false, true] as $fetchesRecords) {
                    $component = livewire(
                        $pivotKeys ? TicketDepartmentsReorderableTable::class : StringKeyBulkSelectionTable::class,
                        $pivotKeys ? ['ticketKey' => $ticket->getKey(), 'duplicates' => true, 'isPaginated' => true] : [],
                    )->set('isTrackingDeselectedTableRecords', true)->instance();

                    expect($component->getSelectedTableRecords($fetchesRecords, $fetchesRecords ? 1000 : null)->count())->toBe(count($expectedKeys));
                    expect($component->getSelectedTableRecordsQuery(false)->update(['updated_at' => '2020-01-01 00:00:00']))->toBe(count($expectedKeys));
                    expect(DB::table('departments')->where('updated_at', '2020-01-01 00:00:00')->orderBy('id')->pluck('name')->all())->toBe($expectedKeys);
                }

                expect($component->getSelectedTableRecordsQuery(false)->toBase()->delete())->toBe(count($expectedKeys));
                expect(Department::query()->orderBy('id')->pluck('name')->all())->toBe(array_values(array_diff($keys, $expectedKeys)));
            },
        );
    })->with([false, true])->with([false, true])->with([2101, 10000]);

    it('respects `maxSelectableRecords()` when chunking selected records', function (bool $tracksDeselectedRecords, bool $checksSelectability): void {
        $posts = Post::factory()->count(7)->create();
        $nonSelectableKey = $posts[2]->getKey();
        $expectedDeletedKeys = $checksSelectability ? [$posts[1]->getKey(), $posts[3]->getKey()] : [$posts[1]->getKey(), $posts[2]->getKey(), $posts[3]->getKey()];

        Table::configureUsing(
            static fn (Table $table): Table => $table
                ->maxSelectableRecords(3)
                ->checkIfRecordIsSelectableUsing($checksSelectability ? static fn (Post $record): bool => $record->getKey() !== $nonSelectableKey : null),
            during: static fn () => DeleteBulkAction::configureUsing(
                static fn (DeleteBulkAction $action): DeleteBulkAction => $action
                    ->chunkSelectedRecords(2)
                    ->after(static fn (DeleteBulkAction $action) => expect($action->getTotalSelectedRecordsCount())->toBe(count($expectedDeletedKeys))),
                during: function () use ($posts, $tracksDeselectedRecords): void {
                    $component = livewire(PostsTable::class);

                    if ($tracksDeselectedRecords) {
                        $component
                            ->set('isTrackingDeselectedTableRecords', true)
                            ->set('deselectedTableRecords', [$posts->first()->getKey()]);
                    }

                    $component->callTableBulkAction(DeleteBulkAction::class, $tracksDeselectedRecords ? [] : $posts->slice(1));
                },
            ),
        );

        expect(Post::onlyTrashed()->orderBy('id')->pluck('id')->all())->toBe($expectedDeletedKeys);
        expect(Post::query()->orderBy('id')->pluck('id')->all())->toBe(array_values(array_diff($posts->modelKeys(), $expectedDeletedKeys)));
    })->with([false, true])->with([false, true]);
});

describe('duplicate relationship selection', function (): void {
    it('only deletes search matches when selecting all relationship records with `allowDuplicates()`', function (bool $duplicates, string $action): void {
        $ticket = Ticket::factory()->create();
        $matchingDepartments = Department::factory()->count(3)->create(['name' => 'Sales']);
        $otherDepartments = Department::factory()->count(2)->create(['name' => 'Engineering']);
        $ticket->departments()->attach($matchingDepartments->merge($otherDepartments)->modelKeys());

        livewire(TicketDepartmentsReorderableTable::class, ['ticketKey' => $ticket->getKey(), 'duplicates' => $duplicates])
            ->searchTable('Sales')
            ->set('isTrackingDeselectedTableRecords', true)
            ->callTableBulkAction($action, []);

        foreach ($matchingDepartments as $department) {
            assertSoftDeleted($department);
        }

        expect(Department::query()->whereIn('id', $otherDepartments->modelKeys())->count())->toBe(2);
    })->with([false, true])->with(['delete', 'queryBulkDelete']);

    it('respects search filters scopes and exclusions when selecting all with `allowDuplicates()`', function (string $constraint, string $action): void {
        $ticket = Ticket::factory()->create();
        $sales = Department::factory()->create(['name' => 'Sales']);
        $engineering = Department::factory()->create(['name' => 'Engineering']);
        foreach ([1, 5, 7, 9] as $quantity) {
            $ticket->departments()->attach($sales, ['quantity' => $quantity]);
        }
        $ticket->departments()->attach($engineering, ['quantity' => 1]);
        $ticket->departments()->attach(Department::factory()->create(['name' => 'Sales', 'deleted_at' => now()]), ['quantity' => 11]);
        $excludedKeys = DB::table('department_ticket')->whereIn('quantity', [1, 7])->where('department_id', $sales->getKey())->pluck('id')->all();
        $expectedQuantities = match ($constraint) {
            'max', 'scopeLimit', 'globalScopeLimit', 'largerGlobalScopeLimit' => [9],
            'scopeOffset', 'globalScopeOffset' => [5],
            default => [5, 9],
        };

        Table::configureUsing(
            static function (Table $table) use ($constraint): void {
                if ($constraint === 'filter') {
                    $table->filters([
                        Filter::make('quantity')->query(static fn (Builder $query): Builder => $query->where('department_ticket.quantity', '>', 1)),
                    ])->deferFilters(false);
                } elseif ($constraint === 'scope') {
                    $table->modifyQueryUsing(static fn (Builder $query): Builder => (clone $query)->where('department_ticket.quantity', '>', 1));
                } elseif (in_array($constraint, ['max', 'scopeLimit', 'scopeOffset', 'globalScopeLimit', 'globalScopeOffset', 'largerGlobalScopeLimit'])) {
                    $table
                        ->maxSelectableRecords(3)
                        ->defaultSort('department_ticket.quantity', 'desc')
                        ->checkIfRecordIsSelectableUsing(static fn (): bool => true)
                        ->modifyQueryUsing(static fn (Builder $query): Builder => $query->where('department_ticket.quantity', '>', 1));

                    if ($constraint === 'max') {
                        $table->maxSelectableRecords(1);
                    } elseif ($constraint === 'largerGlobalScopeLimit') {
                        $table->maxSelectableRecords(1)
                            ->modifyQueryUsing(static fn (Builder $query): Builder => $query->withGlobalScope(
                                'selectionLimit',
                                static fn (Builder $query): Builder => $query->limit(7),
                            ));
                    } elseif ($constraint === 'scopeLimit') {
                        $table->modifyQueryUsing(static fn (Builder $query): Builder => $query->limit(1));
                    } elseif ($constraint === 'globalScopeLimit') {
                        $table->modifyQueryUsing(static fn (Builder $query): Builder => $query->withGlobalScope(
                            'selectionLimit',
                            static fn (Builder $query): Builder => $query->limit(1),
                        ));
                    } elseif ($constraint === 'globalScopeOffset') {
                        $table->modifyQueryUsing(static fn (Builder $query): Builder => $query->withGlobalScope(
                            'selectionOffset',
                            static fn (Builder $query): Builder => $query->limit(1)->offset(1),
                        ));
                    } else {
                        $table->modifyQueryUsing(static fn (Builder $query): Builder => $query->limit(1)->offset(1));
                    }
                }
            },
            during: function () use ($ticket, $constraint, $action, $excludedKeys, $expectedQuantities): void {
                $component = livewire(TicketDepartmentsReorderableTable::class, ['ticketKey' => $ticket->getKey(), 'duplicates' => true]);

                if ($constraint === 'search') {
                    $component->searchTable('Sales');
                } elseif ($constraint === 'columnSearch') {
                    $component->searchTableColumns(['name' => 'Sales']);
                } elseif ($constraint === 'filter') {
                    $component->filterTable('quantity');
                }

                BulkAction::configureUsing(
                    static fn (BulkAction $bulkAction) => $bulkAction->chunkSelectedRecords($action === 'chunked' ? 1 : null),
                    during: static fn () => $component
                        ->set('isTrackingDeselectedTableRecords', true)
                        ->set('deselectedTableRecords', $excludedKeys)
                        ->callTableBulkAction($action === 'chunked' ? 'customBulk' : $action, [])
                        ->assertDispatched('customBulk-called', quantities: $expectedQuantities),
                );
            },
        );
    })->with(['search', 'columnSearch', 'filter', 'scope', 'max', 'scopeLimit', 'scopeOffset', 'globalScopeLimit', 'globalScopeOffset', 'largerGlobalScopeLimit'])->with(['customBulk', 'queryBulk', 'chunked']);

    it('checks selectability per pivot row when selecting all with `allowDuplicates()`', function (string $action, bool $crossesBatchBoundary): void {
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->create();
        $quantities = $crossesBatchBoundary ? [3, ...array_fill(0, 998, 5), 7, 5, 11] : [3, 5, 7];

        foreach ($quantities as $quantity) {
            $ticket->departments()->attach($department, ['quantity' => $quantity]);
        }

        Table::configureUsing(
            static fn (Table $table): Table => $table->checkIfRecordIsSelectableUsing(static fn (Department $record): bool => $record->quantity !== 5),
            during: static fn () => BulkAction::configureUsing(
                static fn (BulkAction $bulkAction) => $bulkAction->chunkSelectedRecords($action === 'chunked' ? 1 : null),
                during: static fn () => livewire(TicketDepartmentsReorderableTable::class, ['ticketKey' => $ticket->getKey(), 'duplicates' => true])
                    ->set('isTrackingDeselectedTableRecords', true)
                    ->callTableBulkAction($action === 'chunked' ? 'customBulk' : $action, [])
                    ->assertDispatched('customBulk-called', quantities: $crossesBatchBoundary ? [3, 7, 11] : [3, 7]),
            ),
        );
    })->with([
        'fetched' => ['customBulk', false],
        'query' => ['queryBulk', false],
        'chunked' => ['chunked', false],
        'query across batch boundary' => ['queryBulk', true],
    ]);

    it('uses pivot keys for explicit selection with `allowDuplicates()`', function (string $selection, bool $checksSelectability, string $action): void {
        $ticket = Ticket::factory()->create();
        $otherTicket = Ticket::factory()->create();
        $sales = Department::factory()->create(['name' => 'Sales']);
        $engineering = Department::factory()->create(['name' => 'Engineering']);
        $otherTicket->departments()->attach($sales);
        $otherTicket->departments()->attach($sales);
        $ticket->departments()->attach($sales, ['quantity' => 3]);
        $ticket->departments()->attach($sales, ['quantity' => 7]);
        $ticket->departments()->attach($engineering, ['quantity' => 9]);
        $selectsCurrentPageOnly = in_array($selection, ['page', 'groupPage']);

        Table::configureUsing(
            static fn (Table $table): Table => $table
                ->deselectAllRecordsWhenFiltered(false)
                ->selectCurrentPageOnly($selectsCurrentPageOnly)
                ->paginationPageOptions($selectsCurrentPageOnly ? [1] : null)
                ->defaultSort('department_ticket.id')
                ->groups([Group::make('name')])
                ->modifyQueryUsing(static fn (Builder $query): Builder => $query->withGlobalScope(
                    'projection',
                    static fn (Builder $query): Builder => $query->select('departments.*')->withCount('tickets'),
                ))
                ->checkIfRecordIsSelectableUsing($checksSelectability ? static fn (Department $record): bool => $record->quantity !== 7 : null),
            during: function () use ($ticket, $selection, $selectsCurrentPageOnly, $action, $checksSelectability): void {
                $component = livewire(TicketDepartmentsReorderableTable::class, [
                    'ticketKey' => $ticket->getKey(),
                    'duplicates' => true,
                    'isPaginated' => $selectsCurrentPageOnly,
                ])->searchTable('Sales');

                if (in_array($selection, ['group', 'groupPage'])) {
                    $component->set('tableGrouping', 'name:asc');
                }

                $keys = in_array($selection, ['group', 'groupPage']) ?
                    $component->instance()->getGroupedSelectableTableRecordKeys('Sales') :
                    $component->instance()->getAllSelectableTableRecordKeys();

                BulkAction::configureUsing(
                    static fn (BulkAction $bulkAction) => $bulkAction->chunkSelectedRecords($action === 'chunked' ? 1 : null),
                    during: static fn () => $component->searchTable('Engineering')
                        ->callTableBulkAction($action === 'chunked' ? 'customBulk' : $action, $keys)
                        ->assertDispatched('customBulk-called', quantities: ($selectsCurrentPageOnly || $checksSelectability) ? [3] : [3, 7]),
                );
            },
        );
    })->with(['all', 'page', 'group', 'groupPage'])->with([false, true])->with(['customBulk', 'queryBulk', 'chunked']);
});

describe('selectability and individual authorization', function (): void {
    it('does not receive non-selectable records when using select all', function (): void {
        // 2 published (selectable) and 1 unpublished (non-selectable)
        $publishedPosts = Post::factory()->count(2)->create(['is_published' => true]);
        Post::factory()->create(['is_published' => false]);

        livewire(SelectablePostsTable::class)
            ->set('isTrackingDeselectedTableRecords', true)
            ->set('deselectedTableRecords', [])
            ->callTableBulkAction('customBulk', [])
            ->assertDispatched('customBulk-called', records: $publishedPosts->pluck('id')->toArray());
    });

    it('does not delete non-selectable records when using select all with a query-based action', function (): void {
        // 2 published (selectable) and 1 unpublished (non-selectable)
        $publishedPosts = Post::factory()->count(2)->create(['is_published' => true]);
        $unpublishedPost = Post::factory()->create(['is_published' => false]);

        livewire(SelectablePostsTable::class)
            ->set('isTrackingDeselectedTableRecords', true)
            ->set('deselectedTableRecords', [])
            ->callTableBulkAction('queryBulkDelete', []);

        foreach ($publishedPosts as $post) {
            assertSoftDeleted($post);
        }

        $this->assertDatabaseHas('posts', [
            'id' => $unpublishedPost->id,
            'deleted_at' => null,
        ]);
    });

    it('deletes every record when all are selectable using a query-based action', function (): void {
        $publishedPosts = Post::factory()->count(3)->create(['is_published' => true]);

        livewire(SelectablePostsTable::class)
            ->set('isTrackingDeselectedTableRecords', true)
            ->set('deselectedTableRecords', [])
            ->callTableBulkAction('queryBulkDelete', []);

        foreach ($publishedPosts as $post) {
            assertSoftDeleted($post);
        }
    });

    it('does not receive non-selectable records when selecting specific records', function (): void {
        // 2 published (selectable) and 1 unpublished (non-selectable)
        $publishedPosts = Post::factory()->count(2)->create(['is_published' => true]);
        $unpublishedPost = Post::factory()->create(['is_published' => false]);

        livewire(SelectablePostsTable::class)
            ->callTableBulkAction('customBulk', [$publishedPosts->first(), $publishedPosts->last(), $unpublishedPost])
            ->assertDispatched('customBulk-called', records: $publishedPosts->pluck('id')->toArray());
    });

    it('respects both deselection and selectability when using select all', function (): void {
        // 2 published (selectable) and 1 unpublished (non-selectable)
        $publishedPosts = Post::factory()->count(2)->create(['is_published' => true]);
        Post::factory()->create(['is_published' => false]);

        livewire(SelectablePostsTable::class)
            ->set('isTrackingDeselectedTableRecords', true)
            ->set('deselectedTableRecords', [$publishedPosts->first()->getKey()])
            ->callTableBulkAction('customBulk', [])
            ->assertDispatched('customBulk-called', records: [$publishedPosts->last()->id]);
    });

    it('does not delete non-selectable records when selecting specific records with a query-based action', function (): void {
        // 2 published (selectable) and 1 unpublished (non-selectable)
        $publishedPosts = Post::factory()->count(2)->create(['is_published' => true]);
        $unpublishedPost = Post::factory()->create(['is_published' => false]);

        livewire(SelectablePostsTable::class)
            ->callTableBulkAction('queryBulkDelete', [$publishedPosts->first(), $publishedPosts->last(), $unpublishedPost]);

        foreach ($publishedPosts as $post) {
            assertSoftDeleted($post);
        }

        $this->assertDatabaseHas('posts', [
            'id' => $unpublishedPost->id,
            'deleted_at' => null,
        ]);
    });

    it('returns `false` from `checksIfRecordIsSelectable()` when no selectability closure is set', function (): void {
        $table = livewire(PostsTable::class)->instance()->getTable();

        expect($table->checksIfRecordIsSelectable())->toBeFalse();
    });

    it('returns `true` from `isRecordSelectable()` for any record when no selectability closure is set', function (): void {
        $post = Post::factory()->create(['is_published' => false]);

        $table = livewire(PostsTable::class)->instance()->getTable();

        expect($table->isRecordSelectable($post))->toBeTrue();
    });

    it('only passes records that pass `authorizeIndividualRecords()` to the action body for a mixed selection', function (): void {
        $posts = Post::factory()->count(4)->create();

        $authorizedPosts = $posts->take(2);

        PostsTable::$authorizedRecordKeys = $authorizedPosts->pluck('id')->all();

        livewire(PostsTable::class)
            ->callTableBulkAction('individuallyAuthorized', $posts)
            ->assertDispatched('individually-authorized-processed', keys: $authorizedPosts->pluck('id')->toArray())
            ->assertNotDispatched('individually-authorized-processed', keys: $posts->pluck('id')->toArray());

        PostsTable::$authorizedRecordKeys = [];
    });

    it('passes no records to the action body when every record fails `authorizeIndividualRecords()`', function (): void {
        $posts = Post::factory()->count(3)->create();

        PostsTable::$authorizedRecordKeys = [];

        livewire(PostsTable::class)
            ->callTableBulkAction('individuallyAuthorized', $posts)
            ->assertDispatched('individually-authorized-processed', keys: []);
    });

    it('passes every record to the action body when all records pass `authorizeIndividualRecords()`', function (): void {
        $posts = Post::factory()->count(3)->create();

        PostsTable::$authorizedRecordKeys = $posts->pluck('id')->all();

        livewire(PostsTable::class)
            ->callTableBulkAction('individuallyAuthorized', $posts)
            ->assertDispatched('individually-authorized-processed', keys: $posts->pluck('id')->toArray());

        PostsTable::$authorizedRecordKeys = [];
    });

    it('returns `true` from `checksIfRecordIsSelectable()` when a selectability closure is set', function (): void {
        $table = livewire(SelectablePostsTable::class)->instance()->getTable();

        expect($table->checksIfRecordIsSelectable())->toBeTrue();
    });

    it('evaluates the selectability closure per record in `isRecordSelectable()`', function (): void {
        $publishedPost = Post::factory()->create(['is_published' => true]);
        $unpublishedPost = Post::factory()->create(['is_published' => false]);

        $table = livewire(SelectablePostsTable::class)->instance()->getTable();

        expect($table->isRecordSelectable($publishedPost))->toBeTrue()
            ->and($table->isRecordSelectable($unpublishedPost))->toBeFalse();
    });

    it('can still track deselected records when a selectability closure is set', function (): void {
        $table = livewire(SelectablePostsTable::class)->instance()->getTable();

        expect($table->canTrackDeselectedRecords())->toBeTrue();
    });

    it('only includes selectable records in `getAllSelectableTableRecordKeys()`', function (): void {
        // 2 published (selectable) and 1 unpublished (non-selectable)
        $publishedPosts = Post::factory()->count(2)->create(['is_published' => true]);
        Post::factory()->create(['is_published' => false]);

        $keys = livewire(SelectablePostsTable::class)->instance()->getAllSelectableTableRecordKeys();

        expect($keys)->toEqualCanonicalizing(
            $publishedPosts->pluck('id')->map(fn (int $id): string => (string) $id)->all(),
        );
    });

    it('includes every record in `getAllSelectableTableRecordKeys()` when no selectability closure is set', function (): void {
        $posts = Post::factory()->count(3)->create(['is_published' => false]);

        $keys = livewire(PostsTable::class)->instance()->getAllSelectableTableRecordKeys();

        expect($keys)->toEqualCanonicalizing(
            $posts->pluck('id')->map(fn (int $id): string => (string) $id)->all(),
        );
    });

    it('only counts selectable records in `getAllSelectableTableRecordsCount()`', function (): void {
        // 2 published (selectable) and 1 unpublished (non-selectable)
        Post::factory()->count(2)->create(['is_published' => true]);
        Post::factory()->create(['is_published' => false]);

        expect(livewire(SelectablePostsTable::class)->instance()->getAllSelectableTableRecordsCount())->toBe(2);
    });

    it('counts every record in `getAllSelectableTableRecordsCount()` when no selectability closure is set', function (): void {
        Post::factory()->count(3)->create();

        expect(livewire(PostsTable::class)->instance()->getAllSelectableTableRecordsCount())->toBe(3);
    });
});

class StringKeyBulkSelectionDepartment extends Department
{
    protected $table = 'departments';

    protected $primaryKey = 'name';

    protected $keyType = 'string';

    public $incrementing = false;
}

class StringKeyBulkSelectionPivot extends Pivot
{
    protected $keyType = 'string';
}

class StringKeyBulkSelectionTable extends PostsTable
{
    public function table(Table $table): Table
    {
        return $table
            ->query(StringKeyBulkSelectionDepartment::query())
            ->columns([TextColumn::make('name')])
            ->selectable();
    }
}

class AggregateBulkSelectionTable extends PostsTable
{
    public bool $hidesAggregate = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('posts_count')
                    ->counts(['posts', 'posts as published_posts_count' => static fn (Builder $query): Builder => $query->where('is_published', true)])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: $this->hidesAggregate),
            ])
            ->toolbarActions([BulkAction::make('inspect')]);
    }
}
