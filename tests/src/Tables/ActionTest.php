<?php

use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can call action', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction(DeleteAction::class, $post);

    $this->assertModelMissing($post);
});

it('can call bulk action', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction(DeleteBulkAction::class, $posts);

    foreach ($posts as $post) {
        $this->assertModelMissing($post);
    }
});

it('can call an action with data', function () {
    livewire(PostsTable::class)
        ->callTableAction('form', data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoTableActionErrors()
        ->assertEmitted('form-called', [
            'payload' => $payload,
        ]);
});

it('can validate an action\'s data', function () {
    livewire(PostsTable::class)
        ->callTableAction('form', data: [
            'payload' => null,
        ])
        ->assertHasTableActionErrors(['payload' => ['required']])
        ->assertNotEmitted('form-called');
});

it('can call an action with arguments', function () {
    livewire(PostsTable::class)
        ->callTableAction('arguments', arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->assertEmitted('arguments-called', [
            'payload' => $payload,
        ]);
});

it('can call an action and hold', function () {
    livewire(PostsTable::class)
        ->callTableAction('hold')
        ->assertEmitted('hold-called')
        ->assertTableActionHeld('hold');
});
