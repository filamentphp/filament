<?php

use Filament\Actions\Action;
use Filament\Notifications\Collection;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Notifications\NotificationMailbox;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Notifications\CustomNotification;
use Filament\Tests\TestCase;
use Illuminate\Notifications\DatabaseNotification as DatabaseNotificationModel;
use Illuminate\Support\Str;
use PHPUnit\Framework\AssertionFailedError;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('construction', function (): void {
    it('can create a notification with `make()` and verify `getId()`', function (): void {
        $notification = Notification::make('custom-id')->title('Test');

        expect($notification->getId())->toBe('custom-id');
    });
});

describe('inline configuration', function (): void {
    it('defaults `isInline()` to `false`', function (): void {
        expect(Notification::make('test')->isInline())->toBeFalse();
    });

    it('can mark a notification as inline using `inline()`', function (): void {
        $notification = Notification::make()->inline();

        expect($notification->isInline())->toBeTrue();
    });

    it('can unset inline using `inline(false)`', function (): void {
        $notification = Notification::make()->inline()->inline(false);

        expect($notification->isInline())->toBeFalse();
    });
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(Notifications::class)
            ->assertSeeHtml('notifications');
    });

    it('can render with a string enum `iconSize()`', function (): void {
        $html = Notification::make()
            ->icon(Heroicon::Check)
            ->iconSize('lg')
            ->toHtml();

        expect($html)->toContain('fi-size-lg');
    });
});

describe('delivery', function (): void {
    it('can send notifications', function (): void {
        Notification::assertNotNotified();

        $notification = Notification::make('saved')
            ->actions([
                Action::make('view')
                    ->label('View record')
                    ->url('/records/1', shouldOpenInNewTab: true)
                    ->close(),
            ])
            ->title('Saved')
            ->body('The record has been saved.')
            ->success()
            ->persistent()
            ->send();

        app(NotificationMailbox::class)->flush();

        $component = livewire(Notifications::class)
            ->dispatch('notificationsSent')
            ->assertCount('notifications', 1);

        expect($component->get('notifications')->first()->toArray())->toBe($notification->toArray());

        livewire(Notifications::class)->dispatch('notificationsSent')->assertCount('notifications', 0);
    });

    it('can close notifications', function (): void {
        ($notification = Notification::make())->send();

        app(NotificationMailbox::class)->flush();

        $component = livewire(Notifications::class);

        $component
            ->dispatch('notificationsSent')
            ->dispatch('notificationClosed', id: $notification->getId());

        expect($component->instance()->notifications)
            ->toBeInstanceOf(Collection::class)
            ->toHaveCount(0);
    });
});

it('does not add toast dismissal behavior to inline notifications', function (): void {
    $html = Notification::make()
        ->inline()
        ->actions([
            Action::make('dismiss')->close(),
        ])
        ->toEmbeddedHtml();

    expect($html)
        ->not->toContain('dismiss-notification')
        ->not->toContain('x-on:keydown.escape')
        ->toContain('x-on:click="close()"');
});

describe('notification assertions', function (): void {
    it('matches the correct notification with `assertNotified()` when multiple notifications are sent', function (): void {
        Notification::make()->title('First')->body('First body')->send();
        Notification::make()->title('Second')->body('Second body')->send();

        // The matching notification is NOT the first one sent.
        Notification::assertNotified(
            Notification::make()->title('Second')->body('Second body'),
        );
    });

    it('fails `assertNotified()` when no sent notification matches the given instance', function (): void {
        Notification::make()->title('First')->body('First body')->send();
        Notification::make()->title('Second')->body('Second body')->send();

        expect(fn () => Notification::assertNotified(
            Notification::make()->title('Third')->body('Third body'),
        ))->toThrow(AssertionFailedError::class);
    });

    it('fails `assertNotNotified()` when a matching notification was sent but was not first', function (): void {
        Notification::make()->title('First')->body('First body')->send();
        Notification::make()->title('Second')->body('Second body')->send();

        // `Second` WAS sent (just not first), so asserting it was not notified must fail.
        expect(fn () => Notification::assertNotNotified(
            Notification::make()->title('Second')->body('Second body'),
        ))->toThrow(AssertionFailedError::class);
    });

    it('passes `assertNotNotified()` when the given instance was genuinely not sent', function (): void {
        Notification::make()->title('First')->body('First body')->send();
        Notification::make()->title('Second')->body('Second body')->send();

        Notification::assertNotNotified(
            Notification::make()->title('Third')->body('Third body'),
        );
    });
});

describe('events', function (): void {
    $getLastNotificationAction = static function (): Action {
        app(NotificationMailbox::class)->flush();

        return livewire(Notifications::class)->dispatch('notificationsSent')->get('notifications')->first()->getActions()[0];
    };

    it('can dispatch an event using `dispatch()`', function () use ($getLastNotificationAction): void {
        $action = Action::make('action')->dispatch('an_event');
        expect($action->getLivewireClickHandler())->toBe("\$dispatch('an_event')");

        $notification = Notification::make()->actions([$action])->send();
        expect($getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatch('an_event')");

        $action = Action::make('action')->dispatch('an_event', ['data']);
        expect($action->getLivewireClickHandler())->toBe("\$dispatch('an_event', JSON.parse('[\\u0022data\\u0022]'))");

        $notification = Notification::make()->actions([$action])->send();
        expect($getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatch('an_event', JSON.parse('[\\u0022data\\u0022]'))");
    });

    it('can dispatch an event to itself using `dispatchSelf()`', function () use ($getLastNotificationAction): void {
        $action = Action::make('action')->dispatchSelf('an_event');
        expect($action->getLivewireClickHandler())->toBe("\$dispatchSelf('an_event')");

        $notification = Notification::make()->actions([$action])->send();
        expect($getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatchSelf('an_event')");

        $action = Action::make('action')->dispatchSelf('an_event', ['data']);
        expect($action->getLivewireClickHandler())->toBe("\$dispatchSelf('an_event', JSON.parse('[\\u0022data\\u0022]'))");

        $notification = Notification::make()->actions([$action])->send();
        expect($getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatchSelf('an_event', JSON.parse('[\\u0022data\\u0022]'))");
    });

    it('can dispatch an event to a component using `dispatchTo()`', function () use ($getLastNotificationAction): void {
        $action = Action::make('action')->dispatchTo('a_component', 'an_event');
        expect($action->getLivewireClickHandler())->toBe("\$dispatchTo('a_component', 'an_event')");

        $notification = Notification::make()->actions([$action])->send();
        expect($getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatchTo('a_component', 'an_event')");

        $action = Action::make('action')->dispatchTo('a_component', 'an_event', ['data']);
        expect($action->getLivewireClickHandler())->toBe("\$dispatchTo('a_component', 'an_event', JSON.parse('[\\u0022data\\u0022]'))");

        $notification = Notification::make()->actions([$action])->send();
        expect($getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatchTo('a_component', 'an_event', JSON.parse('[\\u0022data\\u0022]'))");
    });
});

it('can bind custom notification object', function (): void {
    app()->bind(Notification::class, CustomNotification::class);

    $notification = Notification::make();

    expect($notification)
        ->toBeInstanceOf(CustomNotification::class);
});

it('can resolve custom notification object from data', function (): void {
    app()->bind(Notification::class, CustomNotification::class);

    Notification::make()
        ->size($size = 'lg')
        ->body($body = Str::random())
        ->title($title = Str::random())
        ->send();

    app(NotificationMailbox::class)->flush();

    $component = livewire(Notifications::class);

    $component
        ->dispatch('notificationsSent');

    $notification = $component->instance()->notifications->first();

    expect($notification)
        ->toBeInstanceOf(CustomNotification::class)
        ->getSize()->toBe($size);
});

it('does not apply an unsafe view when restoring via `fromArray()`', function (): void {
    $notification = Notification::make();
    $array = $notification->toArray();

    // A view not in `safeViews` must not be applied
    $restored = Notification::fromArray([...$array, 'view' => 'notifications::not-safe']);

    expect($restored->hasView())->toBeFalse();
});

it('can accumulate safe views across multiple calls to `safeViews()`', function (): void {
    Notification::configureUsing(
        static fn (Notification $notification) => $notification
            ->safeViews('notifications::first')
            ->safeViews(['notifications::second']),
        during: static function (): void {
            foreach (['notifications::first', 'notifications::second'] as $view) {
                expect(Notification::fromArray(['view' => $view])->getView())->toBe($view);
            }

            expect(Notification::fromArray(['view' => 'notifications::not-safe'])->hasView())->toBeFalse();
        },
    );
});

describe('serialization', function (): void {
    it('serializes default and configured scalable icon sizes via `toArray()`', function (): void {
        $defaultSizeNotification = Notification::make()
            ->icon(Heroicon::Bell);
        $smallNotification = Notification::make()
            ->icon(Heroicon::Bell)
            ->iconSize('sm');
        $customSizeNotification = Notification::make()
            ->icon(Heroicon::Bell)
            ->iconSize('custom');

        expect($defaultSizeNotification->toArray())
            ->icon->toBe('heroicon-s-bell')
            ->iconSize->toBeNull()
            ->and($smallNotification->toArray())
            ->icon->toBe('heroicon-c-bell')
            ->iconSize->toBe(IconSize::Small)
            ->and($customSizeNotification->toArray())
            ->icon->toBe('heroicon-s-bell')
            ->iconSize->toBe('custom');
    });

    it('restores notification icon sizes via `fromArray()`', function (): void {
        $notification = Notification::make()
            ->icon(Heroicon::Bell)
            ->iconSize(IconSize::Small);

        $restoredNotification = Notification::fromArray($notification->toArray());

        expect($restoredNotification)
            ->getIcon()->toBe('heroicon-c-bell')
            ->getIconSize()->toBe(IconSize::Small);
    });

    it('restores old notification payloads without an `iconSize` field', function (): void {
        $payload = Notification::make()
            ->icon(Heroicon::Bell)
            ->toArray();

        unset($payload['iconSize']);

        $restoredNotification = Notification::fromArray($payload);

        expect($restoredNotification)
            ->getIcon()->toBe('heroicon-s-bell')
            ->getIconSize()->toBeNull();
    });

    it('preserves a lazy default icon size when restoring old notification payloads', function (): void {
        $restoredNotification = NotificationWithLazyIconSize::fromArray([
            'title' => 'Important',
        ]);

        expect($restoredNotification->getIconSize())->toBe(IconSize::Large);
    });

    it('preserves notification icon sizes through delivery', function (): void {
        Notification::make()
            ->icon(Heroicon::Bell)
            ->iconSize(IconSize::Small)
            ->send();

        app(NotificationMailbox::class)->flush();

        $notificationsComponent = livewire(Notifications::class)->dispatch('notificationsSent');
        $restoredNotification = $notificationsComponent->instance()->notifications->first();

        expect($restoredNotification)
            ->getIcon()->toBe('heroicon-c-bell')
            ->getIconSize()->toBe(IconSize::Small);
    });

    it('preserves notification icon sizes through broadcast messages', function (): void {
        $broadcastNotification = Notification::make()
            ->icon(Heroicon::Bell)
            ->iconSize(IconSize::Small)
            ->toBroadcast();
        $payload = json_decode(
            json_encode($broadcastNotification->toBroadcast(null)->data, JSON_THROW_ON_ERROR),
            associative: true,
            flags: JSON_THROW_ON_ERROR,
        );

        $notificationsComponent = livewire(Notifications::class)
            ->call('handleBroadcastNotification', $payload);
        $restoredNotification = $notificationsComponent->instance()->notifications->first();

        expect($restoredNotification)
            ->getIcon()->toBe('heroicon-c-bell')
            ->getIconSize()->toBe(IconSize::Small);
    });

    it('preserves notification icon sizes through database messages', function (): void {
        $payload = Notification::make()
            ->icon(Heroicon::Bell)
            ->iconSize(IconSize::Small)
            ->toDatabase()
            ->toDatabase(null);
        $databaseNotification = new DatabaseNotificationModel;
        $databaseNotification->setRawAttributes([
            'id' => 'notification-id',
            'data' => json_encode($payload, JSON_THROW_ON_ERROR),
        ]);

        $restoredNotification = Notification::fromDatabase($databaseNotification);

        expect($restoredNotification)
            ->getId()->toBe('notification-id')
            ->getIcon()->toBe('heroicon-c-bell')
            ->getIconSize()->toBe(IconSize::Small);
    });

    it('serializes simple notification via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->body('Test Body')
            ->icon('heroicon-o-bell')
            ->iconColor('success')
            ->color('primary')
            ->duration(5000);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with string icon via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->icon('heroicon-o-check-circle');

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with `Heroicon` enum via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->icon(Heroicon::CheckCircle);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with outlined `Heroicon` enum via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->icon(Heroicon::OutlinedCheckCircle);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with action containing `Heroicon` enum via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::Eye)
                    ->color('primary'),
            ]);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with action containing outlined `Heroicon` enum via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('primary'),
            ]);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with action containing `Heroicon` enum and icon size via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::Eye)
                    ->iconSize(IconSize::Small),
            ]);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with multiple actions via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->body('Test Body')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::Eye)
                    ->url('/view'),
                Action::make('edit')
                    ->label('Edit')
                    ->icon(Heroicon::Pencil)
                    ->url('/edit'),
                Action::make('delete')
                    ->label('Delete')
                    ->icon(Heroicon::Trash)
                    ->color('danger'),
            ]);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification via `getDatabaseMessage()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->body('Test Body')
            ->icon(Heroicon::Bell)
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::Eye),
            ]);

        expect($notification->getDatabaseMessage())
            ->toMatchSnapshot();
    });

    it('roundtrips notification via `toArray()` and `fromArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->body('Test Body')
            ->icon('heroicon-o-bell')
            ->iconColor('success')
            ->color('primary')
            ->duration(5000);

        $array = $notification->toArray();
        $restored = Notification::fromArray($array);
        $restoredArray = $restored->toArray();

        expect($restoredArray)->toBe($array);
    });

    it('roundtrips notification with actions via `toArray()` and `fromArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->body('Test Body')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url('/view'),
                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ]);

        $array = $notification->toArray();
        $restored = Notification::fromArray($array);
        $restoredArray = $restored->toArray();

        expect($restoredArray)->toBe($array);
    });

    it('roundtrips notification with `Heroicon` enum via `toArray()` and `fromArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->icon(Heroicon::Bell);

        $array = $notification->toArray();
        $restored = Notification::fromArray($array);
        $restoredArray = $restored->toArray();

        expect($restoredArray)->toBe($array);
    });

    it('roundtrips notification with action containing `Heroicon` enum via `toArray()` and `fromArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::Eye),
            ]);

        $array = $notification->toArray();
        $restored = Notification::fromArray($array);
        $restoredArray = $restored->toArray();

        expect($restoredArray)->toBe($array);
    });

    it('roundtrips notification via `getDatabaseMessage()` and `fromArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->body('Test Body')
            ->icon(Heroicon::Bell)
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::Eye),
            ]);

        $databaseMessage = $notification->getDatabaseMessage();
        $restored = Notification::fromArray($databaseMessage);
        $restoredMessage = $restored->getDatabaseMessage();

        expect($restoredMessage)->toBe($databaseMessage);
    });
});

it('returns fluent `$this` from `send()`', function (): void {
    $notification = Notification::make('test')
        ->title('Test');

    $result = $notification->send();

    expect($result)->toBe($notification);
});

class NotificationWithLazyIconSize extends Notification
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->iconSize(fn (): IconSize => $this->getTitle() === 'Important' ? IconSize::Large : IconSize::Small);
    }
}
