<?php

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\TicketMessage;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\Fixtures\Resources\Posts\Pages\EditPost;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\TicketMessages\TicketMessageResource;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can render page', function (): void {
    $this->get(PostResource::getUrl('edit', [
        'record' => Post::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data', function (): void {
    $post = Post::factory()->create();

    livewire(EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->assertFormSet([
            'author_id' => $post->author->getKey(),
            'content' => $post->content,
            'tags' => $post->tags,
            'title' => $post->title,
            'rating' => $post->rating,
        ]);
});

it('can save', function (): void {
    $post = Post::factory()->create();
    $newData = Post::factory()->make();

    livewire(EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'content' => $newData->content,
            'tags' => $newData->tags,
            'title' => $newData->title,
            'rating' => $newData->rating,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($post->refresh())
        ->author->toBeSameModel($newData->author)
        ->content->toBe($newData->content)
        ->tags->toBe($newData->tags)
        ->title->toBe($newData->title);
});

it('can validate input', function (): void {
    $post = Post::factory()->create();

    livewire(EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->fillForm([
            'title' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['title' => 'required']);
});

it('can delete', function (): void {
    $post = Post::factory()->create();

    livewire(EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->callAction(DeleteAction::class);

    assertSoftDeleted($post);
});

it('can refresh data', function (): void {
    $post = Post::factory()->create();

    $page = livewire(EditPost::class, [
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

    $this->get(TicketMessageResource::getUrl('edit', ['record' => $message]))
        ->assertSuccessful();
});

it('can render page if the policy viewAny returns true', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertSuccessful();
});

it('can render page if the policy viewAny returns an allowed response', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): Response => Response::allow());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertSuccessful();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('does not render page without a policy if authorization is strict', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $message = TicketMessage::factory()
        ->create();

    $this->get(TicketMessageResource::getUrl('edit', ['record' => $message]))
        ->assertServerError();

    Filament::getCurrentOrDefaultPanel()->strictAuthorization(false);
});

it('does not render page if the policy viewAny returns false', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => false);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('does not render page if the policy viewAny returns a denied response', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): Response => Response::deny());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('can render page if the policy update returns true', function (): void {
    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertSuccessful();
});

it('can render page if the policy update returns an allowed response', function (): void {
    app()->bind(TicketPolicy::class . '::update', fn (): Response => Response::allow());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertSuccessful();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
});

it('does not render page if the policy update returns false', function (): void {
    app()->bind(TicketPolicy::class . '::update', fn (): bool => false);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
});

it('does not render page if the policy update returns a denied response', function (): void {
    app()->bind(TicketPolicy::class . '::update', fn (): Response => Response::deny());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
});

it('renders actions based on policy', function (string $action, string $policyMethod, bool | Response $policyResult, bool $isVisible, bool $isSoftDeleted = false): void {
    app()->bind(TicketPolicy::class . '::' . $policyMethod, fn (): bool | Response => $policyResult);

    $ticket = Ticket::factory()
        ->create();

    if ($isSoftDeleted) {
        $ticket->delete();
    }

    livewire(EditTicket::class, ['record' => $ticket->getKey()])
        ->{$isVisible ? 'assertActionVisible' : 'assertActionHidden'}($action);

    app()->bind(TicketPolicy::class . '::' . $policyMethod, fn (): bool => true);
})->with([
    'create action with policy returning true' => fn (): array => [CreateAction::class, 'create', true, true],
    'create action with policy returning allowed response' => fn (): array => [CreateAction::class, 'create', Response::allow(), true],
    'create action with policy returning false' => fn (): array => [CreateAction::class, 'create', false, false],
    'create action with policy returning denied response' => fn (): array => [CreateAction::class, 'create', Response::deny(), false],
    'view action with policy returning true' => fn (): array => [ViewAction::class, 'view', true, true],
    'view action with policy returning allowed response' => fn (): array => [ViewAction::class, 'view', Response::allow(), true],
    'view action with policy returning false' => fn (): array => [ViewAction::class, 'view', false, false],
    'view action with policy returning denied response' => fn (): array => [ViewAction::class, 'view', Response::deny(), false],
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
