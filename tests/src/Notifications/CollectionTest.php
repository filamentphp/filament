<?php

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Collection;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Exceptions;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;

use function Filament\Tests\livewire;

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

it('rejects non-array values via `fromLivewire()` with HTTP 419', function (mixed $value): void {
    expect(fn () => Collection::fromLivewire($value))
        ->toThrow(fn (HttpException $exception) => expect($exception->getStatusCode())->toBe(419));
})->with([5, 'not-a-list', null, false]);

it('rejects malformed Livewire collection updates without reporting or calling component methods', function (array $notifications, bool $isDebug): void {
    config(['app.debug' => $isDebug]);

    $component = livewire(Notifications::class);
    Exceptions::fake();
    Notification::make('queued')->title('Queued')->send();

    $this->postJson(Livewire::getUpdateUri(), [
        'components' => [[
            'snapshot' => json_encode($component->snapshot),
            'updates' => ['notifications' => $notifications],
            'calls' => [['method' => 'pullNotificationsFromSession', 'params' => []]],
        ]],
    ], ['X-Livewire' => 'true'])->assertStatus(419);

    expect(session()->get('filament.notifications'))->toHaveCount(1);
    Exceptions::assertNothingReported();
})->with([
    'scalar notification among valid notifications' => fn (): array => [
        'first' => Notification::make('first')->toArray(),
        'invalid' => 1,
        'second' => Notification::make('second')->toArray(),
    ],
    'null notification' => [[null]],
    'scalar actions container' => [[['actions' => 1]]],
    'scalar action among valid actions' => fn (): array => [['actions' => [Action::make('view')->toArray(), 1]]],
    'nested scalar action' => [[['actions' => [['actions' => [['actions' => [1]]]]]]]],
    'scalar grouped actions container' => [[['actions' => [['actions' => 1]]]]],
    'missing action name' => [[['actions' => [[]]]]],
    'non-string action name' => [[['actions' => [['name' => []]]]]],
])->with([false, true]);

it('preserves notification and action keys and nested groups via `fromLivewire()`', function (): void {
    $notification = Notification::make('with-actions')
        ->actions([
            'view-key' => Action::make('view'),
            'group-key' => ActionGroup::make([
                ActionGroup::make([Action::make('edit')]),
            ]),
            'delete-key' => Action::make('delete'),
        ]);

    $data = [
        'with-actions' => $notification->toArray(),
        'without-actions' => ['id' => 'without-actions'],
        'null-actions' => ['id' => 'null-actions', 'actions' => null],
    ];

    $collection = Collection::fromLivewire($data);

    expect($collection->keys()->all())->toBe(['with-actions', 'without-actions', 'null-actions']);
    expect(array_keys($collection['with-actions']->getActions()))->toBe(['view-key', 'group-key', 'delete-key']);
    expect($collection['with-actions']->toArray())->toBe($notification->toArray());
    expect($collection['without-actions']->getActions())->toBe([]);
    expect($collection['null-actions']->getActions())->toBe([]);

    $component = livewire(Notifications::class)->set('notifications', $data)->assertStatus(200);
    $component->call('removeNotification', 'with-actions')->assertStatus(200);

    expect($component->instance()->notifications->keys()->all())->toBe(['without-actions', 'null-actions']);
});

it('does not suppress unrelated deserialization `TypeError`s via `fromLivewire()`', function (): void {
    expect(fn () => Collection::fromLivewire([['title' => []]]))->toThrow(TypeError::class);
});

it('does not filter malformed actions in shared `Notification::fromArray()` deserialization', function (): void {
    expect(fn () => Notification::fromArray(['actions' => [1]]))->toThrow(TypeError::class);
});
