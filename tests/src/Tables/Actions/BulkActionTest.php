<?php

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\SelectablePostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;
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
