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

it('can call a bulk action and halt', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction('halt', records: $posts)
        ->assertEmitted('halt-called')
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
        ->assertTableBulkActionHasIcon('has-icon', 'heroicon-s-pencil')
        ->assertTableBulkActionDoesNotHaveIcon('has-icon', 'heroicon-o-trash');
});

it('can have a label', function () {
    livewire(PostsTable::class)
        ->assertTableBulkActionHasLabel('has-label', 'My Action')
        ->assertTableBulkActionDoesNotHaveLabel('has-label', 'My Other Action');
});

it('can have a color', function () {
    livewire(PostsTable::class)
        ->assertTableBulkActionHasColor('has-color', 'primary')
        ->assertTableBulkActionDoesNotHaveColor('has-color', 'secondary');
});

it('can state whether a bulk action exists', function () {
    livewire(PostsTable::class)
        ->assertTableBulkActionExists('exists')
        ->assertTableBulkActionDoesNotExist('does_not_exist');
});

it('can state whether several bulk actions exist in order', function () {
    livewire(PostsTable::class)
        ->assertTableBulkActionsExistInOrder(['exists', 'exists_in_order']);
});
