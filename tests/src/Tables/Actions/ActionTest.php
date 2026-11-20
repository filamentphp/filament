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

describe('table action registration context', function (): void {
    it('does not mount non-record actions with a supplied record', function (string $name): void {
        $post = Post::factory()->create();

        livewire(PostsTable::class)
            ->mountTableAction($name, $post)
            ->assertTableActionNotMounted($name)
            ->callMountedTableAction()
            ->assertNotDispatched("{$name}-called");
    })->with(['arguments', 'groupedHeader', 'empty', 'create']);

    it('can call non-record actions without a supplied record', function (string $name): void {
        livewire(PostsTable::class)
            ->callTableAction($name)
            ->assertDispatched("{$name}-called");
    })->with(['arguments', 'groupedHeader', 'empty']);

    it('can mount and submit a column action with its record', function (): void {
        $post = Post::factory()->create();

        livewire(PostsTable::class)
            ->mountTableAction('column-action-object', $post)
            ->assertTableActionMounted('column-action-object')
            ->callMountedTableAction()
            ->assertDispatched('column-action-object-called', recordKey: $post->getKey());
    });

    it('can submit a nested grouped record action', function (): void {
        $post = Post::factory()->create();

        livewire(PostsTable::class)
            ->callTableAction('groupedWithVisibleGroupCondition.nested', $post)
            ->assertDispatched('nested-called', recordKey: $post->getKey());
    });

    it('does not submit a header action after its record is changed', function (): void {
        $post = Post::factory()->create();

        livewire(PostsTable::class)
            ->mountTableAction('arguments')
            ->set('mountedTableActionRecord', $post->getKey())
            ->call('callMountedTableAction')
            ->assertNotDispatched('arguments-called');
    });

    it('only submits a nested header action without a supplied record', function (): void {
        $post = Post::factory()->create();

        livewire(PostsTable::class)
            ->call('mountTableAction', 'arguments', $post->getKey())
            ->call('mountTableAction', 'nested', $post->getKey())
            ->call('callMountedTableAction')
            ->assertNotDispatched('nested-called');

        livewire(PostsTable::class)
            ->callTableAction('arguments.nested')
            ->assertDispatched('nested-called');
    });

    it('can create another header record after correcting validation errors', function (): void {
        $post = Post::factory()->create();
        $originalCount = Post::count();

        $livewire = livewire(PostsTable::class)
            ->mountTableAction('create')
            ->callMountedTableAction()
            ->assertHasTableActionErrors(['title' => 'required', 'author_id' => 'required'])
            ->assertTableActionMounted('create')
            ->setTableActionData([
                'title' => $firstTitle = Str::random(),
                'author_id' => $post->author_id,
                'metadata' => ['seo_title' => 'First metadata'],
            ])
            ->callMountedTableAction(['another' => true])
            ->assertHasNoTableActionErrors()
            ->assertTableActionMounted('create')
            ->assertTableActionDataSet(['title' => null, 'metadata' => ['seo_title' => null]]);

        $firstPost = Post::query()->where('title', $firstTitle)->firstOrFail();

        $livewire->assertDispatched('created', recordKey: $firstPost->getKey())
            ->setTableActionData([
                'title' => $secondTitle = Str::random(),
                'author_id' => $post->author_id,
                'metadata' => ['seo_title' => 'Second metadata'],
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors()
            ->assertTableActionNotMounted('create');

        $secondPost = Post::query()->where('title', $secondTitle)->firstOrFail();

        $livewire->assertDispatched('created', recordKey: $secondPost->getKey());

        expect(Post::count())->toBe($originalCount + 2)
            ->and($firstPost->metadata?->seo_title)->toBe('First metadata')
            ->and($secondPost->metadata?->seo_title)->toBe('Second metadata');
    });

    it('does not save create action relationships to the supplied table record', function (): void {
        $post = Post::factory()->create();
        $metadata = $post->metadata()->create(['seo_title' => 'Original metadata']);

        livewire(PostsTable::class)
            ->callTableAction('createFromRecord', $post, data: [
                'title' => $title = Str::random(),
                'author_id' => $post->author_id,
                'metadata' => ['seo_title' => 'New table metadata'],
            ])
            ->assertHasNoTableActionErrors();

        expect($metadata->refresh()->seo_title)->toBe('Original metadata')
            ->and(Post::query()->where('title', $title)->firstOrFail()->metadata?->seo_title)->toBe('New table metadata');
    });
});

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
        ])
        ->assertTableActionDataSet(function (array $data): bool {
            return $data['foo'] === 'bar';
        });
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
