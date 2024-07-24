<?php

use Filament\Actions\AttachAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\Fixtures\TestAction;
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
        ->callAction(TestAction::make(DeleteAction::class)->table($post));

    assertSoftDeleted($post);

    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction(DeleteAction::class, $post);

    assertSoftDeleted($post);
});

it('can call an action with data', function () {
    livewire(PostsTable::class)
        ->callAction(TestAction::make('data')->table(), data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertDispatched('data-called', data: [
            'payload' => $payload,
        ]);

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
        ->callAction(TestAction::make('groupedDelete')->table($post));

    assertSoftDeleted($post);

    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction('groupedDelete', $post);

    assertSoftDeleted($post);
});

it('can validate an action\'s data', function () {
    livewire(PostsTable::class)
        ->callAction(TestAction::make('data')->table(), data: [
            'payload' => null,
        ])
        ->assertHasActionErrors(['payload' => ['required']])
        ->assertNotDispatched('data-called');

    livewire(PostsTable::class)
        ->callTableAction('data', data: [
            'payload' => null,
        ])
        ->assertHasTableActionErrors(['payload' => ['required']])
        ->assertNotDispatched('data-called');
});

it('can set default action data when mounted', function () {
    livewire(PostsTable::class)
        ->mountAction(TestAction::make('data')->table())
        ->assertActionDataSet([
            'foo' => 'bar',
        ]);

    livewire(PostsTable::class)
        ->mountTableAction('data')
        ->assertTableActionDataSet([
            'foo' => 'bar',
        ]);
});

it('can call a nested action registered in the modal footer', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callAction([
            TestAction::make('parent')->table($post),
            TestAction::make('footer'),
        ], [
            'bar' => $bar = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertDispatched('nested-called', bar: $bar, recordKey: $post->getKey())
        ->setActionData([
            'foo' => $foo = Str::random(),
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors()
        ->assertDispatched('parent-called', foo: $foo, recordKey: $post->getKey());
});

it('can call a manually modal registered nested action', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callAction([
            TestAction::make('parent')->table($post),
            TestAction::make('manuallyRegisteredModal'),
        ], [
            'bar' => $bar = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertDispatched('nested-called', bar: $bar, recordKey: $post->getKey())
        ->setActionData([
            'foo' => $foo = Str::random(),
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors()
        ->assertDispatched('parent-called', foo: $foo, recordKey: $post->getKey());
});

it('can call a nested action registered on a schema component', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callAction([
            TestAction::make('parent')->table($post),
            TestAction::make('nested')->schemaComponent('foo'),
        ], [
            'bar' => $bar = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertDispatched('nested-called', bar: $bar, recordKey: $post->getKey())
        ->setActionData([
            'foo' => $foo = Str::random(),
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors()
        ->assertDispatched('parent-called', foo: $foo, recordKey: $post->getKey());
});

it('can cancel a parent action when calling a nested action', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callAction([
            TestAction::make('parent')->table($post),
            TestAction::make('cancelParent')->schemaComponent('foo'),
        ], [
            'bar' => $bar = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertDispatched('nested-called', bar: $bar, recordKey: $post->getKey())
        ->assertActionNotMounted()
        ->assertNotDispatched('parent-called');
});

it('can call a grouped nested action registered in the modal footer', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callAction([
            TestAction::make('groupedParent')->table($post),
            TestAction::make('footer'),
        ], [
            'bar' => $bar = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertDispatched('nested-called', bar: $bar, recordKey: $post->getKey())
        ->setActionData([
            'foo' => $foo = Str::random(),
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors()
        ->assertDispatched('grouped-parent-called', foo: $foo, recordKey: $post->getKey());
});

it('can call a grouped manually modal registered nested action', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callAction([
            TestAction::make('groupedParent')->table($post),
            TestAction::make('manuallyRegisteredModal'),
        ], [
            'bar' => $bar = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertDispatched('nested-called', bar: $bar, recordKey: $post->getKey())
        ->setActionData([
            'foo' => $foo = Str::random(),
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors()
        ->assertDispatched('grouped-parent-called', foo: $foo, recordKey: $post->getKey());
});

it('can call a grouped nested action registered on a schema component', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callAction([
            TestAction::make('groupedParent')->table($post),
            TestAction::make('nested')->schemaComponent('foo'),
        ], [
            'bar' => $bar = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertDispatched('nested-called', bar: $bar, recordKey: $post->getKey())
        ->setActionData([
            'foo' => $foo = Str::random(),
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors()
        ->assertDispatched('grouped-parent-called', foo: $foo, recordKey: $post->getKey());
});

it('can cancel a grouped parent action when calling a nested action', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callAction([
            TestAction::make('groupedParent')->table($post),
            TestAction::make('cancelParent')->schemaComponent('foo'),
        ], [
            'bar' => $bar = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertDispatched('nested-called', bar: $bar, recordKey: $post->getKey())
        ->assertActionNotMounted()
        ->assertNotDispatched('grouped-parent-called');
});

it('can call an action with arguments', function () {
    livewire(PostsTable::class)
        ->callAction(TestAction::make('arguments')->table()->arguments([
            'payload' => $payload = Str::random(),
        ]))
        ->assertDispatched('arguments-called', arguments: [
            'payload' => $payload,
        ]);

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
        ->callAction(TestAction::make('halt')->table())
        ->assertDispatched('halt-called')
        ->assertActionHalted(TestAction::make('halt')->table());

    livewire(PostsTable::class)
        ->callTableAction('halt')
        ->assertDispatched('halt-called')
        ->assertTableActionHalted('halt');
});

it('can hide an action', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertActionVisible(TestAction::make('visible')->table())
        ->assertActionHidden(TestAction::make('hidden')->table());

    livewire(PostsTable::class)
        ->assertTableActionVisible('visible')
        ->assertTableActionHidden('hidden')
        ->assertTableActionVisible('groupedWithVisibleGroupCondition', $post)
        ->assertTableActionHidden('groupedWithHiddenGroupCondition', $post);
});

it('can disable an action', function () {
    livewire(PostsTable::class)
        ->assertActionEnabled(TestAction::make('enabled')->table())
        ->assertActionDisabled(TestAction::make('disabled')->table());

    livewire(PostsTable::class)
        ->assertTableActionEnabled('enabled')
        ->assertTableActionDisabled('disabled');
});

it('can have an icon', function () {
    livewire(PostsTable::class)
        ->assertActionHasIcon(TestAction::make('hasIcon')->table(), 'heroicon-m-pencil-square')
        ->assertActionDoesNotHaveIcon(TestAction::make('hasIcon')->table(), 'heroicon-m-trash');

    livewire(PostsTable::class)
        ->assertTableActionHasIcon('hasIcon', 'heroicon-m-pencil-square')
        ->assertTableActionDoesNotHaveIcon('hasIcon', 'heroicon-m-trash');
});

it('can have a label', function () {
    livewire(PostsTable::class)
        ->assertActionHasLabel(TestAction::make('hasLabel')->table(), 'My Action')
        ->assertActionDoesNotHaveLabel(TestAction::make('hasLabel')->table(), 'My Other Action');

    livewire(PostsTable::class)
        ->assertTableActionHasLabel('hasLabel', 'My Action')
        ->assertTableActionDoesNotHaveLabel('hasLabel', 'My Other Action');
});

it('can have a color', function () {
    livewire(PostsTable::class)
        ->assertActionHasColor(TestAction::make('hasColor')->table(), 'primary')
        ->assertActionDoesNotHaveColor(TestAction::make('hasColor')->table(), 'gray');

    livewire(PostsTable::class)
        ->assertTableActionHasColor('hasColor', 'primary')
        ->assertTableActionDoesNotHaveColor('hasColor', 'gray');
});

it('can have a URL', function () {
    livewire(PostsTable::class)
        ->assertActionHasUrl(TestAction::make('url')->table(), 'https://filamentphp.com')
        ->assertActionDoesNotHaveUrl(TestAction::make('url')->table(), 'https://google.com');

    livewire(PostsTable::class)
        ->assertTableActionHasUrl('url', 'https://filamentphp.com')
        ->assertTableActionDoesNotHaveUrl('url', 'https://google.com');
});

it('can open a URL in a new tab', function () {
    livewire(PostsTable::class)
        ->assertActionShouldOpenUrlInNewTab(TestAction::make('urlInNewTab')->table())
        ->assertActionShouldNotOpenUrlInNewTab(TestAction::make('urlNotInNewTab')->table());

    livewire(PostsTable::class)
        ->assertTableActionShouldOpenUrlInNewTab('urlInNewTab')
        ->assertTableActionShouldNotOpenUrlInNewTab('urlNotInNewTab');
});

it('can state whether a table action exists', function () {
    livewire(PostsTable::class)
        ->assertActionExists(TestAction::make('exists')->table())
        ->assertActionDoesNotExist(TestAction::make('doesNotExist')->table());

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
        ->assertActionExists(TestAction::make('attachMultiple')->table(), fn (AttachAction $action) => $action->isMultiple())
        ->assertActionDoesNotExist(TestAction::make(AttachAction::class)->table(), fn (AttachAction $action) => $action->isMultiple());

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
