<?php

use Filament\Tests\Fixtures\Livewire\PostsReorderableTable;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\TicketDepartmentsReorderableTable;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can list records', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts);
});

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

describe('reordering', function (): void {
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
