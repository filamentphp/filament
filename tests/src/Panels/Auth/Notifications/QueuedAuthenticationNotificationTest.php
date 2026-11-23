<?php

use Filament\Auth\MultiFactor\Email\Notifications\VerifyEmailAuthentication;
use Filament\Auth\Notifications\NoticeOfEmailChangeRequest;
use Filament\Auth\Notifications\ResetPassword;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Auth\Notifications\VerifyEmailChange;
use Filament\Tests\Fixtures\Models\ProfileEmailChangeMailUser;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Notifications\Notification;

uses(TestCase::class);

it('does not skip a queued authentication notification when only the mail recipient name changes', function (): void {
    $user = ProfileEmailChangeMailUser::factory()->create();
    $notification = new VerifyEmail;

    $notification->via($user);

    $user->name = 'Changed Name';

    expect($notification->shouldSend($user, 'mail'))->toBeTrue();

    $user->email = 'changed@example.com';

    expect($notification->shouldSend($user, 'mail'))->toBeFalse();
});

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
