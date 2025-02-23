<?php

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\TicketMessage;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\Fixtures\Resources\Posts\Pages\ViewPost;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\TicketMessages\TicketMessageResource;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\ViewTicket;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function (): void {
    $this->get(PostResource::getUrl('view', [
        'record' => Post::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data', function (): void {
    $post = Post::factory()->create();

    livewire(ViewPost::class, [
        'record' => $post->getKey(),
    ])
        ->assertFormSet([
            'author_id' => $post->author->getKey(),
            'content' => $post->content,
            'tags' => $post->tags,
            'title' => $post->title,
        ]);
});

it('can refresh data', function (): void {
    $post = Post::factory()->create();

    $page = livewire(ViewPost::class, [
        'record' => $post->getKey(),
    ]);

    $originalPostTitle = $post->title;

    $page->assertFormSet([
        'title' => $originalPostTitle,
    ]);

    $newPostTitle = Str::random();

    $post->title = $newPostTitle;
    $post->save();

    $page->assertFormSet([
        'title' => $originalPostTitle,
    ]);

    $page->call('refreshTitle');

    $page->assertFormSet([
        'title' => $newPostTitle,
    ]);
});

it('can ticket messages page without a policy', function (): void {
    $message = TicketMessage::factory()
        ->create();

    $this->get(TicketMessageResource::getUrl('view', ['record' => $message]))
        ->assertSuccessful();
});

it('can render tickets page if the policy viewAny returns true', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('view', ['record' => $ticket]))
        ->assertSuccessful();
});

it('can render tickets page if the policy viewAny returns an allowed response', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): Response => Response::allow());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('view', ['record' => $ticket]))
        ->assertSuccessful();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('does not render ticket messages page without a policy if authorization is strict', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $message = TicketMessage::factory()
        ->create();

    $this->get(TicketMessageResource::getUrl('view', ['record' => $message]))
        ->assertServerError();

    Filament::getCurrentOrDefaultPanel()->strictAuthorization(false);
});

it('does not render tickets page if the policy viewAny returns false', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => false);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('view', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('does not render tickets page if the policy viewAny returns a denied response', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): Response => Response::deny());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('view', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('can render tickets page if the policy view returns true', function (): void {
    app()->bind(TicketPolicy::class . '::view', fn (): bool => true);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('view', ['record' => $ticket]))
        ->assertSuccessful();
});

it('can render tickets page if the policy view returns an allowed response', function (): void {
    app()->bind(TicketPolicy::class . '::view', fn (): Response => Response::allow());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('view', ['record' => $ticket]))
        ->assertSuccessful();

    app()->bind(TicketPolicy::class . '::view', fn (): bool => true);
});

it('does not render tickets page if the policy view returns false', function (): void {
    app()->bind(TicketPolicy::class . '::view', fn (): bool => false);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('view', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::view', fn (): bool => true);
});

it('does not render tickets page if the policy view returns a denied response', function (): void {
    app()->bind(TicketPolicy::class . '::view', fn (): Response => Response::deny());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('view', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::view', fn (): bool => true);
});

it('renders actions based on policy', function (string $action, string $policyMethod, bool | Response $policyResult, bool $isVisible, bool $isSoftDeleted = false): void {
    app()->bind(TicketPolicy::class . '::' . $policyMethod, fn (): bool | Response => $policyResult);

    $ticket = Ticket::factory()
        ->create();

    if ($isSoftDeleted) {
        $ticket->delete();
    }

    livewire(ViewTicket::class, ['record' => $ticket->getKey()])
        ->{$isVisible ? 'assertActionVisible' : 'assertActionHidden'}($action);

    app()->bind(TicketPolicy::class . '::' . $policyMethod, fn (): bool => true);
})->with([
    'create action with policy returning true' => fn (): array => [CreateAction::class, 'create', true, true],
    'create action with policy returning allowed response' => fn (): array => [CreateAction::class, 'create', Response::allow(), true],
    'create action with policy returning false' => fn (): array => [CreateAction::class, 'create', false, false],
    'create action with policy returning denied response' => fn (): array => [CreateAction::class, 'create', Response::deny(), false],
    'edit action with policy returning true' => fn (): array => [EditAction::class, 'update', true, true],
    'edit action with policy returning allowed response' => fn (): array => [EditAction::class, 'update', Response::allow(), true],
    'edit action with policy returning false' => fn (): array => [EditAction::class, 'update', false, false],
    'edit action with policy returning denied response' => fn (): array => [EditAction::class, 'update', Response::deny(), false],
    'delete action with policy returning true' => fn (): array => [DeleteAction::class, 'delete', true, true],
    'delete action with policy returning allowed response' => fn (): array => [DeleteAction::class, 'delete', Response::allow(), true],
    'delete action with policy returning false' => fn (): array => [DeleteAction::class, 'delete', false, false],
    'delete action with policy returning denied response' => fn (): array => [DeleteAction::class, 'delete', Response::deny(), false],
    'force delete action with policy returning true' => fn (): array => [ForceDeleteAction::class, 'forceDelete', true, true, true],
    'force delete action with policy returning allowed response' => fn (): array => [ForceDeleteAction::class, 'forceDelete', Response::allow(), true, true],
    'force delete action with policy returning false' => fn (): array => [ForceDeleteAction::class, 'forceDelete', false, false, true],
    'force delete action with policy returning denied response' => fn (): array => [ForceDeleteAction::class, 'forceDelete', Response::deny(), false, true],
    'restore action with policy returning true' => fn (): array => [RestoreAction::class, 'restore', true, true, true],
    'restore action with policy returning allowed response' => fn (): array => [RestoreAction::class, 'restore', Response::allow(), true, true],
    'restore action with policy returning false' => fn (): array => [RestoreAction::class, 'restore', false, false, true],
    'restore action with policy returning denied response' => fn (): array => [RestoreAction::class, 'restore', Response::deny(), false, true],
    'replicate action with policy returning true' => fn (): array => [ReplicateAction::class, 'replicate', true, true],
    'replicate action with policy returning allowed response' => fn (): array => [ReplicateAction::class, 'replicate', Response::allow(), true],
    'replicate action with policy returning false' => fn (): array => [ReplicateAction::class, 'replicate', false, false],
    'replicate action with policy returning denied response' => fn (): array => [ReplicateAction::class, 'replicate', Response::deny(), false],
]);
