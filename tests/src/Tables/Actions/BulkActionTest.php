<?php

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\Fixtures\TestAction;
use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can call bulk action', function () {
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

it('can call a bulk action with data', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->selectTableRecords($posts)
        ->callAction(TestAction::make('data')->table()->bulk(), data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoActionErrors()
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

it('can validate a bulk action\'s data', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->selectTableRecords($posts)
        ->callAction(TestAction::make('data')->table()->bulk(), data: [
            'payload' => null,
        ])
        ->assertHasActionErrors(['payload' => ['required']])
        ->assertNotDispatched('data-called');

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
        ->selectTableRecords($posts)
        ->mountAction(TestAction::make('data')->table()->bulk())
        ->assertActionDataSet([
            'foo' => 'bar',
        ]);

    livewire(PostsTable::class)
        ->mountTableBulkAction('data', records: $posts)
        ->assertTableBulkActionDataSet([
            'foo' => 'bar',
        ]);
});

it('can call a bulk action with arguments', function () {
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

it('can call a bulk action and halt', function () {
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

it('can hide a bulk action', function () {
    livewire(PostsTable::class)
        ->assertActionVisible(TestAction::make('visible')->table()->bulk())
        ->assertActionHidden(TestAction::make('hidden')->table()->bulk());

    livewire(PostsTable::class)
        ->assertTableBulkActionVisible('visible')
        ->assertTableBulkActionHidden('hidden');
});

it('can disable a bulk action', function () {
    livewire(PostsTable::class)
        ->assertActionEnabled(TestAction::make('enabled')->table()->bulk())
        ->assertActionDisabled(TestAction::make('disabled')->table()->bulk());

    livewire(PostsTable::class)
        ->assertTableBulkActionEnabled('enabled')
        ->assertTableBulkActionDisabled('disabled');
});

it('can have an icon', function () {
    livewire(PostsTable::class)
        ->assertActionHasIcon(TestAction::make('hasIcon')->table()->bulk(), 'heroicon-m-pencil-square')
        ->assertActionDoesNotHaveIcon(TestAction::make('hasIcon')->table()->bulk(), 'heroicon-m-trash');

    livewire(PostsTable::class)
        ->assertTableBulkActionHasIcon('hasIcon', 'heroicon-m-pencil-square')
        ->assertTableBulkActionDoesNotHaveIcon('hasIcon', 'heroicon-m-trash');
});

it('can have a label', function () {
    livewire(PostsTable::class)
        ->assertActionHasLabel(TestAction::make('hasLabel')->table()->bulk(), 'My Action')
        ->assertActionDoesNotHaveLabel(TestAction::make('hasLabel')->table()->bulk(), 'My Other Action');

    livewire(PostsTable::class)
        ->assertTableBulkActionHasLabel('hasLabel', 'My Action')
        ->assertTableBulkActionDoesNotHaveLabel('hasLabel', 'My Other Action');
});

it('can have a color', function () {
    livewire(PostsTable::class)
        ->assertActionHasColor(TestAction::make('hasColor')->table()->bulk(), 'primary')
        ->assertActionDoesNotHaveColor(TestAction::make('hasColor')->table()->bulk(), 'gray');

    livewire(PostsTable::class)
        ->assertTableBulkActionHasColor('hasColor', 'primary')
        ->assertTableBulkActionDoesNotHaveColor('hasColor', 'gray');
});

it('can state whether a bulk action exists', function () {
    livewire(PostsTable::class)
        ->assertActionExists(TestAction::make('exists')->table()->bulk())
        ->assertActionDoesNotExist(TestAction::make('doesNotExist')->table()->bulk());

    livewire(PostsTable::class)
        ->assertTableBulkActionExists('exists')
        ->assertTableBulkActionDoesNotExist('doesNotExist');
});

it('can state whether bulk actions exist in order', function () {
    livewire(PostsTable::class)
        ->assertTableBulkActionsExistInOrder(['exists', 'existsInOrder']);
});
