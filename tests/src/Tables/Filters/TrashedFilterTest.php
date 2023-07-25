<?php

use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can filter records by trashed status', function () {
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

it('can delete records that are not already deleted', function () {
    $post = Post::factory()->create();
    $trashedPost = Post::factory()->trashed()->create();

    $this->assertModelExists($post);
    $this->assertNotSoftDeleted($post);

    $this->assertModelExists($trashedPost);
    $this->assertSoftDeleted($trashedPost);

    livewire(PostsTable::class)
        ->callTableAction(DeleteAction::class, $post)
        ->filterTable(TrashedFilter::class, true)
        ->assertTableActionHidden(DeleteAction::class, $trashedPost)
        ->mountTableAction(DeleteAction::class, $trashedPost)
        ->assertTableActionNotMounted(DeleteAction::class);

    $this->assertSoftDeleted($post);

    $this->assertModelExists($trashedPost);
    $this->assertSoftDeleted($trashedPost);
});

it('can force delete records that are already deleted', function () {
    $post = Post::factory()->create();
    $trashedPost = Post::factory()->trashed()->create();

    $this->assertModelExists($post);
    $this->assertNotSoftDeleted($post);

    $this->assertModelExists($trashedPost);
    $this->assertSoftDeleted($trashedPost);

    livewire(PostsTable::class)
        ->assertTableActionHidden(ForceDeleteAction::class, $post)
        ->mountTableAction(ForceDeleteAction::class, $post)
        ->assertTableActionNotMounted(ForceDeleteAction::class)
        ->filterTable(TrashedFilter::class, true)
        ->callTableAction(ForceDeleteAction::class, $trashedPost);

    $this->assertModelExists($post);
    $this->assertNotSoftDeleted($post);

    $this->assertModelMissing($trashedPost);
});

it('can restore records that are already deleted', function () {
    $post = Post::factory()->create();
    $trashedPost = Post::factory()->trashed()->create();

    $this->assertModelExists($post);
    $this->assertNotSoftDeleted($post);

    $this->assertModelExists($trashedPost);
    $this->assertSoftDeleted($trashedPost);

    livewire(PostsTable::class)
        ->assertTableActionHidden(RestoreAction::class, $post)
        ->mountTableAction(RestoreAction::class, $post)
        ->assertTableActionNotMounted(RestoreAction::class)
        ->filterTable(TrashedFilter::class, true)
        ->callTableAction(RestoreAction::class, $trashedPost);

    $this->assertModelExists($post);
    $this->assertNotSoftDeleted($post);

    $this->assertModelExists($trashedPost);
    $this->assertNotSoftDeleted($trashedPost);
});
