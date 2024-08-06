<?php

use BladeUI\Icons\Factory;
use BladeUI\Icons\IconsManifest;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Collection;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconPosition;
use Filament\Tests\Notifications\Fixtures\CustomNotification;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
