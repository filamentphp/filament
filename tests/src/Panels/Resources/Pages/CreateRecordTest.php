<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\Fixtures\Resources\Posts\Pages\CreateAnotherPreservingDataPost;
use Filament\Tests\Fixtures\Resources\Posts\Pages\CreatePost;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\TicketMessages\TicketMessageResource;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Auth\Access\Response;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function (): void {
    $this->get(PostResource::getUrl('create'))
        ->assertSuccessful();
});

it('can create', function (): void {
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

    $this->assertDatabaseHas(Post::class, [
        'author_id' => $newData->author->getKey(),
        'content' => $newData->content,
        'tags' => json_encode($newData->tags),
        'title' => $newData->title,
        'rating' => $newData->rating,
    ]);
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
        ->assertFormSet([
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

    $this->assertDatabaseHas(Post::class, [
        'author_id' => $newData->author->getKey(),
        'content' => $newData->content,
        'tags' => json_encode($newData->tags),
        'title' => $newData->title,
        'rating' => $newData->rating,
    ]);

    $this->assertDatabaseHas(Post::class, [
        'author_id' => $newData2->author->getKey(),
        'content' => $newData2->content,
        'tags' => json_encode($newData2->tags),
        'title' => $newData2->title,
        'rating' => $newData2->rating,
    ]);
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
        ->assertFormSet([
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

    $this->assertDatabaseHas(Post::class, [
        'author_id' => $newData->author->getKey(),
        'content' => $newData->content,
        'tags' => json_encode($newData->tags),
        'title' => $newData->title,
        'rating' => $newData->rating,
    ]);

    $this->assertDatabaseHas(Post::class, [
        'author_id' => $newData2->author->getKey(),
        'content' => $newData2->content,
        'tags' => json_encode($newData->tags),
        'title' => $newData2->title,
        'rating' => $newData->rating,
    ]);
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
