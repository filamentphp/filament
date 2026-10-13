<?php

use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\SelectablePostsTable;
use Filament\Tests\Fixtures\Livewire\TicketDepartmentsReorderableTable;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Tables\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can call bulk action', function (): void {
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
        'max', 'scopeLimit', 'globalScopeLimit' => [9],
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
            } elseif (in_array($constraint, ['max', 'scopeLimit', 'scopeOffset', 'globalScopeLimit', 'globalScopeOffset'])) {
                $table
                    ->defaultSort('department_ticket.quantity', 'desc')
                    ->checkIfRecordIsSelectableUsing(static fn (): bool => true)
                    ->modifyQueryUsing(static fn (Builder $query): Builder => $query->where('department_ticket.quantity', '>', 1));

                if ($constraint === 'max') {
                    $table->maxSelectableRecords(1);
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
})->with(['search', 'columnSearch', 'filter', 'scope', 'max', 'scopeLimit', 'scopeOffset', 'globalScopeLimit', 'globalScopeOffset'])->with(['customBulk', 'queryBulk', 'chunked']);

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

    // Asserting the exact `keys` array proves both the count (only the 2 authorized
    // records) and their identity, while `assertNotDispatched` proves the denied
    // records never reached the action body alongside the authorized ones.
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
