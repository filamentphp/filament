<?php

use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\PostsReorderableTable;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\TicketDepartmentsReorderableTable;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can list records', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts);
});

it('resolves and counts eligible attachment keys with `allowDuplicates()`', function (bool $checksSelectability): void {
    $ticket = Ticket::factory()->create();
    $otherTicket = Ticket::factory()->create();
    $sales = Department::factory()->create(['name' => 'Sales']);
    $engineering = Department::factory()->create(['name' => 'Engineering']);
    $otherTicket->departments()->attach($sales);
    $otherTicket->departments()->attach($sales);
    $ticket->departments()->attach($sales, ['quantity' => 3]);
    $ticket->departments()->attach($sales, ['quantity' => 7]);
    $ticket->departments()->attach($engineering, ['quantity' => 9]);
    $expectedQuantities = $checksSelectability ? [3] : [3, 7];
    $expectedKeys = DB::table('department_ticket')->where('ticket_id', $ticket->getKey())->whereIn('quantity', $expectedQuantities)->orderBy('id')->pluck('id')->map(strval(...))->all();

    Table::configureUsing(
        static fn (Table $table): Table => $table
            ->modifyQueryUsing(static fn (Builder $query): Builder => $query->withGlobalScope(
                'projection',
                static fn (Builder $query): Builder => $query->select('departments.*')->withCount('tickets'),
            ))
            ->checkIfRecordIsSelectableUsing($checksSelectability ? static fn (Department $record): bool => $record->quantity !== 7 : null),
        during: function () use ($ticket, $expectedKeys, $expectedQuantities): void {
            $component = livewire(TicketDepartmentsReorderableTable::class, ['ticketKey' => $ticket->getKey(), 'duplicates' => true])->searchTable('Sales');

            expect($component->instance()->getAllSelectableTableRecordKeys())->toEqualCanonicalizing($expectedKeys);
            expect($component->instance()->getAllSelectableTableRecordsCount())->toBe(count($expectedQuantities));

            foreach ($expectedKeys as $index => $key) {
                $record = $component->instance()->getTableRecord($key);

                expect($record->quantity)->toBe($expectedQuantities[$index]);
                expect($component->instance()->getTableRecordKey($record))->toBe($key);
            }
        },
    );
})->with([false, true]);

it('preserves related model key casts in selected records with `allowDuplicates()`', function (bool $shouldFetchRecords, ?int $chunkSize): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create();
    $ticket->departments()->attach($department, ['quantity' => 3]);
    $pivotKey = DB::table('department_ticket')->value('id');
    $relationship = $ticket->departments()->withPivot('id')->using(get_class(new class extends Pivot
    {
        public $incrementing = true;

        protected $casts = ['quantity' => 'string'];
    }));
    $relationship->getRelated()->mergeCasts(['id' => 'string']);

    Table::configureUsing(
        static fn (Table $table): Table => $table->relationship(static fn () => $relationship),
        during: function () use ($ticket, $department, $pivotKey, $shouldFetchRecords, $chunkSize): void {
            $component = livewire(TicketDepartmentsReorderableTable::class, ['ticketKey' => $ticket->getKey(), 'duplicates' => true])
                ->set('selectedTableRecords', [(string) $pivotKey]);
            $records = $component->instance()->getSelectedTableRecords($shouldFetchRecords, $chunkSize);

            if (! $shouldFetchRecords) {
                expect($records->all())->toBe([$department->getKey()]);

                return;
            }

            $record = $records->first();
            expect($record->quantity)->toBe('3');
            expect($record->getKey())->toBe((string) $department->getKey());
            expect($component->instance()->getTableRecordKey($record))->toBe((string) $pivotKey);
        },
    );
})->with([[true, null], [true, 1], [false, null]]);

it('can set extra record link attributes', function (): void {
    $table = livewire(PostsTable::class)->instance()->getTable();
    $table->extraRecordLinkAttributes(['data-test' => 'value']);

    $post = Post::factory()->create();
    $attributes = $table->getExtraRecordLinkAttributeBag($post);

    expect($attributes->get('data-test'))->toBe('value');
});

it('can set dynamic extra record link attributes', function (): void {
    $table = livewire(PostsTable::class)->instance()->getTable();
    $table->extraRecordLinkAttributes(fn ($record) => [
        'data-id' => (string) $record->getKey(),
    ]);

    $post = Post::factory()->create();
    $attributes = $table->getExtraRecordLinkAttributeBag($post);

    expect($attributes->get('data-id'))->toBe((string) $post->getKey());
});

it('can merge extra record link attributes', function (): void {
    $table = livewire(PostsTable::class)->instance()->getTable();
    $table->extraRecordLinkAttributes(['data-test' => 'value1']);
    $table->extraRecordLinkAttributes(['data-test2' => 'value2'], merge: true);

    $post = Post::factory()->create();
    $attributes = $table->getExtraRecordLinkAttributeBag($post);

    expect($attributes->get('data-test'))->toBe('value1')
        ->and($attributes->get('data-test2'))->toBe('value2');
});

/**
 * @param  Collection<int, Department>  $departments
 * @param  array<string, mixed>  $extraPivotData
 */
function attachDepartmentsWithSort(Ticket $ticket, Collection $departments, int $sortOffset = 1, array $extraPivotData = []): void
{
    foreach ($departments->values() as $index => $department) {
        $ticket->departments()->attach($department->getKey(), [
            'sort' => $sortOffset + $index,
            ...$extraPivotData,
        ]);
    }
}

/**
 * @return array<int, int>
 */
function pivotSortKeyedByDepartment(Ticket $ticket): array
{
    return DB::table('department_ticket')
        ->where('ticket_id', $ticket->getKey())
        ->pluck('sort', 'department_id')
        ->all();
}

function postSort(int | string $postKey): ?int
{
    $sort = DB::table('posts')->where('id', $postKey)->value('sort');

    return is_null($sort) ? null : (int) $sort;
}

it('activates screen-reader table moves without adding Tab stops or moving focus in both themes', function (bool $grid): void {
    Artisan::call('filament:assets');
    $posts = Post::factory()->count(3)->sequence(
        ['title' => 'Alpha', 'sort' => 1],
        ['title' => 'Beta', 'sort' => 2],
        ['title' => 'Gamma', 'sort' => 3],
    )->create();
    $this->actingAs(User::factory()->create());
    $page = visit('/table-reordering-browser-test?grid=' . (int) $grid);
    $firstDown = '[x-sortable-item="' . $posts[0]->getKey() . '"] [data-table-reorder-direction="down"]';
    $firstUp = '[x-sortable-item="' . $posts[0]->getKey() . '"] [data-table-reorder-direction="up"]';
    $search = '[data-testid="reordering-table"] input[type="search"]';
    $readOrder = 'Array.from(document.querySelectorAll("[x-sortable-item]")).map(element => element.getAttribute("x-sortable-item")).join(",")';
    $movesAreReady = 'Array.from(document.querySelectorAll("[data-table-reorder-direction]")).every(element => element.disabled || element.getAttribute("aria-disabled") !== "true")';

    foreach ([$page, $page->inDarkMode()] as $page) {
        $page->click('[data-testid="toggle-reordering"]')
            ->assertDisabled($firstUp)
            ->assertDisabled('[x-sortable-item="' . $posts[2]->getKey() . '"] [data-table-reorder-direction="down"]')
            ->assertScript('Array.from(document.querySelectorAll("[data-table-reorder-direction], .fi-ta-reorder-handle")).every(element => element.tabIndex === -1)', true);

        $page->drag('[x-sortable-item="' . $posts[0]->getKey() . '"] .fi-ta-reorder-handle', '[x-sortable-item="' . $posts[2]->getKey() . '"] .fi-ta-reorder-handle')
            ->assertScript($readOrder, implode(',', [$posts[1]->getKey(), $posts[2]->getKey(), $posts[0]->getKey()]))
            ->assertScript($movesAreReady, true);
        expect(Post::query()->orderBy('sort')->pluck('id')->all())->toBe([$posts[1]->getKey(), $posts[2]->getKey(), $posts[0]->getKey()]);
        $page->script('document.querySelector(' . json_encode($firstUp) . ').click()');
        $page->assertScript($readOrder, implode(',', [$posts[1]->getKey(), $posts[0]->getKey(), $posts[2]->getKey()]))
            ->assertScript($movesAreReady, true);
        $page->script('document.querySelector(' . json_encode($firstUp) . ').click()');
        $page->assertScript($readOrder, $posts->pluck('id')->implode(','))->assertScript($movesAreReady, true)->click($search);

        $page->script('document.querySelector(' . json_encode($firstDown) . ').click()');
        $page
            ->assertScript($readOrder, implode(',', [$posts[1]->getKey(), $posts[0]->getKey(), $posts[2]->getKey()]))
            ->assertScript($movesAreReady, true)
            ->assertPresent($search . ':focus');

        $page->script('document.querySelector(' . json_encode($firstDown) . ').click()');
        $page
            ->assertScript($readOrder, implode(',', [$posts[1]->getKey(), $posts[2]->getKey(), $posts[0]->getKey()]))
            ->assertScript($movesAreReady, true)
            ->assertDisabled($firstDown)
            ->assertPresent($search . ':focus')
            ->assertSeeIn('[x-ref="tableReorderStatus"]', 'Alpha')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        expect(Post::query()->orderBy('sort')->pluck('id')->all())->toBe([$posts[1]->getKey(), $posts[2]->getKey(), $posts[0]->getKey()]);

        $page->script('document.querySelector(' . json_encode($firstUp) . ').click()');
        $page
            ->assertScript($readOrder, implode(',', [$posts[1]->getKey(), $posts[0]->getKey(), $posts[2]->getKey()]))
            ->assertScript($movesAreReady, true)
            ->assertPresent($search . ':focus');

        $page->script('document.querySelector(' . json_encode($firstUp) . ').click()');
        $page
            ->assertScript($readOrder, $posts->pluck('id')->implode(','))
            ->assertScript($movesAreReady, true)
            ->assertPresent($search . ':focus');

        $page->script('const control = document.querySelector(' . json_encode($firstDown) . '); control.click(); control.click();');
        $page->assertScript($readOrder, implode(',', [$posts[1]->getKey(), $posts[0]->getKey(), $posts[2]->getKey()]))
            ->assertScript($movesAreReady, true)
            ->assertPresent($search . ':focus');

        $page->script('document.querySelector(' . json_encode($firstUp) . ').click()');
        $page->assertScript($readOrder, $posts->pluck('id')->implode(','))->assertScript($movesAreReady, true);
    }
})->with(['standard' => false, 'grid' => true]);

it('allows retrying a failed table move without announcing an unpersisted position', function (bool $networkFailure): void {
    Artisan::call('filament:assets');
    $posts = Post::factory()->count(2)->sequence(['sort' => 1], ['sort' => 2])->create();
    $this->actingAs(User::factory()->create());
    $page = visit('/table-reordering-browser-test');
    $moveDown = '[x-sortable-item="' . $posts[0]->getKey() . '"] [data-table-reorder-direction="down"]';
    $moveUp = '[x-sortable-item="' . $posts[0]->getKey() . '"] [data-table-reorder-direction="up"]';
    $failure = $networkFailure ? 'Promise.reject(new TypeError("Network request failed"))' : 'Promise.resolve(new Response("", { status: 500 }))';

    foreach ([$page, $page->inDarkMode()] as $page) {
        $page->click('[data-testid="toggle-reordering"]')->assertEnabled($moveDown);
        $page->script(<<<JS
            () => {
                Livewire.hook('request', ({ fail }) => fail(({ preventDefault }) => preventDefault()));
                window.tableReorderFetch = window.fetch;
                window.fetch = (input, options) => {
                    if (String(input).includes('/livewire/update')) {
                        window.tableMoveFailed = true;
                        return {$failure};
                    }
                    return window.tableReorderFetch(input, options);
                };
            }
            JS);
        $page->script('document.querySelector(' . json_encode($moveDown) . ').click()');
        $page
            ->assertScript('window.tableMoveFailed', true)
            ->assertScript('document.querySelector(' . json_encode($moveDown) . ').getAttribute("aria-disabled") !== "true"', true)
            ->assertScript('document.querySelector("[x-ref=tableReorderStatus]").textContent.trim()', '');

        expect(Post::query()->orderBy('sort')->pluck('id')->all())->toBe($posts->pluck('id')->all());

        $page->script('() => { window.tableMoveFailed = false; }');
        $page->drag('[x-sortable-item="' . $posts[0]->getKey() . '"] .fi-ta-reorder-handle', '[x-sortable-item="' . $posts[1]->getKey() . '"] .fi-ta-reorder-handle')
            ->assertScript('window.tableMoveFailed', true)
            ->assertScript('document.querySelector(' . json_encode($moveDown) . ').getAttribute("aria-disabled") !== "true"', true)
            ->assertScript('document.querySelector("[x-sortable-item]").getAttribute("x-sortable-item")', (string) $posts[0]->getKey())
            ->assertScript('document.querySelector("[x-ref=tableReorderStatus]").textContent.trim()', '');

        $page->script('() => { window.fetch = window.tableReorderFetch; }');
        $page->script('document.querySelector(' . json_encode($moveDown) . ').click()');
        $page
            ->assertScript('document.querySelector("[x-sortable-item]").getAttribute("x-sortable-item")', (string) $posts[1]->getKey())
            ->assertSeeIn('[x-ref="tableReorderStatus"]', $posts[0]->title)
            ->assertNoAccessibilityIssues();

        expect(Post::query()->orderBy('sort')->pluck('id')->all())->toBe($posts->reverse()->values()->pluck('id')->all());
        $page->script('document.querySelector(' . json_encode($moveUp) . ').click()');
        $page
            ->assertScript('document.querySelector("[x-sortable-item]").getAttribute("x-sortable-item")', (string) $posts[0]->getKey());
    }
})->with(['HTTP failure' => false, 'network failure' => true]);

it('hides unauthorized table reorder controls in both themes', function (): void {
    Artisan::call('filament:assets');
    Post::factory()->count(2)->sequence(['sort' => 1], ['sort' => 2])->create();
    $this->actingAs(User::factory()->create());
    $page = visit('/table-reordering-browser-test?authorized=0');
    $page
        ->assertMissing('[data-testid="toggle-reordering"]')
        ->assertMissing('[data-table-reorder-direction]')
        ->assertNoAccessibilityIssues();
    $page->inDarkMode()->assertNoAccessibilityIssues();
});

describe('reordering', function (): void {
    it('moves array records through custom `reorderTable()` persistence', function (): void {
        $component = livewire(new class extends PostsReorderableTable
        {
            public function table(Table $table): Table
            {
                return parent::table($table)->query(null)->records(static fn (): array => [
                    'first-record' => ['title' => 'Alpha'],
                    'second-record' => ['title' => 'Beta'],
                    'third-record' => ['title' => 'Gamma'],
                ]);
            }

            public function reorderTable(array $order, int | string | null $draggedRecordKey = null): void
            {
                $this->beforeReorderingOrder = $order;
            }
        });

        $component->call('toggleTableReordering')
            ->call('moveTableRecord', 'second-record', 'down')
            ->assertSet('beforeReorderingOrder', ['first-record', 'third-record', 'second-record']);
    });

    it('moves records in rendered order and respects boundaries and authorization', function (string $direction): void {
        $posts = Post::factory()->count(3)->sequence(['sort' => 1], ['sort' => 2], ['sort' => 3])->create();
        $orderedPosts = $direction === 'desc' ? $posts->reverse()->values() : $posts;
        $order = $orderedPosts->pluck('id')->map('strval')->all();
        $component = livewire(PostsReorderableTable::class, ['direction' => $direction]);

        $component->call('moveTableRecord', (string) $orderedPosts[0]->getKey(), 'down');
        expect($posts->map(fn (Post $post): int => postSort($post->getKey()))->all())->toBe([1, 2, 3]);

        $component->call('toggleTableReordering')
            ->call('moveTableRecord', $order[0], 'up')
            ->call('moveTableRecord', $order[2], 'down')
            ->call('moveTableRecord', 'missing', 'down')
            ->call('moveTableRecord', $order[0], 'sideways');

        expect($posts->map(fn (Post $post): int => postSort($post->getKey()))->all())->toBe([1, 2, 3]);

        $component->call('moveTableRecord', $order[0], 'down');
        [$order[0], $order[1]] = [$order[1], $order[0]];
        $component->assertSet('beforeReorderingOrder', $order)->assertSet('afterReorderingOrder', $order);

        expect(Post::query()->orderBy('sort', $direction)->pluck('id')->map('strval')->all())->toBe($order);

        $component->set('reorderAuthorized', false)->call('moveTableRecord', $order[0], 'down');
        expect(Post::query()->orderBy('sort', $direction)->pluck('id')->map('strval')->all())->toBe($order);
    })->with(['asc', 'desc']);

    it('reorders `BelongsToMany` pivot records', function (): void {
        $ticket = Ticket::factory()->create();
        $departments = Department::factory()->count(3)->create();
        attachDepartmentsWithSort($ticket, $departments);

        $order = $departments->pluck('id')->reverse()->values()->all();

        livewire(TicketDepartmentsReorderableTable::class, ['ticketKey' => $ticket->getKey()])
            ->call('reorderTable', $order);

        $sortByDepartment = pivotSortKeyedByDepartment($ticket);

        foreach ($order as $index => $departmentKey) {
            expect($sortByDepartment[$departmentKey])->toBe($index + 1);
        }
    });

    it('reorders `BelongsToMany` pivot records in the `desc` direction', function (): void {
        $ticket = Ticket::factory()->create();
        $departments = Department::factory()->count(3)->create();
        attachDepartmentsWithSort($ticket, $departments);

        $order = $departments->pluck('id')->values()->all();

        livewire(TicketDepartmentsReorderableTable::class, [
            'ticketKey' => $ticket->getKey(),
            'direction' => 'desc',
        ])
            ->call('reorderTable', $order);

        $sortByDepartment = pivotSortKeyedByDepartment($ticket);

        $count = count($order);

        foreach ($order as $index => $departmentKey) {
            expect($sortByDepartment[$departmentKey])->toBe($count - $index);
        }
    });

    it('reorders `BelongsToMany` pivot records when `allowDuplicates()` is enabled', function (): void {
        $ticket = Ticket::factory()->create();
        $departments = Department::factory()->count(3)->create();
        attachDepartmentsWithSort($ticket, $departments);

        $pivotKeys = DB::table('department_ticket')
            ->where('ticket_id', $ticket->getKey())
            ->orderBy('sort')
            ->pluck('id')
            ->all();

        $order = array_reverse($pivotKeys);

        livewire(TicketDepartmentsReorderableTable::class, [
            'ticketKey' => $ticket->getKey(),
            'duplicates' => true,
        ])
            ->call('reorderTable', $order);

        $sortByPivotKey = DB::table('department_ticket')
            ->where('ticket_id', $ticket->getKey())
            ->pluck('sort', 'id')
            ->all();

        foreach ($order as $index => $pivotKey) {
            expect($sortByPivotKey[$pivotKey])->toBe($index + 1);
        }
    });

    it('reorders `BelongsToMany` pivot records with a `wherePivot()` constraint applied', function (): void {
        $ticket = Ticket::factory()->create();
        $departments = Department::factory()->count(3)->create();
        attachDepartmentsWithSort($ticket, $departments, extraPivotData: ['quantity' => 5]);

        $order = $departments->pluck('id')->reverse()->values()->all();

        livewire(TicketDepartmentsReorderableTable::class, [
            'ticketKey' => $ticket->getKey(),
            'minimumQuantity' => 5,
        ])
            ->call('reorderTable', $order);

        $sortByDepartment = pivotSortKeyedByDepartment($ticket);

        foreach ($order as $index => $departmentKey) {
            expect($sortByDepartment[$departmentKey])->toBe($index + 1);
        }
    });

    it('does not reorder another parent\'s pivot records', function (): void {
        $departments = Department::factory()->count(3)->create();

        $ticket = Ticket::factory()->create();
        attachDepartmentsWithSort($ticket, $departments);

        $otherTicket = Ticket::factory()->create();
        attachDepartmentsWithSort($otherTicket, $departments, sortOffset: 100);

        $otherSortBefore = pivotSortKeyedByDepartment($otherTicket);

        $order = $departments->pluck('id')->reverse()->values()->all();

        livewire(TicketDepartmentsReorderableTable::class, ['ticketKey' => $ticket->getKey()])
            ->call('reorderTable', $order);

        expect(pivotSortKeyedByDepartment($otherTicket))->toBe($otherSortBefore);
    });

    it('reorders `BelongsToMany` pivot records in a constant number of queries regardless of record count', function (): void {
        $measure = function (int $recordCount): int {
            $ticket = Ticket::factory()->create();
            $departments = Department::factory()->count($recordCount)->create();
            attachDepartmentsWithSort($ticket, $departments);

            $order = $departments->pluck('id')->reverse()->values()->all();

            $component = livewire(TicketDepartmentsReorderableTable::class, ['ticketKey' => $ticket->getKey()]);
            $component->instance()->getTable();

            DB::flushQueryLog();
            DB::enableQueryLog();

            $component->instance()->reorderTable($order);

            $queryCount = count(DB::getQueryLog());

            DB::disableQueryLog();

            return $queryCount;
        };

        expect($measure(20))->toBe($measure(3));
    });

    it('reorders records', function (): void {
        $posts = Post::factory()->count(3)->create();

        $order = $posts->pluck('id')->reverse()->values()->all();

        livewire(PostsReorderableTable::class)
            ->call('reorderTable', $order);

        foreach ($order as $index => $postKey) {
            expect(postSort($postKey))->toBe($index + 1);
        }
    });

    it('reorders records in the `desc` direction', function (): void {
        $posts = Post::factory()->count(3)->create();

        $order = $posts->pluck('id')->values()->all();

        livewire(PostsReorderableTable::class, ['direction' => 'desc'])
            ->call('reorderTable', $order);

        $count = count($order);

        foreach ($order as $index => $postKey) {
            expect(postSort($postKey))->toBe($count - $index);
        }
    });

    it('reorders records in a constant number of queries regardless of record count', function (): void {
        $measure = function (int $recordCount): int {
            $posts = Post::factory()->count($recordCount)->create();

            $order = $posts->pluck('id')->reverse()->values()->all();

            $component = livewire(PostsReorderableTable::class);
            $component->instance()->getTable();

            DB::flushQueryLog();
            DB::enableQueryLog();

            $component->instance()->reorderTable($order);

            $queryCount = count(DB::getQueryLog());

            DB::disableQueryLog();

            return $queryCount;
        };

        expect($measure(20))->toBe($measure(3));
    });

    it('does not reorder records when the table is not reorderable', function (): void {
        $posts = Post::factory()->count(3)->create();

        $order = $posts->pluck('id')->reverse()->values()->all();

        livewire(PostsReorderableTable::class, ['reorderable' => false])
            ->call('reorderTable', $order);

        foreach ($posts as $post) {
            expect(postSort($post->getKey()))->toBeNull();
        }
    });

    it('calls the `beforeReordering()` and `afterReordering()` callbacks when reordering', function (): void {
        $posts = Post::factory()->count(3)->create();

        $order = $posts->pluck('id')->reverse()->values()->all();

        livewire(PostsReorderableTable::class)
            ->call('reorderTable', $order)
            ->assertSet('beforeReorderingOrder', $order)
            ->assertSet('afterReorderingOrder', $order);
    });
});
