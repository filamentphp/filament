<?php

use BladeUI\Icons\Factory;
use BladeUI\Icons\IconsManifest;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Collection;
use Filament\Notifications\Http\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can render', function () {
    livewire(Notifications::class)
        ->assertSuccessful();
});

it('can send notifications', function () {
    $iconSets = app(Factory::class)->all();
    $icons = app(IconsManifest::class)->getManifest($iconSets);

    $getRandomColor = function (): string {
        return Arr::random(['danger', 'primary', 'secondary', 'success', 'warning']);
    };

    $getRandomIcon = function () use ($icons): string {
        return 'heroicon-' . collect($icons)->flatten()->random();
    };

    expect(session()->get('filament.notifications'))
        ->toBeEmpty();

    Notification::make($id = Str::random())
        ->actions([
            Action::make($actionName = Str::random())
                ->close($shouldActionCloseNotification = (bool) rand(0, 1))
                ->color($actionColor = $getRandomColor())
                ->disabled($isActionDisabled = (bool) rand(0, 1))
                ->emit($actionEvent = Str::random(), $actionEventData = [Str::random()])
                ->extraAttributes($actionExtraAttributes = ['x' . Str::random(15) => Str::random()]) // Attributes must start with a letter
                ->icon($actionIcon = $getRandomIcon())
                ->iconPosition($actionIconPosition = Arr::random(['after', 'before']))
                ->label($actionLabel = Str::random())
                ->outlined($isActionOutlined = (bool) rand(0, 1))
                ->size($actionSize = Arr::random(['sm', 'md', 'lg']))
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
        ->shouldCloseNotification->toBe($shouldActionCloseNotification)
        ->shouldOpenUrlInNewTab->toBe($shouldActionOpenUrlInNewTab)
        ->size->toBe($actionSize)
        ->url->toBe($actionUrl);

    $component = livewire(Notifications::class);

    $component
        ->emit('notificationsSent');

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
        ->shouldCloseNotification()->toBe($shouldActionCloseNotification)
        ->shouldOpenUrlInNewTab()->toBe($shouldActionOpenUrlInNewTab)
        ->getSize()->toBe($actionSize)
        ->getUrl()->toBe($actionUrl);

    expect(session()->get('filament.notifications'))
        ->toBeEmpty();
});

it('can close notifications', function () {
    ($notification = Notification::make())->send();

    $component = livewire(Notifications::class);

    $component
        ->emit('notificationsSent')
        ->emit('notificationClosed', $notification->getId());

    expect($component->instance()->notifications)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(0);
});

function getLastNotificationAction()
{
    $notificationsLivewireComponent = new Notifications();
    $notificationsLivewireComponent->mount();
    $notifications = $notificationsLivewireComponent->notifications;

    return $notifications->first()->getActions()[0];
}

it('can emit an event', function () {
    $action = Action::make('action')->emit('an_event');
    expect($action->getLivewireMountAction())->toBe("\$emit('an_event')");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireMountAction())->toBe("\$emit('an_event')");

    $action = Action::make('action')->emit('an_event', ['data']);
    expect($action->getLivewireMountAction())->toBe("\$emit('an_event', 'data')");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireMountAction())->toBe("\$emit('an_event', 'data')");
});

it('can emit an event to itself', function () {
    $action = Action::make('action')->emitSelf('an_event');
    expect($action->getLivewireMountAction())->toBe("\$emitSelf('an_event')");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireMountAction())->toBe("\$emitSelf('an_event')");

    $action = Action::make('action')->emitSelf('an_event', ['data']);
    expect($action->getLivewireMountAction())->toBe("\$emitSelf('an_event', 'data')");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireMountAction())->toBe("\$emitSelf('an_event', 'data')");
});

it('can emit an event up', function () {
    $action = Action::make('action')->emitUp('an_event');
    expect($action->getLivewireMountAction())->toBe("\$emitUp('an_event')");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireMountAction())->toBe("\$emitUp('an_event')");

    $action = Action::make('action')->emitUp('an_event', ['data']);
    expect($action->getLivewireMountAction())->toBe("\$emitUp('an_event', 'data')");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireMountAction())->toBe("\$emitUp('an_event', 'data')");
});

it('can emit an event to a component', function () {
    $action = Action::make('action')->emitTo('a_component', 'an_event');
    expect($action->getLivewireMountAction())->toBe("\$emitTo('a_component', 'an_event')");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireMountAction())->toBe("\$emitTo('a_component', 'an_event')");

    $action = Action::make('action')->emitTo('a_component', 'an_event', ['data']);
    expect($action->getLivewireMountAction())->toBe("\$emitTo('a_component', 'an_event', 'data')");

    $notification = Notification::make()->actions([$action])->send();
    expect(getLastNotificationAction()->getLivewireMountAction())->toBe("\$emitTo('a_component', 'an_event', 'data')");
});
