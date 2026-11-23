<?php

use Filament\Auth\MultiFactor\Email\Notifications\VerifyEmailAuthentication;
use Filament\Auth\Notifications\NoticeOfEmailChangeRequest;
use Filament\Auth\Notifications\ResetPassword;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Auth\Notifications\VerifyEmailChange;
use Filament\Tests\Fixtures\Models\ProfileEmailChangeMailUser;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;

uses(TestCase::class);

it('supports overriding `shouldSend()` with the Laravel signature', function (): void {
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

it('does not skip a queued authentication notification when only the mail recipient name changes', function (): void {
    $user = ProfileEmailChangeMailUser::factory()->create();
    $notification = new VerifyEmail;

    $notification->via($user);

    $user->name = 'Changed Name';

    expect($notification->shouldSend($user, 'mail'))->toBeTrue();

    $user->email = 'changed@example.com';

    expect($notification->shouldSend($user, 'mail'))->toBeFalse();
});

it('preserves the intended recipient through queue serialization for inherited notifications', function (VerifyEmail $notification, bool $shouldChangeRecipient): void {
    config()->set('mail.default', 'array');
    Queue::fake();

    $user = User::factory()->create();
    $notification->url = 'https://example.com/verify';
    $user->notify($notification);

    $serializedNotification = serialize(Queue::pushed(SendQueuedNotifications::class)->sole());

    if ($shouldChangeRecipient) {
        $user->updateQuietly(['email' => 'changed@example.com']);
    }

    unserialize($serializedNotification)->handle(app(ChannelManager::class));

    expect(app('mailer')->getSymfonyTransport()->messages())->toHaveCount($shouldChangeRecipient ? 0 : 1);
})->with([
    'base notification' => fn (): VerifyEmail => new VerifyEmail,
    'inherited notification' => fn (): VerifyEmailChange => new VerifyEmailChange,
])->with([
    'unchanged recipient' => false,
    'changed recipient' => true,
]);

it('skips authentication mail when the mail route is disabled', function (string | false $initialMailRoute): void {
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

it('can send a queued authentication notification without an intended mail recipient', function (Notification $notification): void {
    $user = User::factory()->create();

    expect($notification->shouldSend($user, 'mail'))->toBeTrue();
})->with([
    'email verification' => fn (): VerifyEmail => new VerifyEmail,
    'email change verification' => fn (): VerifyEmailChange => new VerifyEmailChange,
    'password reset' => fn (): ResetPassword => new ResetPassword('token'),
    'email change notice' => fn (): NoticeOfEmailChangeRequest => new NoticeOfEmailChangeRequest(
        newEmail: 'new@example.com',
        blockVerificationUrl: 'https://example.com/block',
    ),
    'email authentication' => fn (): VerifyEmailAuthentication => new VerifyEmailAuthentication(
        code: '123456',
        codeExpiryMinutes: 4,
    ),
]);
