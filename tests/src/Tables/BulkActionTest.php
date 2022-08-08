<?php

use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can call bulk action', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction(DeleteBulkAction::class, $posts);

    foreach ($posts as $post) {
        $this->assertModelMissing($post);
    }
});

it('can call a bulk action with data', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction('data', records: $posts, data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoTableBulkActionErrors()
        ->assertEmitted('data-called', [
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
        ->assertNotEmitted('data-called');
});

it('can set default bulk action data when mounted', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->mountTableBulkAction('data', records: $posts)
        ->assertTableBulkActionDataSet([
            'foo' => 'bar',
        ]);
});

it('can call a bulk action with arguments', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction('arguments', records: $posts, arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->assertEmitted('arguments-called', [
            'payload' => $payload,
        ]);
});

it('can call a bulk action and hold', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction('hold', records: $posts)
        ->assertEmitted('hold-called')
        ->assertTableBulkActionHeld('hold');
});

it('can hide a bulk action', function () {
    livewire(PostsTable::class)
        ->assertTableBulkActionVisible('visible')
        ->assertTableBulkActionHidden('hidden');
});
