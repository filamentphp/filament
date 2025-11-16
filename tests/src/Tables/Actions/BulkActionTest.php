<?php

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Livewire\PostsTable;
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
        ->callAction(TestAction::make(DeleteBulkAction::class)->bulk()->table());

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
        ->callAction(TestAction::make('data')->bulk()->table(), data: [
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
        ->callAction(TestAction::make('data')->bulk()->table(), data: [
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
        ->mountAction(TestAction::make('data')->bulk()->table())
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
        ])->bulk()->table())
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
        ->callAction(TestAction::make('halt')->bulk()->table())
        ->assertDispatched('halt-called')
        ->assertActionHalted(TestAction::make('halt')->bulk()->table());

    livewire(PostsTable::class)
        ->callTableBulkAction('halt', records: $posts)
        ->assertDispatched('halt-called')
        ->assertTableBulkActionHalted('halt');
});

it('can hide a bulk action', function (): void {
    livewire(PostsTable::class)
        ->assertActionVisible(TestAction::make('visible')->bulk()->table())
        ->assertActionHidden(TestAction::make('hidden')->bulk()->table());

    livewire(PostsTable::class)
        ->assertTableBulkActionVisible('visible')
        ->assertTableBulkActionHidden('hidden');
});

it('can disable a bulk action', function (): void {
    livewire(PostsTable::class)
        ->assertActionEnabled(TestAction::make('enabled')->bulk()->table())
        ->assertActionDisabled(TestAction::make('disabled')->bulk()->table());

    livewire(PostsTable::class)
        ->assertTableBulkActionEnabled('enabled')
        ->assertTableBulkActionDisabled('disabled');
});

it('can have an icon', function (): void {
    livewire(PostsTable::class)
        ->assertActionHasIcon(TestAction::make('hasIcon')->bulk()->table(), Heroicon::PencilSquare)
        ->assertActionDoesNotHaveIcon(TestAction::make('hasIcon')->bulk()->table(), Heroicon::Trash);

    livewire(PostsTable::class)
        ->assertTableBulkActionHasIcon('hasIcon', Heroicon::PencilSquare)
        ->assertTableBulkActionDoesNotHaveIcon('hasIcon', Heroicon::Trash);
});

it('can have a label', function (): void {
    livewire(PostsTable::class)
        ->assertActionHasLabel(TestAction::make('hasLabel')->bulk()->table(), 'My Action')
        ->assertActionDoesNotHaveLabel(TestAction::make('hasLabel')->bulk()->table(), 'My Other Action');

    livewire(PostsTable::class)
        ->assertTableBulkActionHasLabel('hasLabel', 'My Action')
        ->assertTableBulkActionDoesNotHaveLabel('hasLabel', 'My Other Action');
});

it('can have a color', function (): void {
    livewire(PostsTable::class)
        ->assertActionHasColor(TestAction::make('hasColor')->bulk()->table(), 'primary')
        ->assertActionDoesNotHaveColor(TestAction::make('hasColor')->bulk()->table(), 'gray');

    livewire(PostsTable::class)
        ->assertTableBulkActionHasColor('hasColor', 'primary')
        ->assertTableBulkActionDoesNotHaveColor('hasColor', 'gray');
});

it('can state whether a bulk action exists', function (): void {
    livewire(PostsTable::class)
        ->assertActionExists(TestAction::make('exists')->bulk()->table())
        ->assertActionDoesNotExist(TestAction::make('doesNotExist')->bulk()->table());

    livewire(PostsTable::class)
        ->assertTableBulkActionExists('exists')
        ->assertTableBulkActionDoesNotExist('doesNotExist');
});

it('can state whether bulk actions exist in order', function (): void {
    livewire(PostsTable::class)
        ->assertTableBulkActionsExistInOrder(['exists', 'existsInOrder']);
});
