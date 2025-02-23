<?php

use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\Fixtures\TestAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can call action', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callAction(TestAction::make(DeleteAction::class)->table($post));

    assertSoftDeleted($post);

    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction(DeleteAction::class, $post);

    assertSoftDeleted($post);
});

it('can call an action with data', function (): void {
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

it('can call action inside group', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callAction(TestAction::make('groupedDelete')->table($post));

    assertSoftDeleted($post);

    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction('groupedDelete', $post);

    assertSoftDeleted($post);
});

it('can validate an action\'s data', function (): void {
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

it('can set default action data when mounted', function (): void {
    livewire(PostsTable::class)
        ->mountAction(TestAction::make('data')->table())
        ->assertActionDataSet([
            'foo' => 'bar',
        ]);

    livewire(PostsTable::class)
        ->mountTableAction('data')
        ->assertTableActionDataSet([
            'foo' => 'bar',
        ])
        ->assertTableActionDataSet(function (array $data): bool {
            return $data['foo'] === 'bar';
        });
});

it('can call a nested action registered in the modal footer', function (): void {
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

it('can call a manually modal registered nested action', function (): void {
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

it('can call a nested action registered on a schema component', function (): void {
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

it('can cancel a parent action when calling a nested action', function (): void {
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

it('can call a grouped nested action registered in the modal footer', function (): void {
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

it('can call a grouped manually modal registered nested action', function (): void {
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

it('can call a grouped nested action registered on a schema component', function (): void {
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

it('can cancel a grouped parent action when calling a nested action', function (): void {
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

it('can call an action with arguments', function (): void {
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

it('can call an action and halt', function (): void {
    livewire(PostsTable::class)
        ->callAction(TestAction::make('halt')->table())
        ->assertDispatched('halt-called')
        ->assertActionHalted(TestAction::make('halt')->table());

    livewire(PostsTable::class)
        ->callTableAction('halt')
        ->assertDispatched('halt-called')
        ->assertTableActionHalted('halt');
});

it('can hide an action', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertActionVisible(TestAction::make('visible')->table())
        ->assertActionHidden(TestAction::make('hidden')->table())
        ->assertActionExists(TestAction::make('visible')->table(), fn (Action $action): bool => $action->isVisible())
        ->assertActionExists(TestAction::make('hidden')->table(), fn (Action $action): bool => $action->isHidden())
        ->assertActionDoesNotExist(TestAction::make('visible')->table(), fn (Action $action): bool => $action->isHidden())
        ->assertActionDoesNotExist(TestAction::make('hidden')->table(), fn (Action $action): bool => $action->isVisible());

    livewire(PostsTable::class)
        ->assertTableActionVisible('visible')
        ->assertTableActionHidden('hidden')
        ->assertTableActionVisible('groupedWithVisibleGroupCondition', $post)
        ->assertTableActionHidden('groupedWithHiddenGroupCondition', $post)
        ->assertTableActionExists('visible', fn (Action $action): bool => $action->isVisible())
        ->assertTableActionExists('hidden', fn (Action $action): bool => $action->isHidden())
        ->assertTableActionDoesNotExist('visible', fn (Action $action): bool => $action->isHidden())
        ->assertTableActionDoesNotExist('hidden', fn (Action $action): bool => $action->isVisible())
        ->assertTableActionExists('groupedWithVisibleGroupCondition', fn (Action $action): bool => $action->isVisible(), $post)
        ->assertTableActionExists('groupedWithHiddenGroupCondition', fn (Action $action): bool => $action->isHidden(), $post)
        ->assertTableActionDoesNotExist('groupedWithVisibleGroupCondition', fn (Action $action): bool => $action->isHidden(), $post)
        ->assertTableActionDoesNotExist('groupedWithHiddenGroupCondition', fn (Action $action): bool => $action->isVisible(), $post);
});

it('can disable an action', function (): void {
    livewire(PostsTable::class)
        ->assertActionEnabled(TestAction::make('enabled')->table())
        ->assertActionDisabled(TestAction::make('disabled')->table());

    livewire(PostsTable::class)
        ->assertTableActionEnabled('enabled')
        ->assertTableActionDisabled('disabled');
});

it('can have an icon', function (): void {
    livewire(PostsTable::class)
        ->assertActionHasIcon(TestAction::make('hasIcon')->table(), Heroicon::PencilSquare)
        ->assertActionDoesNotHaveIcon(TestAction::make('hasIcon')->table(), Heroicon::Trash);

    livewire(PostsTable::class)
        ->assertTableActionHasIcon('hasIcon', Heroicon::PencilSquare)
        ->assertTableActionDoesNotHaveIcon('hasIcon', Heroicon::Trash);
});

it('can have a label', function (): void {
    livewire(PostsTable::class)
        ->assertActionHasLabel(TestAction::make('hasLabel')->table(), 'My Action')
        ->assertActionDoesNotHaveLabel(TestAction::make('hasLabel')->table(), 'My Other Action');

    livewire(PostsTable::class)
        ->assertTableActionHasLabel('hasLabel', 'My Action')
        ->assertTableActionDoesNotHaveLabel('hasLabel', 'My Other Action');
});

it('can have a color', function (): void {
    livewire(PostsTable::class)
        ->assertActionHasColor(TestAction::make('hasColor')->table(), 'primary')
        ->assertActionDoesNotHaveColor(TestAction::make('hasColor')->table(), 'gray');

    livewire(PostsTable::class)
        ->assertTableActionHasColor('hasColor', 'primary')
        ->assertTableActionDoesNotHaveColor('hasColor', 'gray');
});

it('can have a URL', function (): void {
    livewire(PostsTable::class)
        ->assertActionHasUrl(TestAction::make('url')->table(), 'https://filamentphp.com')
        ->assertActionDoesNotHaveUrl(TestAction::make('url')->table(), 'https://google.com');

    livewire(PostsTable::class)
        ->assertTableActionHasUrl('url', 'https://filamentphp.com')
        ->assertTableActionDoesNotHaveUrl('url', 'https://google.com');
});

it('can open a URL in a new tab', function (): void {
    livewire(PostsTable::class)
        ->assertActionShouldOpenUrlInNewTab(TestAction::make('urlInNewTab')->table())
        ->assertActionShouldNotOpenUrlInNewTab(TestAction::make('urlNotInNewTab')->table());

    livewire(PostsTable::class)
        ->assertTableActionShouldOpenUrlInNewTab('urlInNewTab')
        ->assertTableActionShouldNotOpenUrlInNewTab('urlNotInNewTab');
});

it('can state whether a table action exists', function (): void {
    livewire(PostsTable::class)
        ->assertActionExists(TestAction::make('exists')->table())
        ->assertActionDoesNotExist(TestAction::make('doesNotExist')->table());

    livewire(PostsTable::class)
        ->assertTableActionExists('exists')
        ->assertTableActionDoesNotExist('doesNotExist');
});

it('can state whether table actions exist in order', function (): void {
    livewire(PostsTable::class)
        ->assertTableActionsExistInOrder(['edit', 'delete'])
        ->assertTableHeaderActionsExistInOrder(['exists', 'existsInOrder'])
        ->assertTableEmptyStateActionsExistInOrder(['emptyExists', 'emptyExistsInOrder']);
});

it('can state whether a table action exists with a given configuration', function (): void {
    livewire(PostsTable::class)
        ->assertActionExists(TestAction::make('attachMultiple')->table(), fn (AttachAction $action) => $action->isMultiple())
        ->assertActionDoesNotExist(TestAction::make(AttachAction::class)->table(), fn (AttachAction $action) => $action->isMultiple());

    livewire(PostsTable::class)
        ->assertTableActionExists('attachMultiple', fn (AttachAction $action) => $action->isMultiple())
        ->assertTableActionDoesNotExist(AttachAction::class, fn (AttachAction $action) => $action->isMultiple());
});

it('can replicate a record', function (): void {
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
