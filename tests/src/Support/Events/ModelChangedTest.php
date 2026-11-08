<?php

use Filament\Support\Enums\ModelEvent;
use Filament\Support\Events\ModelChanged;
use Filament\Support\Events\ModelChangedBroadcast;
use Filament\Support\Facades\FilamentRealtime;
use Filament\Support\ModelSubscription;
use Filament\Tests\Fixtures\Enums\ObservablePostEvent;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

uses(TestCase::class);

beforeEach(function (): void {
    $this->events = [];

    Event::listen(ModelChanged::class, function (ModelChanged $event): void {
        $this->events[] = $event;
    });
});

it('waits for the outer transaction to commit and preserves each change snapshot', function (): void {
    $post = Post::factory()->create();
    FilamentRealtime::subscribe(ModelSubscription::make(Post::class)->onlyEvents([ModelEvent::Updated]));

    DB::transaction(function () use ($post): void {
        $post->update(['title' => 'First']);

        DB::transaction(function () use ($post): void {
            $post->update(['title' => 'Second']);
        });

        $post->title = 'Unsaved';

        expect($this->events)->toBe([]);
    });

    expect(array_map(static fn (ModelChanged $event): mixed => $event->getRecord()->getAttribute('title'), $this->events))
        ->toEqualCanonicalizing(['First', 'Second'])
        ->and($post->fresh()->title)->toBe('Second');
});

it('discards automatic and manual events when a transaction rolls back', function (): void {
    $post = Post::factory()->create(['title' => 'Original']);
    FilamentRealtime::subscribe(ModelSubscription::make(Post::class)->onlyEvents([ModelEvent::Updated]));

    DB::beginTransaction();
    $post->update(['title' => 'Rolled back']);
    ModelChanged::make($post)->dispatch();
    DB::rollBack();

    DB::transaction(function () use ($post): void {
        $post->refresh()->update(['title' => 'Committed']);
    });

    expect($this->events)->toHaveCount(1)
        ->and($this->events[0]->getRecord()->getAttribute('title'))->toBe('Committed')
        ->and($post->fresh()->title)->toBe('Committed');
});

it('can dispatch manual changes without a model subscription', function (): void {
    $post = Post::factory()->create();

    Event::fake([ModelChanged::class]);

    ModelChanged::make($post)->dispatch();
    ModelChanged::make($post)->event(ModelEvent::Updated)->dispatch();
    ModelChanged::make($post)->event(ObservablePostEvent::Published)->dispatch();
    ModelChanged::make($post)->event('updated')->dispatch();
    ModelChanged::make($post)->event('inventory-adjusted')->dispatch();

    Event::assertDispatchedTimes(ModelChanged::class, 5);
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getEvent() === null);
    expect(Event::dispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getEvent() === ModelEvent::Updated))
        ->toHaveCount(2);
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => ($event->getEvent() === ObservablePostEvent::Published)
        && $event->getRecord()->is($post));
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getEvent() === 'inventory-adjusted');
});

it('broadcasts private invalidations after commit without exposing model attributes', function (): void {
    $post = Post::factory()->create(['title' => 'Sensitive record title']);
    FilamentRealtime::subscribe(Post::class)->onlyEvents([ModelEvent::Updated]);
    Event::fake([ModelChangedBroadcast::class]);

    $post->update(['title' => 'Not broadcast without Echo']);
    Event::assertNotDispatched(ModelChangedBroadcast::class);

    config(['filament.broadcasting.echo' => ['broadcaster' => 'pusher']]);

    DB::beginTransaction();
    $post->update(['title' => 'Rolled back']);
    Event::assertNotDispatched(ModelChangedBroadcast::class);
    DB::rollBack();

    DB::transaction(function () use ($post): void {
        $post->update(['title' => 'Committed private title']);
        Event::assertNotDispatched(ModelChangedBroadcast::class);
    });

    Event::assertDispatchedTimes(ModelChangedBroadcast::class, 1);
    Event::assertDispatched(ModelChangedBroadcast::class, static fn (ModelChangedBroadcast $event): bool => ((string) $event->broadcastOn() === 'private-' . ModelChangedBroadcast::getChannel($post))
        && ($event->broadcastWith() === [])
        && (! str_contains(serialize($event), 'Committed private title')));

    FilamentRealtime::subscribe(Post::class)->onlyEvents(['retrieved']);
    Post::findOrFail($post->getKey());

    Event::assertDispatchedTimes(ModelChangedBroadcast::class, 1);
});
