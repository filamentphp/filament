<?php

use Filament\Actions\Testing\TestAction;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseHas;

uses(TestCase::class);

it('can render `ReplicateAction`', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertActionExists(TestAction::make('replicate')->table($post));
});

it('can mount `ReplicateAction` modal', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->mountAction(TestAction::make('replicate')->table($post))
        ->assertActionMounted(TestAction::make('replicate')->table($post));
});

it('can replicate a record using `ReplicateAction`', function (): void {
    $post = Post::factory()->create(['title' => 'Original Post']);

    livewire(PostsTable::class)
        ->callTableAction('replicate', $post)
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseHas('posts', [
        'title' => 'Original Post (Copy)',
    ]);
});

it('can show success notification after replicating a record', function (): void {
    $post = Post::factory()->create(['title' => 'Test Post']);

    livewire(PostsTable::class)
        ->callTableAction('replicate', $post)
        ->callMountedTableAction()
        ->assertNotified();
});

it('creates a new record when replicating', function (): void {
    $post = Post::factory()->create(['title' => 'Original Post']);
    $initialCount = Post::count();

    livewire(PostsTable::class)
        ->callTableAction('replicate', $post)
        ->callMountedTableAction();

    expect(Post::count())->toBe($initialCount + 1);
});

it('preserves original record when replicating', function (): void {
    $post = Post::factory()->create(['title' => 'Original Post']);

    livewire(PostsTable::class)
        ->callTableAction('replicate', $post)
        ->callMountedTableAction();

    expect($post->refresh()->title)->toBe('Original Post');
});

it('can modify replicated data using `mutateRecordDataUsing()`', function (): void {
    $post = Post::factory()->create(['title' => 'My Article']);

    livewire(PostsTable::class)
        ->callTableAction('replicate', $post)
        ->callMountedTableAction();

    // The PostsTable fixture appends ' (Copy)' to the title
    assertDatabaseHas('posts', [
        'title' => 'My Article (Copy)',
    ]);
});

it('can replicate multiple records sequentially', function (): void {
    $post1 = Post::factory()->create(['title' => 'First Post']);
    $post2 = Post::factory()->create(['title' => 'Second Post']);

    livewire(PostsTable::class)
        ->callTableAction('replicate', $post1)
        ->callMountedTableAction()
        ->callTableAction('replicate', $post2)
        ->callMountedTableAction();

    assertDatabaseHas('posts', ['title' => 'First Post (Copy)']);
    assertDatabaseHas('posts', ['title' => 'Second Post (Copy)']);
});
