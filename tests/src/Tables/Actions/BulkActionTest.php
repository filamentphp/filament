<?php

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can call bulk action', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction(DeleteBulkAction::class, $posts);

    foreach ($posts as $post) {
        assertSoftDeleted($post);
    }
});

it('can call a bulk action with data', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction('data', records: $posts, data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoTableBulkActionErrors()
        ->assertDispatched('data-called', data: [
            'payload' => $payload,
        ]);
});

it('can validate a bulk action\'s data', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction('data', records: $posts, data: [
            'payload' => null,
        ])
        ->assertHasTableBulkActionErrors(['payload' => ['required']])
        ->assertNotDispatched('data-called');
});

it('can set default bulk action data when mounted', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->mountTableBulkAction('data', records: $posts)
        ->assertTableBulkActionDataSet([
            'foo' => 'bar',
        ])
        ->assertTableBulkActionDataSet(function (array $data): bool {
            return $data['foo'] === 'bar';
        });
});

it('can call a bulk action with arguments', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction('arguments', records: $posts, arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->assertDispatched('arguments-called', arguments: [
            'payload' => $payload,
        ]);
});

it('can call a bulk action and halt', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction('halt', records: $posts)
        ->assertDispatched('halt-called')
        ->assertTableBulkActionHalted('halt');
});

it('can hide a bulk action', function () {
    livewire(PostsTable::class)
        ->assertTableBulkActionVisible('visible')
        ->assertTableBulkActionHidden('hidden');
});

it('can disable a bulk action', function () {
    livewire(PostsTable::class)
        ->assertTableBulkActionEnabled('enabled')
        ->assertTableBulkActionDisabled('disabled');
});

it('can have an icon', function () {
    livewire(PostsTable::class)
        ->assertTableBulkActionHasIcon('hasIcon', 'heroicon-m-pencil-square')
        ->assertTableBulkActionDoesNotHaveIcon('hasIcon', 'heroicon-m-trash');
});

it('can have a label', function () {
    livewire(PostsTable::class)
        ->assertTableBulkActionHasLabel('hasLabel', 'My Action')
        ->assertTableBulkActionDoesNotHaveLabel('hasLabel', 'My Other Action');
});

it('can have a color', function () {
    livewire(PostsTable::class)
        ->assertTableBulkActionHasColor('hasColor', 'primary')
        ->assertTableBulkActionDoesNotHaveColor('hasColor', 'gray');
});

it('can state whether a bulk action exists', function () {
    livewire(PostsTable::class)
        ->assertTableBulkActionExists('exists')
        ->assertTableBulkActionDoesNotExist('does_not_exist');
});

it('can state whether bulk actions exist in order', function () {
    livewire(PostsTable::class)
        ->assertTableBulkActionsExistInOrder(['exists', 'existsInOrder']);
});

describe('selected record caching', function (): void {
    it('resolves the current selection after its record keys change', function (): void {
        $posts = Post::factory()->count(2)->create();
        $livewire = livewire(PostsTable::class)->instance();
        $livewire->selectedTableRecords = [(string) $posts->first()->getKey()];

        expect($livewire->getSelectedTableRecords()->modelKeys())->toBe([$posts->first()->getKey()]);

        $livewire->selectedTableRecords = [(string) $posts->last()->getKey()];

        expect($livewire->getSelectedTableRecords()->modelKeys())->toBe([$posts->last()->getKey()]);
    });

    it('separates record keys from models when the fetch mode changes', function (): void {
        $post = Post::factory()->create();
        $livewire = livewire(PostsTable::class)->instance();
        $livewire->selectedTableRecords = [(string) $post->getKey()];

        expect($livewire->getSelectedTableRecords(shouldFetchSelectedRecords: false)->map(strval(...))->all())->toBe([(string) $post->getKey()])
            ->and($livewire->getSelectedTableRecords()->modelKeys())->toBe([$post->getKey()])
            ->and($livewire->getSelectedTableRecords(shouldFetchSelectedRecords: false)->map(strval(...))->all())->toBe([(string) $post->getKey()]);
    });

    it('refreshes selected records when the table cache is flushed', function (): void {
        $post = Post::factory()->create(['is_published' => true]);
        $livewire = livewire(SelectionRefreshPostsTable::class)->instance();
        $livewire->selectedTableRecords = [(string) $post->getKey()];

        expect($livewire->getSelectedTableRecords()->modelKeys())->toBe([$post->getKey()]);

        $post->update(['is_published' => false]);
        $livewire->flushCachedTableRecords();

        expect($livewire->getSelectedTableRecords())->toBeEmpty();
    });

    it('refreshes selected records before mounting a bulk action', function (): void {
        $post = Post::factory()->create(['is_published' => true]);
        $livewire = livewire(SelectionRefreshPostsTable::class)->instance();
        $livewire->selectedTableRecords = [(string) $post->getKey()];

        expect($livewire->getSelectedTableRecords()->modelKeys())->toBe([$post->getKey()]);

        $post->update(['is_published' => false]);
        $livewire->mountTableBulkAction('delete');

        expect($livewire->getSelectedTableRecords())->toBeEmpty();
    });

    it('refreshes selected records before executing a bulk action', function (bool $shouldRevokeSelection): void {
        $post = Post::factory()->create(['is_published' => true]);
        $livewire = livewire(SelectionRefreshPostsTable::class)->instance();
        $livewire->mountTableBulkAction('delete', [(string) $post->getKey()]);

        expect($livewire->getSelectedTableRecords()->modelKeys())->toBe([$post->getKey()]);

        if ($shouldRevokeSelection) {
            $post->update(['is_published' => false]);
        }

        $livewire->callMountedTableBulkAction();

        expect($post->fresh()->trashed())->toBe(! $shouldRevokeSelection);
    })->with([false, true]);

    it('refreshes selected records before mounting an ordinary table action', function (): void {
        $post = Post::factory()->create(['is_published' => true]);
        $livewire = livewire(SelectionRefreshPostsTable::class)->instance();
        $livewire->selectedTableRecords = [(string) $post->getKey()];

        expect($livewire->getSelectedTableRecords()->modelKeys())->toBe([$post->getKey()]);

        $post->update(['is_published' => false]);
        $livewire->mountTableAction('inspectSelected');

        expect($livewire->selectedRecordKeys)->toBeEmpty();
    });

    it('refreshes selected records between a modal-less action mount and execution', function (): void {
        $post = Post::factory()->create(['is_published' => true]);
        $livewire = livewire(SelectionRefreshPostsTable::class)->instance();
        $livewire->selectedTableRecords = [(string) $post->getKey()];
        $livewire->mountTableAction('changeSelected');

        expect($post->fresh())
            ->is_published->toBeFalse()
            ->deleted_at->toBeNull();
    });
});

class SelectionRefreshPostsTable extends PostsTable
{
    public array $selectedRecordKeys = [];

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->query(Post::query()->where('is_published', true))
            ->headerActions([
                Action::make('inspectSelected')
                    ->accessSelectedRecords()
                    ->requiresConfirmation()
                    ->mountUsing(function (Collection $selectedRecords): void {
                        $this->selectedRecordKeys = $selectedRecords->modelKeys();
                    }),
                Action::make('changeSelected')
                    ->accessSelectedRecords()
                    ->mountUsing(static fn (Collection $selectedRecords) => $selectedRecords->each->update(['is_published' => false]))
                    ->action(static fn (Collection $selectedRecords) => $selectedRecords->each->delete()),
            ]);
    }
}
