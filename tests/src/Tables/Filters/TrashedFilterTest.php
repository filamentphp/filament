<?php

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can filter records by trashed status', function (): void {
    $posts = Post::factory()->count(5)->create();
    $trashedPosts = Post::factory()->count(5)->trashed()->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->assertCanNotSeeTableRecords($trashedPosts)
        ->filterTable(TrashedFilter::class, true)
        ->assertCanSeeTableRecords($posts)
        ->assertCanSeeTableRecords($trashedPosts)
        ->filterTable(TrashedFilter::class, false)
        ->assertCanNotSeeTableRecords($posts)
        ->assertCanSeeTableRecords($trashedPosts)
        ->filterTable(TrashedFilter::class, null)
        ->assertCanSeeTableRecords($posts)
        ->assertCanNotSeeTableRecords($trashedPosts);
});

it('can delete records that are not already deleted', function (): void {
    $post = Post::factory()->create();
    $trashedPost = Post::factory()->trashed()->create();

    assertModelExists($post);
    assertNotSoftDeleted($post);

    assertModelExists($trashedPost);
    assertSoftDeleted($trashedPost);

    livewire(PostsTable::class)
        ->callTableAction(DeleteAction::class, $post)
        ->filterTable(TrashedFilter::class, true)
        ->assertTableActionHidden(DeleteAction::class, $trashedPost)
        ->mountTableAction(DeleteAction::class, $trashedPost)
        ->assertTableActionNotMounted(DeleteAction::class);

    assertSoftDeleted($post);

    assertModelExists($trashedPost);
    assertSoftDeleted($trashedPost);
});

it('can force delete records that are already deleted', function (): void {
    $post = Post::factory()->create();
    $trashedPost = Post::factory()->trashed()->create();

    assertModelExists($post);
    assertNotSoftDeleted($post);

    assertModelExists($trashedPost);
    assertSoftDeleted($trashedPost);

    livewire(PostsTable::class)
        ->assertTableActionHidden(ForceDeleteAction::class, $post)
        ->mountTableAction(ForceDeleteAction::class, $post)
        ->assertTableActionNotMounted(ForceDeleteAction::class)
        ->filterTable(TrashedFilter::class, true)
        ->callTableAction(ForceDeleteAction::class, $trashedPost);

    assertModelExists($post);
    assertNotSoftDeleted($post);

    assertModelMissing($trashedPost);
});

it('can restore records that are already deleted', function (): void {
    $post = Post::factory()->create();
    $trashedPost = Post::factory()->trashed()->create();

    assertModelExists($post);
    assertNotSoftDeleted($post);

    assertModelExists($trashedPost);
    assertSoftDeleted($trashedPost);

    livewire(PostsTable::class)
        ->assertTableActionHidden(RestoreAction::class, $post)
        ->mountTableAction(RestoreAction::class, $post)
        ->assertTableActionNotMounted(RestoreAction::class)
        ->filterTable(TrashedFilter::class, true)
        ->callTableAction(RestoreAction::class, $trashedPost);

    assertModelExists($post);
    assertNotSoftDeleted($post);

    assertModelExists($trashedPost);
    assertNotSoftDeleted($trashedPost);
});
