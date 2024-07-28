<?php

use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can call action', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction(DeleteAction::class, $post);

    assertSoftDeleted($post);
});

it('can call an action with data', function () {
    livewire(PostsTable::class)
        ->callTableAction('data', data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoTableActionErrors()
        ->assertDispatched('data-called', data: [
            'payload' => $payload,
        ]);
});

it('can call action inside group', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction('groupedDelete', $post);

    assertSoftDeleted($post);
});

it('can validate an action\'s data', function () {
    livewire(PostsTable::class)
        ->callTableAction('data', data: [
            'payload' => null,
        ])
        ->assertHasTableActionErrors(['payload' => ['required']])
        ->assertNotDispatched('data-called');
});

it('can set default action data when mounted', function () {
    livewire(PostsTable::class)
        ->mountTableAction('data')
        ->assertTableActionDataSet([
            'foo' => 'bar',
        ]);
});

it('can call an action with arguments', function () {
    livewire(PostsTable::class)
        ->callTableAction('arguments', arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->assertDispatched('arguments-called', arguments: [
            'payload' => $payload,
        ]);
});

it('can call an action and halt', function () {
    livewire(PostsTable::class)
        ->callTableAction('halt')
        ->assertDispatched('halt-called')
        ->assertTableActionHalted('halt');
});

it('can hide an action', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableActionVisible('visible')
        ->assertTableActionHidden('hidden')
        ->assertTableActionVisible('groupedWithVisibleGroupCondition', $post)
        ->assertTableActionHidden('groupedWithHiddenGroupCondition', $post);
});

it('can disable an action', function () {
    livewire(PostsTable::class)
        ->assertTableActionEnabled('enabled')
        ->assertTableActionDisabled('disabled');
});

it('can have an icon', function () {
    livewire(PostsTable::class)
        ->assertTableActionHasIcon('hasIcon', 'heroicon-m-pencil-square')
        ->assertTableActionDoesNotHaveIcon('hasIcon', 'heroicon-m-trash');
});

it('can have a label', function () {
    livewire(PostsTable::class)
        ->assertTableActionHasLabel('hasLabel', 'My Action')
        ->assertTableActionDoesNotHaveLabel('hasLabel', 'My Other Action');
});

it('can have a color', function () {
    livewire(PostsTable::class)
        ->assertTableActionHasColor('hasColor', 'primary')
        ->assertTableActionDoesNotHaveColor('hasColor', 'gray');
});

it('can have a URL', function () {
    livewire(PostsTable::class)
        ->assertTableActionHasUrl('url', 'https://filamentphp.com')
        ->assertTableActionDoesNotHaveUrl('url', 'https://google.com');
});

it('can open a URL in a new tab', function () {
    livewire(PostsTable::class)
        ->assertTableActionShouldOpenUrlInNewTab('urlInNewTab')
        ->assertTableActionShouldNotOpenUrlInNewTab('urlNotInNewTab');
});

it('can state whether a table action exists', function () {
    livewire(PostsTable::class)
        ->assertTableActionExists('exists')
        ->assertTableActionDoesNotExist('doesNotExist');
});

it('can state whether table actions exist in order', function () {
    livewire(PostsTable::class)
        ->assertTableActionsExistInOrder(['edit', 'delete'])
        ->assertTableHeaderActionsExistInOrder(['exists', 'existsInOrder'])
        ->assertTableEmptyStateActionsExistInOrder(['emptyExists', 'emptyExistsInOrder']);
});

it('can state whether a table action exists with a given configuration', function () {
    livewire(PostsTable::class)
        ->assertTableActionExists('attachMultiple', fn (AttachAction $action) => $action->isMultiple())
        ->assertTableActionDoesNotExist(AttachAction::class, fn (AttachAction $action) => $action->isMultiple());
});

it('can replicate a record', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableActionExists('replicate')
        ->callTableAction('replicate', $post)
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseHas('posts', [
        'title' => $post->title . ' (Copy)',
    ]);
});
