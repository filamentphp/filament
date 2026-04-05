<?php

use Filament\Notifications\BroadcastNotification;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

uses(TestCase::class);

it('stores data in constructor', function (): void {
    $notification = new BroadcastNotification(['title' => 'Hello', 'body' => 'World']);

    expect($notification->data)->toBe(['title' => 'Hello', 'body' => 'World']);
});

it('returns `[broadcast]` from `via()`', function (): void {
    $notification = new BroadcastNotification(['title' => 'Test']);

    expect($notification->via(null))->toBe(['broadcast']);
});

it('returns `BroadcastMessage` from `toBroadcast()`', function (): void {
    $notification = new BroadcastNotification(['title' => 'Test', 'body' => 'Content']);

    $message = $notification->toBroadcast(null);

    expect($message)->toBeInstanceOf(BroadcastMessage::class);
    expect($message->data)->toBe(['title' => 'Test', 'body' => 'Content']);
});

it('implements `ShouldQueue`', function (): void {
    $notification = new BroadcastNotification([]);

    expect($notification)->toBeInstanceOf(ShouldQueue::class);
});
