<?php

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\Testing\Fixtures\TestAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\TicketMessage;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\Fixtures\Resources\Posts\Pages\ListPosts;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\TicketMessages\TicketMessageResource;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\ListTickets;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;
use Filament\Tests\Fixtures\Resources\Users\UserResource;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Auth\Access\Response;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can render posts page', function (): void {
    $this->get(PostResource::getUrl('index'))
        ->assertSuccessful();
});

it('can render users page', function (): void {
    $this->get(UserResource::getUrl('index'))
        ->assertSuccessful();
});

it('can list posts', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(ListPosts::class)
        ->assertCanSeeTableRecords($posts);
});

it('can render post titles', function (): void {
    Post::factory()->count(10)->create();

    livewire(ListPosts::class)
        ->assertCanRenderTableColumn('title');
});

it('can render post authors', function (): void {
    Post::factory()->count(10)->create();

    livewire(ListPosts::class)
        ->assertCanRenderTableColumn('author.name');
});

it('can sort posts by title', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(ListPosts::class)
        ->sortTable('title')
        ->assertCanSeeTableRecords($posts->sortBy('title'), inOrder: true)
        ->sortTable('title', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('title'), inOrder: true);
});

it('can sort posts by author', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(ListPosts::class)
        ->sortTable('author.name')
        ->assertCanSeeTableRecords($posts->sortBy('author.name'), inOrder: true)
        ->sortTable('author.name', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('author.name'), inOrder: true);
});

it('can search posts by title', function (): void {
    $posts = Post::factory()->count(10)->create();

    $title = $posts->first()->title;

    livewire(ListPosts::class)
        ->searchTable($title)
        ->assertCanSeeTableRecords($posts->where('title', $title))
        ->assertCanNotSeeTableRecords($posts->where('title', '!=', $title));
});

it('can search posts by author', function (): void {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author->name;

    livewire(ListPosts::class)
        ->searchTable($author)
        ->assertCanSeeTableRecords($posts->where('author.name', $author))
        ->assertCanNotSeeTableRecords($posts->where('author.name', '!=', $author));
});

it('can filter posts by `is_published`', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(ListPosts::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanSeeTableRecords($posts->where('is_published', true))
        ->assertCanNotSeeTableRecords($posts->where('is_published', false));
});

it('can delete posts', function (): void {
    $post = Post::factory()->create();

    livewire(ListPosts::class)
        ->callTableAction(DeleteAction::class, $post);

    assertSoftDeleted($post);
});

it('can bulk delete posts', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(ListPosts::class)
        ->callTableBulkAction(DeleteBulkAction::class, $posts);

    foreach ($posts as $post) {
        assertSoftDeleted($post);
    }
});

it('can render ticket messages page without a policy', function (): void {
    TicketMessage::factory(10)
        ->create();

    $this->get(TicketMessageResource::getUrl('index'))
        ->assertSuccessful();
});

it('can render tickets page if the policy viewAny returns true', function (): void {
    Ticket::factory(10)
        ->create();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);

    $this->get(TicketResource::getUrl('index'))
        ->assertSuccessful();
});

it('can render tickets page if the policy viewAny returns an allowed response', function (): void {
    Ticket::factory(10)
        ->create();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): Response => Response::allow());

    $this->get(TicketResource::getUrl('index'))
        ->assertSuccessful();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('does not render ticket messages page without a policy if authorization is strict', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    TicketMessage::factory(10)
        ->create();

    $this->get(TicketMessageResource::getUrl('index'))
        ->assertServerError();

    Filament::getCurrentOrDefaultPanel()->strictAuthorization(false);
});

it('does not render tickets page if the policy viewAny returns false', function (): void {
    Ticket::factory(10)
        ->create();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => false);

    $this->get(TicketResource::getUrl('index'))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('does not render tickets page if the policy viewAny returns a denied response', function (): void {
    Ticket::factory(10)
        ->create();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): Response => Response::deny());

    $this->get(TicketResource::getUrl('index'))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('renders actions based on policy', function (string $action, string $policyMethod, bool | Response $policyResult, bool $isVisible, bool $isSoftDeleted = false, bool $isTableAction = false, bool $isBulkAction = false): void {
    app()->bind(TicketPolicy::class . '::' . $policyMethod, fn (): bool | Response => $policyResult);

    if ($isTableAction) {
        $ticket = Ticket::factory()
            ->create();

        if ($isSoftDeleted) {
            $ticket->delete();
        }

        livewire(ListTickets::class)
            ->filterTable('trashed', $isSoftDeleted ? 1 : null)
            ->{$isVisible ? 'assertActionVisible' : 'assertActionHidden'}(TestAction::make($action)->table($ticket)->bulk($isBulkAction));
    } else {
        livewire(ListTickets::class)
            ->{$isVisible ? 'assertActionVisible' : 'assertActionHidden'}($action);
    }

    app()->bind(TicketPolicy::class . '::' . $policyMethod, fn (): bool => true);
})->with([
    'create action with policy returning true' => fn (): array => [CreateAction::class, 'create', true, true],
    'create action with policy returning allowed response' => fn (): array => [CreateAction::class, 'create', Response::allow(), true],
    'create action with policy returning false' => fn (): array => [CreateAction::class, 'create', false, false],
    'create action with policy returning denied response' => fn (): array => [CreateAction::class, 'create', Response::deny(), false],
    'view action with policy returning true' => fn (): array => [ViewAction::class, 'view', true, true, false, true],
    'view action with policy returning allowed response' => fn (): array => [ViewAction::class, 'view', Response::allow(), true, false, true],
    'view action with policy returning false' => fn (): array => [ViewAction::class, 'view', false, false, false, true],
    'view action with policy returning denied response' => fn (): array => [ViewAction::class, 'view', Response::deny(), false, false, true],
    'edit action with policy returning true' => fn (): array => [EditAction::class, 'update', true, true, false, true],
    'edit action with policy returning allowed response' => fn (): array => [EditAction::class, 'update', Response::allow(), true, false, true],
    'edit action with policy returning false' => fn (): array => [EditAction::class, 'update', false, false, false, true],
    'edit action with policy returning denied response' => fn (): array => [EditAction::class, 'update', Response::deny(), false, false, true],
    'delete action with policy returning true' => fn (): array => [DeleteAction::class, 'delete', true, true, false, true],
    'delete action with policy returning allowed response' => fn (): array => [DeleteAction::class, 'delete', Response::allow(), true, false, true],
    'delete action with policy returning false' => fn (): array => [DeleteAction::class, 'delete', false, false, false, true],
    'delete action with policy returning denied response' => fn (): array => [DeleteAction::class, 'delete', Response::deny(), false, false, true],
    'force delete action with policy returning true' => fn (): array => [ForceDeleteAction::class, 'forceDelete', true, true, true, true],
    'force delete action with policy returning allowed response' => fn (): array => [ForceDeleteAction::class, 'forceDelete', Response::allow(), true, true, true],
    'force delete action with policy returning false' => fn (): array => [ForceDeleteAction::class, 'forceDelete', false, false, true, true],
    'force delete action with policy returning denied response' => fn (): array => [ForceDeleteAction::class, 'forceDelete', Response::deny(), false, true, true],
    'restore action with policy returning true' => fn (): array => [RestoreAction::class, 'restore', true, true, true, true],
    'restore action with policy returning allowed response' => fn (): array => [RestoreAction::class, 'restore', Response::allow(), true, true, true],
    'restore action with policy returning false' => fn (): array => [RestoreAction::class, 'restore', false, false, true, true],
    'restore action with policy returning denied response' => fn (): array => [RestoreAction::class, 'restore', Response::deny(), false, true, true],
    'replicate action with policy returning true' => fn (): array => [ReplicateAction::class, 'replicate', true, true, false, true],
    'replicate action with policy returning allowed response' => fn (): array => [ReplicateAction::class, 'replicate', Response::allow(), true, false, true],
    'replicate action with policy returning false' => fn (): array => [ReplicateAction::class, 'replicate', false, false, false, true],
    'replicate action with policy returning denied response' => fn (): array => [ReplicateAction::class, 'replicate', Response::deny(), false, false, true],
    'delete bulk action with policy returning true' => fn (): array => [DeleteBulkAction::class, 'deleteAny', true, true, false, true, true],
    'delete bulk action with policy returning allowed response' => fn (): array => [DeleteBulkAction::class, 'deleteAny', Response::allow(), true, false, true, true],
    'delete bulk action with policy returning false' => fn (): array => [DeleteBulkAction::class, 'deleteAny', false, false, false, true, true],
    'delete bulk action with policy returning denied response' => fn (): array => [DeleteBulkAction::class, 'deleteAny', Response::deny(), false, false, true, true],
    'force delete bulk action with policy returning true' => fn (): array => [ForceDeleteBulkAction::class, 'forceDeleteAny', true, true, true, true, true],
    'force delete bulk action with policy returning allowed response' => fn (): array => [ForceDeleteBulkAction::class, 'forceDeleteAny', Response::allow(), true, true, true, true],
    'force delete bulk action with policy returning false' => fn (): array => [ForceDeleteBulkAction::class, 'forceDeleteAny', false, false, true, true, true],
    'force delete bulk action with policy returning denied response' => fn (): array => [ForceDeleteBulkAction::class, 'forceDeleteAny', Response::deny(), false, true, true, true],
    'restore bulk action with policy returning true' => fn (): array => [RestoreBulkAction::class, 'restoreAny', true, true, true, true, true],
    'restore bulk action with policy returning allowed response' => fn (): array => [RestoreBulkAction::class, 'restoreAny', Response::allow(), true, true, true, true],
    'restore bulk action with policy returning false' => fn (): array => [RestoreBulkAction::class, 'restoreAny', false, false, true, true, true],
    'restore bulk action with policy returning denied response' => fn (): array => [RestoreBulkAction::class, 'restoreAny', Response::deny(), false, true, true, true],
]);
