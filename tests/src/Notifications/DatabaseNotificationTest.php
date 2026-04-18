<?php

use Filament\Notifications\DatabaseNotification;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Support\Arrayable;

uses(TestCase::class);

it('stores data in constructor', function (): void {
    $notification = new DatabaseNotification(['title' => 'Hello']);

    expect($notification->data)->toBe(['title' => 'Hello']);
});

it('returns `[database]` from `via()`', function (): void {
    $notification = new DatabaseNotification(['title' => 'Test']);

    expect($notification->via(null))->toBe(['database']);
});

it('returns data from `toDatabase()`', function (): void {
    $notification = new DatabaseNotification(['title' => 'Test', 'body' => 'Content']);

    expect($notification->toDatabase(null))->toBe(['title' => 'Test', 'body' => 'Content']);
});

it('returns data from `toArray()`', function (): void {
    $notification = new DatabaseNotification(['key' => 'value']);

    expect($notification->toArray())->toBe(['key' => 'value']);
});

it('implements `ShouldQueue`', function (): void {
    expect(new DatabaseNotification([]))->toBeInstanceOf(ShouldQueue::class);
});

it('implements `Arrayable`', function (): void {
    expect(new DatabaseNotification([]))->toBeInstanceOf(Arrayable::class);
});
