<?php

use Filament\Notifications\Auth\ResetPassword;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Tests\Models\User;
use Filament\Tests\Panels\Auth\Notifications\Fixtures\CustomVerifyEmail;
use Filament\Tests\TestCase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;

uses(TestCase::class);

it('supports overriding `shouldSend()` with the Laravel signature', function () {
    $notification = new class extends VerifyEmail
    {
        public function shouldSend(object $notifiable, string $channel): bool
        {
            return parent::shouldSend($notifiable, $channel);
        }
    };

    $user = User::factory()->create();
    $notification->via($user);

    expect($notification->shouldSend($user, 'mail'))->toBeTrue();
});

it('does not skip a queued authentication notification when only the mail recipient name changes', function () {
    $notifiable = (new AnonymousNotifiable)->route('mail', ['intended@example.com' => 'Original Name']);
    $notification = new VerifyEmail;
    $notification->via($notifiable);

    $notifiable->route('mail', ['intended@example.com' => 'Changed Name']);

    expect($notification->shouldSend($notifiable, 'mail'))->toBeTrue();

    $notifiable->route('mail', ['changed@example.com' => 'Changed Name']);

    expect($notification->shouldSend($notifiable, 'mail'))->toBeFalse();
});

it('preserves the intended recipient through queue serialization', function (Notification $notification, bool $shouldChangeRecipient) {
    config()->set('mail.default', 'array');
    Queue::fake();

    $user = User::factory()->create();
    $notification->url = 'https://example.com/authenticate';
    $user->notify($notification);

    $serializedNotification = serialize(Queue::pushed(SendQueuedNotifications::class)->sole());

    if ($shouldChangeRecipient) {
        $user->updateQuietly(['email' => 'changed@example.com']);
    }

    unserialize($serializedNotification)->handle(app(ChannelManager::class));

    expect(app('mailer')->getSymfonyTransport()->messages())->toHaveCount($shouldChangeRecipient ? 0 : 1);
})->with([
    'email verification' => fn (): VerifyEmail => new VerifyEmail,
    'password reset' => fn (): ResetPassword => new ResetPassword('token'),
    'inherited notification' => fn (): CustomVerifyEmail => new CustomVerifyEmail,
])->with([
    'unchanged recipient' => false,
    'changed recipient' => true,
]);

it('skips authentication mail when the mail route is disabled', function ($initialMailRoute) {
    config()->set('mail.default', 'array');

    $notifiable = (new AnonymousNotifiable)->route('mail', $initialMailRoute);
    $notification = new VerifyEmail;
    $notification->url = 'https://example.com/verify';
    $channels = $notification->via($notifiable);

    $notifiable->route('mail', false);

    app(ChannelManager::class)->sendNow($notifiable, unserialize(serialize($notification)), $channels);

    expect(app('mailer')->getSymfonyTransport()->messages())->toHaveCount(0);
})->with([
    'disabled before queueing' => false,
    'disabled after queueing' => 'intended@example.com',
]);

it('can send a queued authentication notification without an intended mail recipient', function (Notification $notification) {
    $user = User::factory()->create();

    expect($notification->shouldSend($user, 'mail'))->toBeTrue();
})->with([
    'email verification' => fn (): VerifyEmail => new VerifyEmail,
    'password reset' => fn (): ResetPassword => new ResetPassword('token'),
]);
