<?php

use Filament\Support\Enums\ModelEvent;
use Filament\Support\Events\ModelChangedBroadcast;
use Filament\Support\Facades\FilamentRealtime;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

uses(TestCase::class);

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
