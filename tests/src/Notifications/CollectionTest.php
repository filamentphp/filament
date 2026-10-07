<?php

use Filament\Actions\Action;
use Filament\Notifications\Collection;
use Filament\Notifications\Notification;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can serialize to Livewire format via `toLivewire()`', function (): void {
    $collection = new Collection;

    $notification = Notification::make('test-id')
        ->title('Test Title')
        ->body('Test Body');

    $collection->push($notification->toArray());

    expect($collection->toLivewire())
        ->toBeArray()
        ->toHaveCount(1)
        ->sequence(
            fn ($item) => $item->toBeArray()->id->toBe('test-id'),
        );
});

it('can restore from Livewire format via `fromLivewire()`', function (): void {
    $notification = Notification::make('test-id')
        ->title('Test Title')
        ->body('Test Body');

    $data = [$notification->toArray()];

    $collection = Collection::fromLivewire($data);

    expect($collection)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(1);

    expect($collection->first())
        ->toBeInstanceOf(Notification::class)
        ->getId()->toBe('test-id')
        ->getTitle()->toBe('Test Title')
        ->getBody()->toBe('Test Body');
});

it('roundtrips through `toLivewire()` and `fromLivewire()`', function (): void {
    $notification = Notification::make('roundtrip-id')
        ->title('Roundtrip')
        ->body('Body text');

    $original = new Collection([$notification->toArray()]);
    $wire = $original->toLivewire();
    $restored = Collection::fromLivewire($wire);

    expect($restored)
        ->toHaveCount(1);

    expect($restored->first())
        ->toBeInstanceOf(Notification::class)
        ->getId()->toBe('roundtrip-id')
        ->getTitle()->toBe('Roundtrip');
});

it('produces an empty array via `toLivewire()` when empty', function (): void {
    $collection = new Collection;

    expect($collection->toLivewire())
        ->toBeArray()
        ->toBeEmpty();
});

it('can roundtrip multiple notifications', function (): void {
    $notifications = [
        Notification::make('first')->title('First')->toArray(),
        Notification::make('second')->title('Second')->toArray(),
        Notification::make('third')->title('Third')->toArray(),
    ];

    $collection = new Collection($notifications);
    $restored = Collection::fromLivewire($collection->toLivewire());

    expect($restored)->toHaveCount(3);
    expect($restored[0]->getId())->toBe('first');
    expect($restored[1]->getId())->toBe('second');
    expect($restored[2]->getId())->toBe('third');
});

it('produces an empty `Collection` via `fromLivewire()` when given an empty array', function (): void {
    $collection = Collection::fromLivewire([]);

    expect($collection)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(0);
});

it('produces an empty `Collection` via `fromLivewire()` when given a value that is not an array', function (): void {
    expect(Collection::fromLivewire(5))
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(0);

    expect(Collection::fromLivewire('not-a-list'))
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(0);
});

it('skips items that are not arrays via `fromLivewire()` and keeps the rest in order', function (): void {
    $collection = Collection::fromLivewire([
        Notification::make('first')->title('First')->toArray(),
        5,
        'not-a-notification',
        null,
        Notification::make('second')->title('Second')->toArray(),
    ]);

    expect($collection)
        ->toHaveCount(2)
        ->sequence(
            fn ($item) => $item->toBeInstanceOf(Notification::class)->getId()->toBe('first'),
            fn ($item) => $item->toBeInstanceOf(Notification::class)->getId()->toBe('second'),
        );
});

it('skips actions that are not arrays when restoring a notification via `fromLivewire()`', function (): void {
    $data = Notification::make('with-actions')
        ->title('With actions')
        ->actions([
            Action::make('view'),
        ])
        ->toArray();

    $data['actions'][] = 5;
    $data['actions'][] = 'not-an-action';

    $collection = Collection::fromLivewire([$data]);

    expect($collection)->toHaveCount(1);

    expect($collection->first()->getActions())
        ->toHaveCount(1)
        ->sequence(
            fn ($action) => $action->toBeInstanceOf(Action::class)->getName()->toBe('view'),
        );
});
