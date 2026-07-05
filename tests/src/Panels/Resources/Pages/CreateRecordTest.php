<?php

use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Events\RecordCreated;
use Filament\Resources\Events\RecordSaved;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\Fixtures\Resources\Posts\Pages\CreateAnotherPreservingDataPost;
use Filament\Tests\Fixtures\Resources\Posts\Pages\CreateAnotherPreservingRepeaterPost;
use Filament\Tests\Fixtures\Resources\Posts\Pages\CreateAnotherPreservingRepeaterWithDefaultPost;
use Filament\Tests\Fixtures\Resources\Posts\Pages\CreatePost;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\TicketMessages\TicketMessageResource;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\CreateTicket;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Event;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function (): void {
    $this->get(PostResource::getUrl('create'))
        ->assertSuccessful();
});

it('can create', function (): void {
    Event::fake([
        RecordCreated::class,
        RecordSaved::class,
    ]);

    $newData = Post::factory()->make();

    livewire(CreatePost::class)
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'content' => $newData->content,
            'tags' => $newData->tags,
            'title' => $newData->title,
            'rating' => $newData->rating,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $record = Post::query()

        ->where('author_id', $newData->author->getKey())

        ->where('content', $newData->content)

        ->where('title', $newData->title)

        ->where('rating', $newData->rating)

        ->first();

    expect($record)->not->toBeNull();

    expect($record->tags)->toBe($newData->tags);

    Event::assertDispatched(RecordCreated::class);
    Event::assertDispatched(RecordSaved::class);
});

it('can create another', function (): void {
    $newData = Post::factory()->make();
    $newData2 = Post::factory()->make();

    livewire(CreatePost::class)
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'content' => $newData->content,
            'tags' => $newData->tags,
            'title' => $newData->title,
            'rating' => $newData->rating,
        ])
        ->call('create', true)
        ->assertHasNoFormErrors()
        ->assertNoRedirect()
        ->assertSchemaStateSet([
            'author_id' => null,
            'content' => null,
            'tags' => [],
            'title' => null,
            'rating' => null,
        ])
        ->fillForm([
            'author_id' => $newData2->author->getKey(),
            'content' => $newData2->content,
            'tags' => $newData2->tags,
            'title' => $newData2->title,
            'rating' => $newData2->rating,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $record = Post::query()

        ->where('author_id', $newData->author->getKey())

        ->where('content', $newData->content)

        ->where('title', $newData->title)

        ->where('rating', $newData->rating)

        ->first();

    expect($record)->not->toBeNull();

    expect($record->tags)->toBe($newData->tags);

    $record2 = Post::query()
        ->where('author_id', $newData2->author->getKey())
        ->where('content', $newData2->content)
        ->where('title', $newData2->title)
        ->where('rating', $newData2->rating)
        ->first();

    expect($record2)->not->toBeNull();
    expect($record2->tags)->toBe($newData2->tags);
});

it('can create another and preserve data', function (): void {
    $newData = Post::factory()->make();
    $newData2 = Post::factory()->make();

    livewire(CreateAnotherPreservingDataPost::class)
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'content' => $newData->content,
            'tags' => $newData->tags,
            'title' => $newData->title,
            'rating' => $newData->rating,
        ])
        ->call('create', true)
        ->assertHasNoFormErrors()
        ->assertNoRedirect()
        ->assertSchemaStateSet([
            'author_id' => null,
            'content' => null,
            'tags' => $newData->tags,
            'title' => null,
            'rating' => $newData->rating,
        ])
        ->fillForm([
            'author_id' => $newData2->author->getKey(),
            'content' => $newData2->content,
            'title' => $newData2->title,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $record = Post::query()

        ->where('author_id', $newData->author->getKey())

        ->where('content', $newData->content)

        ->where('title', $newData->title)

        ->where('rating', $newData->rating)

        ->first();

    expect($record)->not->toBeNull();

    expect($record->tags)->toBe($newData->tags);

    $record2 = Post::query()
        ->where('author_id', $newData2->author->getKey())
        ->where('content', $newData2->content)
        ->where('title', $newData2->title)
        ->where('rating', $newData->rating)
        ->first();

    expect($record2)->not->toBeNull();
    expect($record2->tags)->toBe($newData->tags);
});

it('can create another and preserve repeater data', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $newData = Post::factory()->make();
    $newData2 = Post::factory()->make();

    $repeaterItems = [
        ['name' => 'First Item', 'email' => 'first@example.com'],
        ['name' => 'Second Item', 'email' => 'second@example.com'],
    ];

    livewire(CreateAnotherPreservingRepeaterPost::class)
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'title' => $newData->title,
            'rating' => $newData->rating,
            'json_array_of_objects' => $repeaterItems,
        ])
        ->call('create', true)
        ->assertHasNoFormErrors()
        ->assertNoRedirect()
        ->fillForm([
            'author_id' => $newData2->author->getKey(),
            'title' => $newData2->title,
            'rating' => $newData2->rating,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $record = Post::query()
        ->where('title', $newData->title)
        ->where('rating', $newData->rating)
        ->first();

    expect($record)->not->toBeNull();
    expect($record->json_array_of_objects)->toBe($repeaterItems);

    $record2 = Post::query()
        ->where('title', $newData2->title)
        ->where('rating', $newData2->rating)
        ->first();

    expect($record2)->not->toBeNull();
    expect($record2->json_array_of_objects)->toBe($repeaterItems);

    $undoRepeaterFake();
});

it('can create another and preserve repeater data with `default()` values', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $newData = Post::factory()->make();
    $newData2 = Post::factory()->make();

    $repeaterItems = [
        ['name' => 'Custom Item A', 'email' => 'a@example.com'],
        ['name' => 'Custom Item B', 'email' => 'b@example.com'],
        ['name' => 'Custom Item C', 'email' => 'c@example.com'],
    ];

    livewire(CreateAnotherPreservingRepeaterWithDefaultPost::class)
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'title' => $newData->title,
            'rating' => $newData->rating,
            'json_array_of_objects' => $repeaterItems,
        ])
        ->call('create', true)
        ->assertHasNoFormErrors()
        ->assertNoRedirect()
        ->fillForm([
            'author_id' => $newData2->author->getKey(),
            'title' => $newData2->title,
            'rating' => $newData2->rating,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $record = Post::query()
        ->where('title', $newData->title)
        ->where('rating', $newData->rating)
        ->first();

    expect($record)->not->toBeNull();
    expect($record->json_array_of_objects)->toBe($repeaterItems);

    $record2 = Post::query()
        ->where('title', $newData2->title)
        ->where('rating', $newData2->rating)
        ->first();

    expect($record2)->not->toBeNull();
    expect($record2->json_array_of_objects)->toBe($repeaterItems);

    $undoRepeaterFake();
});

it('can validate input', function (): void {
    Post::factory()->make();

    livewire(CreatePost::class)
        ->fillForm([
            'title' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required']);
});

it('can render page without a policy', function (): void {
    $this->get(TicketMessageResource::getUrl('create'))
        ->assertSuccessful();
});

it('can render page if the policy viewAny returns true', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);

    $this->get(TicketResource::getUrl('create'))
        ->assertSuccessful();
});

it('can render page if the policy viewAny returns an allowed response', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): Response => Response::allow());

    $this->get(TicketResource::getUrl('create'))
        ->assertSuccessful();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('does not render page without a policy if authorization is strict', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $this->get(TicketMessageResource::getUrl('create'))
        ->assertServerError();

    Filament::getCurrentOrDefaultPanel()->strictAuthorization(false);
});

it('does not render page if the policy viewAny returns false', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => false);

    $this->get(TicketResource::getUrl('create'))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('does not render page if the policy viewAny returns a denied response', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): Response => Response::deny());

    $this->get(TicketResource::getUrl('create'))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('can render page if the policy create returns true', function (): void {
    app()->bind(TicketPolicy::class . '::create', fn (): bool => true);

    $this->get(TicketResource::getUrl('create'))
        ->assertSuccessful();
});

it('can render page if the policy create returns an allowed response', function (): void {
    app()->bind(TicketPolicy::class . '::create', fn (): Response => Response::allow());

    $this->get(TicketResource::getUrl('create'))
        ->assertSuccessful();

    app()->bind(TicketPolicy::class . '::create', fn (): bool => true);
});

it('does not render page if the policy create returns false', function (): void {
    app()->bind(TicketPolicy::class . '::create', fn (): bool => false);

    $this->get(TicketResource::getUrl('create'))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::create', fn (): bool => true);
});

it('does not render page if the policy create returns a denied response', function (): void {
    app()->bind(TicketPolicy::class . '::create', fn (): Response => Response::deny());

    $this->get(TicketResource::getUrl('create'))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::create', fn (): bool => true);
});

it('re-authorizes create on Livewire updates after the initial mount', function (): void {
    app()->bind(TicketPolicy::class . '::create', fn (): bool => true);

    $component = livewire(CreateTicket::class);

    app()->bind(TicketPolicy::class . '::create', fn (): bool => false);

    $component
        ->set('data.subject', 'foo')
        ->assertStatus(403);

    app()->bind(TicketPolicy::class . '::create', fn (): bool => true);
});

it('re-authorizes viewAny on Livewire updates after the initial mount of a create page', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);

    $component = livewire(CreateTicket::class);

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => false);

    $component
        ->set('data.subject', 'foo')
        ->assertStatus(403);

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});
