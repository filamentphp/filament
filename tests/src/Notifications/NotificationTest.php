<?php

use BladeUI\Icons\Factory;
use BladeUI\Icons\IconsManifest;
use Filament\Livewire\Notifications as PanelNotifications;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Actions\ActionGroup;
use Filament\Notifications\Collection;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconPosition;
use Filament\Tests\Notifications\Fixtures\CustomNotification;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function () {
    livewire(Notifications::class)
        ->assertSeeHtml('notifications');
});

it('can send notifications', function () {
    $iconSets = app(Factory::class)->all();
    $icons = app(IconsManifest::class)->getManifest($iconSets);

    $getRandomColor = function (): string {
        return Arr::random([
            'danger',
            'gray',
            'info',
            'primary',
            'success',
            'warning',
        ]);
    };

    $getRandomIcon = function () use ($icons): string {
        return 'heroicon-' . collect($icons)->flatten()->random();
    };

    expect(session()->get('filament.notifications'))
        ->toBeEmpty();

    Notification::make($id = Str::random())
        ->actions([
            Action::make($actionName = Str::random())
                ->close($shouldClose = (bool) rand(0, 1))
                ->color($actionColor = $getRandomColor())
                ->disabled($isActionDisabled = (bool) rand(0, 1))
                ->dispatch($actionEvent = Str::random(), $actionEventData = [Str::random()])
                ->extraAttributes($actionExtraAttributes = ['x' . Str::random(15) => Str::random()]) // Attributes must start with a letter
                ->icon($actionIcon = $getRandomIcon())
                ->iconPosition($actionIconPosition = Arr::random([IconPosition::After, IconPosition::Before]))
                ->label($actionLabel = Str::random())
                ->outlined($isActionOutlined = (bool) rand(0, 1))
                ->size($actionSize = Arr::random([ActionSize::ExtraSmall, ActionSize::Small, ActionSize::Medium, ActionSize::Large, ActionSize::ExtraLarge]))
                ->url(
                    $actionUrl = Str::random(),
                    shouldOpenInNewTab: $shouldActionOpenUrlInNewTab = (bool) rand(0, 1),
                ),
        ])
        ->body($body = Str::random())
        ->duration($duration = rand(1, 10))
        ->icon($icon = $getRandomIcon())
        ->iconColor($iconColor = $getRandomColor())
        ->title($title = Str::random())
        ->send();

    $notifications = session()->get('filament.notifications');

    expect($notifications)
        ->toBeArray()
        ->toHaveCount(1);

    $notification = Arr::last($notifications);

    expect($notification)
        ->toBeArray()
        ->id->toBe($id)
        ->body->toBe($body)
        ->duration->toBe($duration)
        ->icon->toBe($icon)
        ->iconColor->toBe($iconColor)
        ->title->toBe($title);

    $actions = $notification['actions'];

    expect($actions)
        ->toBeArray();

    $action = Arr::first($actions);

    expect($action)
        ->toBeArray()
        ->name->toBe($actionName)
        ->color->toBe($actionColor)
        ->event->toBe($actionEvent)
        ->eventData->toBe($actionEventData)
        ->extraAttributes->toBe($actionExtraAttributes)
        ->icon->toBe($actionIcon)
        ->iconPosition->toBe($actionIconPosition)
        ->isOutlined->toBe($isActionOutlined)
        ->isDisabled->toBe($isActionDisabled)
        ->label->toBe($actionLabel)
        ->shouldClose->toBe($shouldClose)
        ->shouldOpenUrlInNewTab->toBe($shouldActionOpenUrlInNewTab)
        ->size->toBe($actionSize)
        ->url->toBe($actionUrl);

    $component = livewire(Notifications::class);

    $component
        ->dispatch('notificationsSent');

    expect($component->instance()->notifications)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(1);

    $notification = $component->instance()->notifications->first();

    expect($notification)
        ->toBeInstanceOf(Notification::class)
        ->getId()->toBe($id)
        ->getBody()->toBe($body)
        ->getDuration()->toBe($duration)
        ->getIcon()->toBe($icon)
        ->getIconColor()->toBe($iconColor)
        ->getTitle()->toBe($title);

    $actions = $notification->getActions();

    expect($actions)
        ->toBeArray();

    $action = Arr::first($actions);

    expect($action)
        ->toBeInstanceOf(Action::class)
        ->getName()->toBe($actionName)
        ->getColor()->toBe($actionColor)
        ->getEvent()->toBe($actionEvent)
        ->getEventData()->toBe($actionEventData)
        ->getExtraAttributes()->toBe($actionExtraAttributes)
        ->getIcon()->toBe($actionIcon)
        ->getIconPosition()->toBe($actionIconPosition)
        ->isOutlined()->toBe($isActionOutlined)
        ->isDisabled()->toBe($isActionDisabled)
        ->getLabel()->toBe($actionLabel)
        ->shouldClose()->toBe($shouldClose)
        ->shouldOpenUrlInNewTab()->toBe($shouldActionOpenUrlInNewTab)
        ->getSize()->toBe($actionSize)
        ->getUrl()->toBe($actionUrl);

    expect(session()->get('filament.notifications'))
        ->toBeEmpty();
});

it('can pull notifications through the inherited panel component', function (): void {
    livewire(PanelNotifications::class)
        ->call('pullNotificationsFromSession')
        ->assertHasNoErrors();
});

it('keeps the newest 50 session notifications', function (): void {
    foreach (range(1, 51) as $index) {
        Notification::make("notification-{$index}")->send();
    }

    $sessionNotifications = session()->get('filament.notifications');

    expect($sessionNotifications)
        ->toHaveCount(50)
        ->and($sessionNotifications[0]['id'])->toBe('notification-2')
        ->and($sessionNotifications[49]['id'])->toBe('notification-51');

    $notifications = livewire(Notifications::class)->get('notifications');

    expect($notifications)
        ->toHaveCount(50)
        ->and($notifications->keys()->first())->toBe('notification-2')
        ->and($notifications->keys()->last())->toBe('notification-51');
});

it('bounds notification restoration across repeated calls in one request', function (): void {
    $component = livewire(Notifications::class);

    $this->postJson(Livewire::getUpdateUri(), [
        'components' => [[
            'snapshot' => json_encode($component->snapshot),
            'updates' => [],
            'calls' => array_map(
                static fn (int $index): array => [
                    'method' => 'pushNotificationFromEvent',
                    'params' => [Notification::make("notification-{$index}")->toArray()],
                ],
                range(1, 101),
            ),
        ]],
    ], ['X-Livewire' => 'true'])->assertStatus(419);
});

it('shares notification restoration limits across components in one request', function (): void {
    $firstComponent = livewire(Notifications::class);
    $secondComponent = livewire(Notifications::class);
    $firstPayload = Notification::make('first')
        ->actions(array_map(
            static fn (int $index): Action => Action::make("first-action-{$index}"),
            range(1, 59),
        ))
        ->toArray();
    $secondPayload = Notification::make('second')
        ->actions(array_map(
            static fn (int $index): Action => Action::make("second-action-{$index}"),
            range(1, 40),
        ))
        ->toArray();

    $this->postJson(Livewire::getUpdateUri(), [
        'components' => [
            [
                'snapshot' => json_encode($firstComponent->snapshot),
                'updates' => [],
                'calls' => [[
                    'method' => 'pushNotificationFromEvent',
                    'params' => [$firstPayload],
                ]],
            ],
            [
                'snapshot' => json_encode($secondComponent->snapshot),
                'updates' => [],
                'calls' => [[
                    'method' => 'pushNotificationFromEvent',
                    'params' => [$secondPayload],
                ]],
            ],
        ],
    ], ['X-Livewire' => 'true'])->assertStatus(419);
});

it('applies notification restoration limits to event and broadcast delivery', function (string $method): void {
    $component = livewire(Notifications::class);
    $payload = Notification::make('over-limit')
        ->actions(array_map(
            static fn (int $index): Action => Action::make("action-{$index}"),
            range(1, 100),
        ))
        ->toArray();

    if ($method === 'handleBroadcastNotification') {
        $payload['format'] = 'filament';
    }

    $this->postJson(Livewire::getUpdateUri(), [
        'components' => [[
            'snapshot' => json_encode($component->snapshot),
            'updates' => [],
            'calls' => [[
                'method' => $method,
                'params' => [$payload],
            ]],
        ]],
    ], ['X-Livewire' => 'true'])->assertStatus(419);
})->with(['pushNotificationFromEvent', 'handleBroadcastNotification']);

it('keeps trusted session delivery within the next signed snapshot hydration limit', function (): void {
    foreach (['first', 'second'] as $id) {
        Notification::make($id)
            ->actions(array_map(
                static fn (int $index): Action => Action::make("{$id}-action-{$index}"),
                range(1, 59),
            ))
            ->send();
    }

    $component = livewire(Notifications::class);

    expect($component->get('notifications')->keys()->all())->toBe(['second']);

    $this->postJson(Livewire::getUpdateUri(), [
        'components' => [[
            'snapshot' => json_encode($component->snapshot),
            'updates' => [],
            'calls' => [],
        ]],
    ], ['X-Livewire' => 'true'])->assertOk();
});

it('can replace and close notifications by ID after budget eviction', function (array $ids): void {
    $component = livewire(Notifications::class);

    foreach ($ids as $id) {
        Notification::make($id)
            ->title("Original {$id}")
            ->persistent()
            ->actions(array_map(
                static fn (int $index): Action => Action::make("action-{$index}")->url('/jobs'),
                range(1, 39),
            ))
            ->send();
    }

    $component->dispatch('notificationsSent');

    expect($component->instance()->notifications->map->getId()->values()->all())
        ->toBe([$ids[1], $ids[2]]);

    Notification::make($ids[1])->title('Replacement')->persistent()->send();
    $component->dispatch('notificationsSent');

    expect($component->instance()->notifications->map->getId()->values()->all())
        ->toBe([$ids[2], $ids[1]]);

    $component
        ->assertSee('Replacement')
        ->assertDontSee("Original {$ids[1]}")
        ->dispatch('notificationClosed', id: $ids[2]);

    expect($component->instance()->notifications->map->getId()->values()->all())
        ->toBe([$ids[1]]);

    $component->dispatch('notificationClosed', id: $ids[1]);

    expect($component->instance()->notifications)->toBeEmpty();
})->with([
    'numeric IDs' => [['100', '200', '300']],
    'zero oldest ID' => [['0', '-1', '200']],
    'negative oldest ID' => [['-1', '0', '200']],
    'mixed IDs' => [['oldest', '200', 'newest']],
    'UUID IDs' => [[
        'ed5ec08d-136e-4ea6-8e8d-51eb9d3c5a01',
        'ed5ec08d-136e-4ea6-8e8d-51eb9d3c5a02',
        'ed5ec08d-136e-4ea6-8e8d-51eb9d3c5a03',
    ]],
]);

it('discards individually oversized trusted notifications without invalidating the signed snapshot', function (): void {
    Notification::make('too-many-actions')
        ->actions(array_map(
            static fn (int $index): Action => Action::make("action-{$index}"),
            range(1, 100),
        ))
        ->send();

    $nestedActions = [Action::make('nested-leaf')];

    foreach (range(1, 11) as $depth) {
        $nestedActions = [ActionGroup::make($nestedActions)];
    }

    Notification::make('too-deep')
        ->actions($nestedActions)
        ->send();

    Notification::make('valid')->send();

    $component = livewire(Notifications::class);

    expect($component->get('notifications')->keys()->all())->toBe(['valid']);

    $this->postJson(Livewire::getUpdateUri(), [
        'components' => [[
            'snapshot' => json_encode($component->snapshot),
            'updates' => [],
            'calls' => [],
        ]],
    ], ['X-Livewire' => 'true'])->assertOk();
});

it('can close notifications', function () {
    ($notification = Notification::make())->send();

    $component = livewire(Notifications::class);

    $component
        ->dispatch('notificationsSent')
        ->dispatch('notificationClosed', id: $notification->getId());

    expect($component->instance()->notifications)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(0);
});

function getLastNotificationAction()
{
    $notificationsLivewireComponent = new Notifications;
    $notificationsLivewireComponent->mount();
    $notifications = $notificationsLivewireComponent->notifications;

    return $notifications->first()->getActions()[0];
}

it('can dispatch an event', function () {
    $action = Action::make('action')->dispatch('an_event');
    expect($action->getLivewireClickHandler())->toBe("\$dispatch('an_event')");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatch('an_event')");

    $action = Action::make('action')->dispatch('an_event', ['data']);
    expect($action->getLivewireClickHandler())->toBe("\$dispatch('an_event', JSON.parse('[\\u0022data\\u0022]'))");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatch('an_event', JSON.parse('[\\u0022data\\u0022]'))");
});

it('can dispatch an event to itself', function () {
    $action = Action::make('action')->dispatchSelf('an_event');
    expect($action->getLivewireClickHandler())->toBe("\$dispatchSelf('an_event')");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatchSelf('an_event')");

    $action = Action::make('action')->dispatchSelf('an_event', ['data']);
    expect($action->getLivewireClickHandler())->toBe("\$dispatchSelf('an_event', JSON.parse('[\\u0022data\\u0022]'))");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatchSelf('an_event', JSON.parse('[\\u0022data\\u0022]'))");
});

it('can dispatch an event to a component', function () {
    $action = Action::make('action')->dispatchTo('a_component', 'an_event');
    expect($action->getLivewireClickHandler())->toBe("\$dispatchTo('a_component', 'an_event')");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatchTo('a_component', 'an_event')");

    $action = Action::make('action')->dispatchTo('a_component', 'an_event', ['data']);
    expect($action->getLivewireClickHandler())->toBe("\$dispatchTo('a_component', 'an_event', JSON.parse('[\\u0022data\\u0022]'))");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatchTo('a_component', 'an_event', JSON.parse('[\\u0022data\\u0022]'))");
});

it('can bind custom notification object', function () {
    app()->bind(Notification::class, CustomNotification::class);

    $notification = Notification::make();

    expect($notification)
        ->toBeInstanceOf(CustomNotification::class);
});

it('can resolve custom notification object from data', function () {
    app()->bind(Notification::class, CustomNotification::class);

    Notification::make()
        ->size($size = 'lg')
        ->body($body = Str::random())
        ->title($title = Str::random())
        ->send();

    $notifications = session()->get('filament.notifications');

    expect($notifications)
        ->toBeArray()
        ->toHaveCount(1);

    $notification = Arr::last($notifications);

    $component = livewire(Notifications::class);

    $component
        ->dispatch('notificationsSent');

    $notification = $component->instance()->notifications->first();

    expect($notification)
        ->toBeInstanceOf(CustomNotification::class)
        ->getSize()->toBe($size);
});
