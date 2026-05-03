<?php

use Filament\Notifications\Livewire\DatabaseNotifications;
use Filament\Notifications\Notification;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);
});

describe('ignores IDs that are not database notification IDs', function (): void {
    it('does not affect database notifications when `notificationClosed` is dispatched', function (): void {
        Notification::make()->title('Test')->sendToDatabase($this->user);
        $flashNotification = Notification::make('password_reset_link_sent')->send();

        livewire(DatabaseNotifications::class)
            ->dispatch('notificationClosed', id: $flashNotification->getId());

        expect($this->user->notifications()->count())->toBe(1);
    });

    it('does not affect database notifications when `markedNotificationAsRead` is dispatched', function (): void {
        Notification::make()->title('Test')->sendToDatabase($this->user);
        $flashNotification = Notification::make('password_reset_link_sent')->send();

        livewire(DatabaseNotifications::class)
            ->dispatch('markedNotificationAsRead', id: $flashNotification->getId());

        expect($this->user->notifications()->first()->read_at)->toBeNull();
    });

    it('does not affect database notifications when `markedNotificationAsUnread` is dispatched', function (): void {
        Notification::make()->title('Test')->sendToDatabase($this->user);
        $this->user->notifications()->first()->markAsRead();
        $flashNotification = Notification::make('password_reset_link_sent')->send();

        livewire(DatabaseNotifications::class)
            ->dispatch('markedNotificationAsUnread', id: $flashNotification->getId());

        expect($this->user->notifications()->first()->read_at)->not->toBeNull();
    });
});

describe('acts on matching database notification IDs', function (): void {
    it('deletes the matching database notification when `notificationClosed` is dispatched', function (): void {
        Notification::make()->title('Test')->sendToDatabase($this->user);
        $notification = $this->user->notifications()->first();

        livewire(DatabaseNotifications::class)
            ->dispatch('notificationClosed', id: $notification->getKey());

        expect($this->user->notifications()->count())->toBe(0);
    });

    it('marks the matching database notification as read when `markedNotificationAsRead` is dispatched', function (): void {
        Notification::make()->title('Test')->sendToDatabase($this->user);
        $notification = $this->user->notifications()->first();

        livewire(DatabaseNotifications::class)
            ->dispatch('markedNotificationAsRead', id: $notification->getKey());

        expect($this->user->notifications()->first()->read_at)->not->toBeNull();
    });

    it('marks the matching database notification as unread when `markedNotificationAsUnread` is dispatched', function (): void {
        Notification::make()->title('Test')->sendToDatabase($this->user);
        $notification = $this->user->notifications()->first();
        $notification->markAsRead();

        livewire(DatabaseNotifications::class)
            ->dispatch('markedNotificationAsUnread', id: $notification->getKey());

        expect($this->user->notifications()->first()->read_at)->toBeNull();
    });
});
